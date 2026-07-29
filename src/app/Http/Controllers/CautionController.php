<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Ipsum\Reservation\app\Http\Requests\Caution\GandoWebhookRequest;
use Ipsum\Reservation\app\Http\Requests\Caution\SwiklyWebhookRequest;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Ipsum\Reservation\app\Models\Reservation\Type;

class CautionController extends Controller
{

    /**
     * Webhook Swikly
     */
    public function swikly(SwiklyWebhookRequest $request)
    {
        $data = $request->validated();
        $requestData = $data['request'] ?? null;
        $reservationId = $requestData['customId'] ?? null;

        $reservation = Reservation::findOrFail($reservationId);

        $reservation->paiements()->create(
            [
                'paiement_type_id'  => Type::CAUTION_ID,
                'paiement_moyen_id' => config('ipsum.reservation.caution_paiement_moyen_id'),
                'montant'           => $requestData['deposit']['securedAmount'] / 100,
                'transaction_ref'   => $requestData['id'],
            ]
        );

         Log::channel('caution')->info("Caution sécurisée pour la réservation {$reservation->id}");


        return response()->json([
            'status' => 'success',
        ]);
    }

    /**
     * Webhook Gando
     */
    public function gando(GandoWebhookRequest $request)
    {
        $reservation = Reservation::findOrFail($request->getReservationId());

        $reservation->paiements()->create([
            'paiement_type_id'  => Type::CAUTION_ID,
            'paiement_moyen_id' => config('ipsum.reservation.caution_paiement_moyen_id'),
            'montant'           => $request->getAmount(),
            'transaction_ref'   => $request->getDepositId(),
        ]);

        Log::channel('caution')->info("Caution sécurisée pour la réservation {$reservation->id}");

        return response()->json([
            'status' => 'success',
        ]);
    }

}