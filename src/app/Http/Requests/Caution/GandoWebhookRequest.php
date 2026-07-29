<?php

namespace Ipsum\Reservation\app\Http\Requests\Caution;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Ipsum\Reservation\app\Services\GandoService;

class GandoWebhookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        Log::channel('caution')->info('Webhook Gando reçu', [
            'headers' => [
                'event'     => $this->header('X-Gando-Event'),
                'timestamp' => $this->header('X-Gando-Timestamp'),
                'signature' => $this->header('X-Gando-Signature'),
            ],
            'payload' => $this->all(),
        ]);

        if (!$this->has('event') && $this->header('X-Gando-Event')) {
            $this->merge([
                'event' => $this->header('X-Gando-Event'),
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
                    'deposit.activated',
                ]),
            ],

            /*
            |--------------------------------------------------------------------------
            | Payload Data
            |--------------------------------------------------------------------------
            */
            'createdAt'                     => ['nullable', 'string'],
            'data'                          => ['required', 'array'],
            'data.id'                       => ['required', 'string'],
            'data.reference'                => ['nullable', 'string'],
            'data.rentalContract'           => ['required', 'string'],
            'data.status'                   => ['required', 'string'],
            'data.previousStatus'           => ['nullable', 'string'],
            'data.amountCents'              => ['required', 'integer'],
            'data.contractStartAt'          => ['nullable', 'string'],
            'data.contractEndAt'            => ['nullable', 'string'],
            'data.client'                   => ['nullable', 'array'],
            'data.metadata'                 => ['required', 'array'],
            'data.metadata.reservation_id'  => ['required'],
        ];
    }

    protected function passedValidation(): void
    {
        /** @var GandoService $gandoService */
        $gandoService = app(GandoService::class);

        $signature = $this->header('X-Gando-Signature');
        $timestamp = $this->header('X-Gando-Timestamp');

        if (!$signature || !$timestamp) {
            Log::channel('caution')->warning('Webhook Gando : En-têtes X-Gando manquants', [
                'signature' => $signature,
                'timestamp' => $timestamp,
            ]);

            throw new HttpResponseException(
                response()->json(['error' => 'En-têtes de sécurité manquants'], 401)
            );
        }

        if (!$gandoService->verifyWebhookSignature($this->getContent(), $signature, $timestamp)) {
            Log::channel('caution')->warning('Webhook Gando : Signature HMAC invalide', [
                'signature' => $signature,
                'timestamp' => $timestamp,
            ]);

            throw new HttpResponseException(
                response()->json(['error' => 'Signature invalide'], 401)
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Méthodes d'Accès Utilitaires (Helpers)
    |--------------------------------------------------------------------------
    */

    /**
     * Récupère l'ID interne de la réservation s'il est présent dans metadata
     */
    public function getReservationId(): ?string
    {
        return $this->input('data.metadata.reservation_id');
    }

    /**
     * Récupère l'ID de la caution Gando (ex: "cms7l2z0q02buqq01cpo05iy0")
     */
    public function getDepositId(): ?string
    {
        return $this->input('data.id');
    }

    /**
     * Récupère le montant garanti en Euros (ex: 850.00 pour 85000 centimes)
     */
    public function getAmount(): ?float
    {
        $cents = $this->input('data.amountCents');

        return $cents !== null ? ($cents / 100) : null;
    }
}