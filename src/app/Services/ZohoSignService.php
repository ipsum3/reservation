<?php

namespace Ipsum\Reservation\app\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ZohoSignService
{
    protected $clientId;
    protected $clientSecret;
    protected $refreshToken;
    protected $apiDomain;

    public function __construct()
    {
        $this->clientId = config('ipsum.reservation.zoho.client_id');
        $this->clientSecret = config('ipsum.reservation.zoho.client_secret');
        $this->refreshToken = config('ipsum.reservation.zoho.refresh_token');
        $this->apiDomain = config('ipsum.reservation.zoho.api_domain');
    }

    /**
     * Récupérer / rafraîchir le token d'accès
     */
    protected function getAccessToken()
    {
        return Cache::remember('zoho_sign_access_token', 3300, function () {
            $url = "https://accounts.zoho.eu/oauth/v2/token";

            $response = Http::asForm()->post($url, [
                'refresh_token' => $this->refreshToken,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'refresh_token',
            ]);

            if ($response->failed()) {
                throw new \Exception("Impossible de récupérer l’access token Zoho Sign : " . $response->body());
            }
            if (isset($response->json()['error'])) {
                throw new \Exception("Impossible de récupérer l’access token Zoho Sign : " . $response->json()['error']);
            }

            return $response->json()['access_token'];
        });
    }

    protected function headers()
    {
        return [
            'Authorization' => 'Zoho-oauthtoken ' . $this->getAccessToken(),
            'Accept' => 'application/json',
        ];
    }

    /**
     * Envoi d’un document pour signature
     */
    public function sendForSignature(string $filePath, array $recipients, string $subject = "Signature de document", string $message = "")
    {
        $url = "https://{$this->apiDomain}/api/v1/requests";

        $payload = [
            'requests' => [
                'request_name' => $subject,
                'notes' => $message,
                'is_sequential' => false,
                'email_reminders' => false,
                'actions' => array_map(function ($recipient, $index) {
                    return [
                        'action_type' => !empty($recipient['action_type']) ? $recipient['action_type'] : 'SIGN',
                        'recipient_name' => $recipient['name'],
                        'recipient_email' => $recipient['email'],
                        'in_person_name' => $recipient['email'],
                        'verify_recipient' => false,
                    ];
                }, $recipients, array_keys($recipients)),
            ],
        ];

        $response = Http::withHeaders($this->headers())
            ->attach('file', fopen($filePath, 'r'), basename($filePath))
            ->post($url, ['data' => json_encode($payload)]);

        if ($response->failed()) {
            throw new \Exception("Erreur création du document à signer : " . $response->body());
        }

        $requestId = $response->json()['requests']['request_id'] ?? null;

        if(!$requestId){
            throw new \Exception("Erreur envoi signature : id document introuvable");
        }

        $payload['requests']['verify_recipient'] = false;

        $send_response = Http::withHeaders($this->headers())
            ->post("{$url}/{$requestId}/submit", ['data' => json_encode($payload)]);

        if ($send_response->failed()) {
            throw new \Exception("Erreur envoi signature : " . $send_response->body());
        }

        return $response->json();
    }

    /**
     * Vérifier le statut d’une requête
     */
    public function getRequestStatus(string $requestId)
    {
        $url = "https://{$this->apiDomain}/api/v1/requests/{$requestId}";

        $response = Http::withHeaders($this->headers())->get($url);

        if ($response->failed()) {
            throw new \Exception("Erreur récupération statut : " . $response->body());
        }

        return $response->json();
    }

    /**
     * Télécharger le PDF signé
     */
    public function downloadSignedDocument(string $requestId, string $destinationPath)
    {
        $url = "https://{$this->apiDomain}/api/v1/requests/{$requestId}/pdf";

        $response = Http::withHeaders($this->headers())->get($url);

        if ($response->failed()) {
            throw new \Exception("Erreur téléchargement document : " . $response->body());
        }

        file_put_contents($destinationPath, $response->body());

        return $destinationPath;
    }
}
