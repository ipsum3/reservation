<?php

namespace Ipsum\Reservation\app\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Ipsum\Reservation\app\Models\Reservation\Reservation;

class StripeService
{
    protected Client $client;
    protected string $secretKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('ipsum.reservation.caution_secret');
        $this->baseUrl = config('ipsum.reservation.caution_base_url');

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers'  => [
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Accept'        => 'application/json',
            ],
            'http_errors' => false,
        ]);
    }

    /**
     * Génère le lien de caution interne Stripe et initialise le PaymentIntent
     *
     * @param Reservation $reservation
     * @param float|null $amount
     * @return Reservation
     * @throws Exception
     */
    public function createDepositLink(Reservation $reservation, ?float $amount = null): Reservation
    {
        $depositAmount = $amount ?? $reservation->caution;

        try {
            $response = $this->client->post('checkout/sessions', [
                'form_params' => [
                    'line_items' => [
                        [
                            'price_data' => [
                                'currency'     => 'eur',
                                'unit_amount'  => (int) round($depositAmount * 100),
                                'product_data' => [
                                    'name' => "Dépôt de caution - Réservation {$reservation->reference}",
                                ],
                            ],
                            'quantity' => 1,
                        ],
                    ],
                    'mode' => 'payment',
                    'payment_intent_data' => ['capture_method' => 'manual'],
                    'payment_method_options' => [
                        'card' => [
                            'request_extended_authorization' => 'if_available',
                        ],
                    ],
                    //'client_reference_id' => (string) $reservation->id,
                    'success_url'         => route('caution.checkout.stripe_success', ['reservation' => $reservation->id]),
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $body       = json_decode((string) $response->getBody(), true);
            dd($body); // TODO TESTER AVEC CLE API CLIENT - Autorisation prolongée

            if ($statusCode < 200 || $statusCode >= 300) {
                $errorMsg = $body['error']['message'] ?? "Erreur API Stripe (HTTP {$statusCode})";
                Log::channel('caution')->error('Erreur API Stripe (PaymentIntent)', [
                    'reservation_id' => $reservation->id,
                    'status'         => $statusCode,
                    'body'           => $body,
                ]);

                throw new Exception($errorMsg);
            }

            /*$intentId     = $body['id'] ?? null;
            $clientSecret = $body['client_secret'] ?? null;
            $securingFee = $body['application_fee_amount'] ?? null;

            if (!$intentId || !$clientSecret) {
                throw new Exception("Erreur d'initialisation de l'empreinte Stripe.");
            }

            $url = route('caution.checkout.stripe', [
                'reservation' => $reservation->id,
                'intentId'    => $intentId
            ]);*/

            $url = $body['url'] ?? null;

            if (!$url) {
                throw new Exception("L'URL Checkout de Stripe n'a pas pu être générée.");
            }

            // Sauvegarde uniforme sur la réservation
            $reservation->caution_url = $url; // TODO VERIFIER LA REPONSE RETOURNER ET TESTER
            //$reservation->caution_frais = $securingFee;
            $reservation->save();

            return $reservation;

        } catch (GuzzleException $e) {
            Log::channel('caution')->error('StripeCautionService GuzzleException: ' . $e->getMessage());
            throw new Exception("Erreur de communication avec Stripe : " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Récupère le PaymentIntent Stripe pour afficher le formulaire
     */
    public function getPaymentIntent(string $intentId): array
    {
        $response = $this->client->get("payment_intents/{$intentId}");
        return json_decode((string) $response->getBody(), true);
    }

    /**
     * Vérification de la signature du Webhook Stripe (HMAC-SHA256)
     */
    public function verifyWebhookSignature(string $payload, ?string $sigHeader): bool
    {
        $webhookSecret = config('ipsum.reservation.caution_secret');

        if (empty($sigHeader) || empty($webhookSecret)) {
            return false;
        }

        $items     = explode(',', $sigHeader);
        $timestamp = null;
        $signatures = [];

        foreach ($items as $item) {
            $parts = explode('=', trim($item), 2);
            if (count($parts) === 2) {
                if ($parts[0] === 't') {
                    $timestamp = $parts[1];
                } elseif ($parts[0] === 'v1') {
                    $signatures[] = $parts[1];
                }
            }
        }

        if (!$timestamp || empty($signatures)) {
            return false;
        }

        if (abs(now()->timestamp - (int) $timestamp) > 300) {
            Log::channel('caution')->warning('Webhook Stripe : Horodatage expiré');
            return false;
        }

        $signedPayload     = $timestamp . '.' . $payload;
        $expectedSignature = hash_hmac('sha256', $signedPayload, $webhookSecret);

        foreach ($signatures as $signature) {
            if (hash_equals($expectedSignature, $signature)) {
                return true;
            }
        }

        return false;
    }
}