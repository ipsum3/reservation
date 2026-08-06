<?php

namespace Ipsum\Reservation\app\Http\Requests\Caution;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Ipsum\Reservation\app\Services\StripeService;

class StripeWebhookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        Log::channel('caution')->info('Webhook Stripe reçu', [
            'headers' => [
                'signature' => $this->header('Stripe-Signature'),
            ],
            'event_type' => $this->input('type'),
        ]);

        // Merge de l'évènement au cas où
        if (!$this->has('event') && $this->input('type')) {
            $this->merge([
                'event' => $this->input('type'),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            /*
            |--------------------------------------------------------------------------
            | Événement
            |--------------------------------------------------------------------------
            */
            'event' => [
                'required',
                'string',
                Rule::in([
                    'payment_intent.amount_capturable_updated', // Empreinte bancaire validée (capture_method = manual)
                    'payment_intent.succeeded',                 // Paiement direct ou capturé avec succès
                ]),
            ],

            /*
            |--------------------------------------------------------------------------
            | Payload Data
            |--------------------------------------------------------------------------
            */
            'id'                                                  => ['nullable', 'string'],
            'data'                                                => ['required', 'array'],
            'data.object'                                         => ['required', 'array'],
            'data.object.id'                                      => ['required', 'string'],
            'data.object.status'                                  => ['nullable', 'string'],
            'data.object.amount_capturable'                      => ['nullable', 'integer'],
            'data.object.amount'                                 => ['nullable', 'integer'],
            'data.object.metadata'                                => ['nullable', 'array'],
            'data.object.metadata.reservation_id'                 => ['nullable'],
        ];
    }

    protected function passedValidation(): void
    {
        /** @var StripeService $stripeService */
        $stripeService = app(StripeService::class);

        $sigHeader = $this->header('Stripe-Signature');
        $rawBody   = $this->getContent();

        // 1. Contrôle de présence de l'en-tête de signature
        if (!$sigHeader) {
            Log::channel('caution')->warning('Webhook Stripe : En-tête Stripe-Signature manquant');

            throw new HttpResponseException(
                response()->json(['error' => 'En-tête de sécurité manquant'], 401)
            );
        }

        // 2. Vérification HMAC-SHA256 & Anti-Replay Attack via le service
        if (!$stripeService->verifyWebhookSignature($rawBody, $sigHeader)) {
            Log::channel('caution')->warning('Webhook Stripe : Échec de la vérification de la signature ou horodatage expiré');

            throw new HttpResponseException(
                response()->json(['error' => 'Signature invalide ou requête expirée'], 401)
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Méthodes d'Accès Utilitaires (Helpers)
    |--------------------------------------------------------------------------
    */

    /**
     * Récupère l'ID interne de la réservation stocké dans les métadonnées Stripe
     */
    public function getReservationId(): ?string
    {
        return $this->input('data.object.metadata.reservation_id');
    }

    /**
     * Récupère l'ID du PaymentIntent Stripe (ex: "pi_3MtwBwLkdIwHu7ix28a3tCyD")
     */
    public function getPaymentIntentId(): ?string
    {
        return $this->input('data.object.id');
    }

    /**
     * Récupère le montant garanti en Euros (converti depuis les centimes)
     */
    public function getAmount(): ?float
    {
        // En mode 'manual', la somme bloquée est sous 'amount_capturable'
        $cents = $this->input('data.object.amount_capturable')
            ?? $this->input('data.object.amount');

        return $cents !== null ? ($cents / 100) : null;
    }

    /**
     * Récupère le statut du PaymentIntent
     */
    public function getStatus(): ?string
    {
        return $this->input('data.object.status');
    }
}