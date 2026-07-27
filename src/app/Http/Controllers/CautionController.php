<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Ipsum\Reservation\app\Http\Requests\SwiklyWebhookRequest;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Ipsum\Reservation\app\Models\Reservation\Type;
use Ipsum\Reservation\app\Services\SwiklyService;

class CautionController extends Controller
{
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

         Log::channel('paiement')->info("Caution sécurisée pour la réservation {$reservation->id}");


        return response()->json([
            'status' => 'success',
        ]);
    }

}