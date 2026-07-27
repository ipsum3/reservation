<?php

namespace Ipsum\Reservation\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Ipsum\Reservation\app\Services\SwiklyService;
use Illuminate\Http\Exceptions\HttpResponseException;

class SwiklyWebhookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        Log::channel('paiement')->info('Webhook swikly', $this->all());
    }

    public function rules(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Evènement
            |--------------------------------------------------------------------------
            */

            'event' => [
                'required',
                'string',
                Rule::in([
                    'requestSecured',
                ]),
            ],

            /*
            |--------------------------------------------------------------------------
            | Request
            |--------------------------------------------------------------------------
            */

            'request' => ['required', 'array'],

            'request.id' => ['required', 'uuid'],

            'request.customId' => ['required', 'exists:reservations,id'],

            'request.accountId' => ['nullable', 'uuid'],

            'request.createdAt' => ['nullable', 'date'],

            'request.updatedAt' => ['nullable', 'date'],

            /*
            |--------------------------------------------------------------------------
            | Dépôt
            |--------------------------------------------------------------------------
            */

            'request.deposit' => ['nullable', 'array'],

            'request.deposit.id' => ['nullable', 'uuid'],

            'request.deposit.amount' => ['nullable', 'numeric'],

            'request.deposit.securedAmount' => ['nullable', 'numeric'],

            'request.deposit.status' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'event.required' => 'L\'évènement Swikly est obligatoire.',
            'request.required' => 'Les données de la requête sont obligatoires.',
            'request.customId.required' => 'Le customId est obligatoire.',
        ];
    }

    protected function passedValidation()
    {
        /*
        |--------------------------------------------------------------------------
        | Vérification de la signature
        |--------------------------------------------------------------------------
        */

        $swiklyService = new SwiklyService();
        $signature = $this->header('Swikly-Signature');

        if (!$swiklyService->verifyWebhookSignature($this->getContent(), $signature)) {

            Log::channel('paiement')->warning('Signature Swikly invalide', [
                'signature_header' => $signature,
                'body' => $this->getContent()
            ]);

            throw new HttpResponseException(
                response()->json(['error' => 'Signature invalide'], 400)
            );
        }
    }


    protected function failedValidation(\Illuminate\Validation\Validator|\Illuminate\Contracts\Validation\Validator $validator): void
    {
        Log::channel('paiement')->critical($validator->messages());
    }
}