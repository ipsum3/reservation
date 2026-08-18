<?php

namespace Ipsum\Reservation\app\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Ipsum\Reservation\app\Models\Reservation\Reservation;

class SwiklyService
{
    protected string $accountId;
    protected string $apiToken;
    protected string $secret;
    protected string $baseUrl;
    protected Client $client;

    public function __construct()
    {
        $this->apiToken = config('ipsum.reservation.caution_token');
        $this->accountId = config('ipsum.reservation.caution_account_id');
        $this->baseUrl = config('ipsum.reservation.caution_base_url');
        $this->secret = config('ipsum.reservation.caution_secret');

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers'  => [
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ],
            'http_errors' => false,
        ]);
    }

    /**
     * Génère un lien de caution Swikly et enregistre/met à jour le Paiement associé.
     *
     * @param Reservation $reservation
     * @param float|null $amount Montant de la caution
     * @return Reservation
     * @throws Exception
     */
    public function createDepositLink(Reservation $reservation, ?float $amount = null): Reservation
    {
        $endpoint = "{$this->baseUrl}/v1/accounts/{$this->accountId}/requests";

        // URL dynamique de callback pour le webhook IPN
        $callbackUrl = route('caution.webhooks.swikly');

        // Détermination du montant
        $depositAmount = $amount ?? $reservation->caution;

        $payload = [
            'description'                 => "Caution pour la réservation ref {$reservation->reference}",
            'language'                    => 'fr',
            'customId'                   =>  (string) $reservation->id,
            'firstName'                   => $reservation->prenom,
            'lastName'                    => $reservation->nom,
            'email'                       => $reservation->email,
            'phoneNumber'                 => $this->formatPhoneNumber($reservation->telephone),
            'skipToPaymentPageIfPossible' => true,
            'sendEmail'                   => false,
            'sendSms'                     => false,
            'deposit'                     => [
                'startDate' => $reservation->debut_at->format('Y-m-d'),
                'endDate'   => $reservation->fin_at->format('Y-m-d'),
                'amount'    => (int) round($depositAmount * 100),
            ],
            'callbacks' => [
                'requestSecured' => $callbackUrl,
            ],
        ];

        try {
            $response = $this->client->post($endpoint, [
                'json' => $payload,
            ]);

            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();

            if ($statusCode < 200 || $statusCode >= 300) {
                Log::channel('caution')->error('Erreur API Swikly (Guzzle)', [
                    'reservation_id' => $reservation->id,
                    'status'         => $statusCode,
                    'body'           => $body,
                ]);

                throw new Exception("Erreur API Swikly (HTTP {$statusCode}) : {$body}");
            }

            $data = json_decode($body, true)['request'];
            $url = $data['link'] ?? null;

            if (!$url) {
                throw new Exception("URL de caution absente de la réponse Swikly.");
            }

            // Récupération ou création de l'enregistrement Paiement pour la caution
            $reservation->caution_url = $url;
            $reservation->save();

            return $reservation;

        } catch (GuzzleException $e) {
            Log::channel('caution')->error('SwiklyService GuzzleException: ' . $e->getMessage());
            throw new Exception("Erreur de communication avec Swikly : " . $e->getMessage(), 0, $e);
        } catch (Exception $e) {
            Log::channel('caution')->error('SwiklyService Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Vérifie la signature de sécurité du Webhook Swikly
     */
    public function verifyWebhookSignature(string $body, string $signatureHeader): bool
    {
        $headerValues = [];
        foreach (explode(',', $signatureHeader) as $part) {
            $keyValue = explode('=', trim($part), 2);
            if (count($keyValue) === 2) {
                $headerValues[$keyValue[0]] = trim($keyValue[1]);
            }
        }

        // 1. Vérifie si les clés requises sont présentes dans le header
        if (!isset($headerValues['t'], $headerValues['sha256'])) {
            Log::channel('caution')->warning('Swikly : Clés "t" ou "sha256" manquantes dans l\'en-tête de signature.');
            return false;
        }

        $signatureTimestamp = $headerValues['t'];
        $signatureHash      = $headerValues['sha256']; // <--- L'empreinte reçue

        // 2. Anti-replay attack (optionnel mais recommandé : vérifie que la requête a < 10 min)
        if (abs(time() - (int)$signatureTimestamp) > 600) {
            Log::channel('caution')->warning('Swikly : Signature expirée (timestamp trop ancien).');
            return false;
        }

        // 3. Calcul du HMAC
        $toBeSignedPayload = sprintf("%s.%s", $signatureTimestamp, $body);
        $computedSignature = hash_hmac('sha256', $toBeSignedPayload, $this->secret);

        Log::channel('caution')->info('Vérification signature Swikly', [
            'calculee' => $computedSignature,
            'recue'    => $signatureHash,
        ]);

        // 4. Comparaison sécurisée entre le calcul et le hash extrait
        return hash_equals($computedSignature, $signatureHash);
    }

    /**
     * Formate un numéro de téléphone au format international (E.164)
     */
    public function formatPhoneNumber(?string $phone, string $defaultCountryCode = '+33'): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // 1. Supprime tous les caractères non numériques à l'exception du "+"
        $cleaned = preg_replace('/[^\d+]/', '', $phone);

        if (empty($cleaned)) {
            return null;
        }

        // 2. Si le numéro commence déjà par "+", on s'assure juste du bon format
        if (str_starts_with($cleaned, '+')) {
            return $cleaned;
        }

        // 3. Gestion du préfixe international "00" (ex: 0033612345678 -> +33612345678)
        if (str_starts_with($cleaned, '00')) {
            return '+' . substr($cleaned, 2);
        }

        // 4. Si le numéro commence par "0" (ex: 0612345678 ou 0696000000)
        if (str_starts_with($cleaned, '0')) {
            return $defaultCountryCode . substr($cleaned, 1);
        }

        // 5. Par défaut, si aucun "0" ni "+", on ajoute le code pays par défaut
        return $defaultCountryCode . $cleaned;
    }
}