<?php

namespace Ipsum\Reservation\app\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Ipsum\Reservation\app\Models\Reservation\Reservation;

class GandoService
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
                'x-api-key'     => $this->apiToken,
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ],
            'http_errors' => false,
        ]);
    }

    /**
     * Recherche ou crée un client dans Gando et renvoie son ID (clientId).
     *
     * @param Reservation $reservation
     * @return string $clientId
     * @throws Exception
     */
    public function findOrCreateClient(Reservation $reservation): string
    {

        // 1. Recherche si le client existe déjà par son e-mail pour éviter les doublons
        try {
            $searchResponse = $this->client->get("api/operator/v1/clients", [
                'query' => [
                    'email'     => $reservation->email,
                ],
            ]);

            if ($searchResponse->getStatusCode() === 200) {
                $searchData = json_decode((string) $searchResponse->getBody(), true);
                $client = $searchData['data'] ?? null;

                // Si au moins un client correspond
                if ($client) {
                    $existingId = $client['id'];

                    Log::channel('caution')->info("Client Gando existant trouvé", [
                        'reservation_id' => $reservation->id,
                        'client_id'      => $existingId,
                    ]);

                    return (string) $existingId;
                }
            }
        } catch (GuzzleException $e) {
            // En cas d'échec sur la recherche, on poursuit vers la tentative de création
            Log::channel('caution')->info("Recherche client Gando échouée, passage à la création", [
                'error' => $e->getMessage()
            ]);
        }

        // 2. Construction du payload de création
        $payload = [
            "email"       => $reservation->email,
            "firstName"   => $reservation->prenom,
            "lastName"    => $reservation->nom,
            "phone"       => $this->formatPhoneNumber($reservation->telephone),
            "clientType"  => "particulier",
            "companyName" => "",
            "siren"       => "",
            "tvaNumber"   => "",
            "country"     => $reservation->pays_nom ?? "",
            "street"      => $reservation->adresse ?? "",
            "city"        => $reservation->ville ?? "",
            "postalCode"  => $reservation->cp ?? "",
            "accountId"   => $this->accountId,
        ];

        try {
            $response = $this->client->post("api/operator/v1/clients", [
                'json' => $payload,
            ]);

            $statusCode = $response->getStatusCode();
            $body       = (string) $response->getBody();

            if ($statusCode < 200 || $statusCode >= 300) {
                Log::channel('caution')->error('Erreur lors de la création du client Gando', [
                    'reservation_id' => $reservation->id,
                    'status'         => $statusCode,
                    'body'           => $body,
                ]);

                throw new Exception("Erreur API Gando (Client HTTP {$statusCode}) : {$body}");
            }

            $responseData = json_decode($body, true);
            $clientId     = $responseData['data']['id'] ?? null;

            if (!$clientId) {
                Log::channel('caution')->error('Réponse Gando Client sans ID', ['response' => $responseData]);
                throw new Exception("Identifiant client absent de la réponse Gando.");
            }

            return (string) $clientId;

        } catch (GuzzleException $e) {
            Log::channel('caution')->error('GandoService Client GuzzleException: ' . $e->getMessage());
            throw new Exception("Erreur de communication lors de la création du client Gando : " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Génère un lien de caution Gando et met à jour la Réservation.
     *
     * Endpoint : POST /api/operator/v1/deposits
     *
     * @param Reservation $reservation
     * @param float|null $amount Montant de la caution
     * @return Reservation
     * @throws Exception
     */
    public function createDepositLink(Reservation $reservation, ?float $amount = null): Reservation
    {
        // Détermination du montant
        $depositAmount = $amount ?? $reservation->caution;
        if($depositAmount < 70) {
            Log::channel('caution')->error('Seuil minimum de la caution : 70 EUR', ['amount' => $depositAmount]);
            throw new Exception("Seuil minimum de la caution : 70 EUR");
        }

        $clientId = $this->findOrCreateClient($reservation);

        $payload = [
            'rentalContract'  => (string) $reservation->reference,
            'contractStartAt' => $reservation->debut_at->format('c'),
            'contractEndAt'   => $reservation->fin_at->format('c'),
            'clientId'        => $clientId,
            'amount'          => (float) $depositAmount,
            'metadata'        => [
                'reservation_id' => (string) $reservation->id,
            ],
            'depositUrlGeneration' => true,
        ];

        try {
            $response = $this->client->post("api/operator/v1/deposits", [
                'json' => $payload,
            ]);

            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();

            if ($statusCode < 200 || $statusCode >= 300) {
                Log::channel('caution')->error('Erreur API Gando (Guzzle)', [
                    'reservation_id' => $reservation->id,
                    'status'         => $statusCode,
                    'body'           => $body,
                ]);

                throw new Exception("Erreur API Gando (HTTP {$statusCode}) : {$body}");
            }

            $data = json_decode($body, true)['data'];
            $url = $data['depositUrl'] ??  null;
            $securingFeeCents = $data['securingFeeCents'] ?? null;

            if (!$url) {
                Log::channel('caution')->error('Réponse Gando sans URL de caution', ['response' => $data]);
                throw new Exception("URL de caution absente de la réponse Gando.");
            }

            // Récupération ou création de l'enregistrement Paiement pour la caution
            $reservation->caution_url = $url;
            $reservation->caution_frais = $securingFeeCents;
            $reservation->save();

            return $reservation;

        } catch (GuzzleException $e) {
            Log::channel('caution')->error('GandoService GuzzleException: ' . $e->getMessage());
            throw new Exception("Erreur de communication avec Gando : " . $e->getMessage(), 0, $e);
        } catch (Exception $e) {
            Log::channel('caution')->error('GandoService Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Vérifie la signature de sécurité du Webhook Gando
     *
     * @param string $rawBody Le corps brut de la requête ($request->getContent())
     * @param string|null $signature Ex: "sha256=abcdef..."
     * @param string|null $timestamp En-tête X-Gando-Timestamp ou similaire
     * @return bool
     */
    public function verifyWebhookSignature(?string $rawBody, ?string $signature, ?string $timestamp = null): bool
    {
        if (empty($rawBody) || empty($signature)) {
            return false;
        }

        $cleanSignature = str_starts_with($signature, 'sha256=')
            ? substr($signature, 7)
            : $signature;

        $signedPayload     = $timestamp ? ($timestamp . '.' . $rawBody) : $rawBody;
        $expectedSignature = hash_hmac('sha256', $signedPayload, $this->secret);

        $isValid = hash_equals($expectedSignature, $cleanSignature);

        if (!$isValid) {
            Log::channel('caution')->warning('Gando : Échec de la vérification de la signature du webhook', [
                'expected' => $expectedSignature,
                'received' => $cleanSignature,
            ]);
        }

        return $isValid;
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