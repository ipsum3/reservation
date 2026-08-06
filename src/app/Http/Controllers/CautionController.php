<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Ipsum\Reservation\app\Events\CautionSecuredEvent;
use Ipsum\Reservation\app\Http\Requests\Caution\GandoWebhookRequest;
use Ipsum\Reservation\app\Http\Requests\Caution\StripeWebhookRequest;
use Ipsum\Reservation\app\Http\Requests\Caution\SwiklyWebhookRequest;
use Ipsum\Reservation\app\Location\Location;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Ipsum\Reservation\app\Models\Reservation\Type;
use Ipsum\Reservation\app\Services\StripeService;

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

        $paiement = $reservation->paiements()->create(
            [
                'paiement_type_id'  => Type::CAUTION_ID,
                'paiement_moyen_id' => config('ipsum.reservation.caution_paiement_moyen_id'),
                'montant'           => $requestData['deposit']['securedAmount'] / 100,
                'transaction_ref'   => $requestData['id'],
            ]
        );

         Log::channel('caution')->info("Caution sécurisée pour la réservation {$reservation->id}");

        CautionSecuredEvent::dispatch($reservation, $paiement);

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

        $paiement = $reservation->paiements()->create([
            'paiement_type_id'  => Type::CAUTION_ID,
            'paiement_moyen_id' => config('ipsum.reservation.caution_paiement_moyen_id'),
            'montant'           => $request->getAmount(),
            'transaction_ref'   => $request->getDepositId(),
        ]);

        Log::channel('caution')->info("Caution sécurisée pour la réservation {$reservation->id}");

        CautionSecuredEvent::dispatch($reservation, $paiement);

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function stripe(StripeWebhookRequest $request)
    {
        $reservationId = $request->getReservationId();
        $reservation   = Reservation::find($reservationId);

        $montantInEuros  = $request->getAmount();
        $paymentIntentId = $request->getPaymentIntentId();

        $paiement = $reservation->paiements()->create([
            'paiement_type_id'  => Type::CAUTION_ID,
            'paiement_moyen_id' => config('ipsum.reservation.stripe_caution_moyen_id'),
            'montant'           => $montantInEuros,
            'transaction_ref'   => $paymentIntentId,
        ]);

        Log::channel('caution')->info("Caution sécurisée pour la réservation {$reservation->id}");

        CautionSecuredEvent::dispatch($reservation, $paiement);

        return response()->json([
            'status' => 'success',
        ], 200);
    }

    /*public function stripe_checkout(Reservation $reservation, string $intentId)
    {
        $stripeService = new StripeService();

        if ($reservation->paiementCaution) {
            return view('IpsumReservation::reservation.caution.already_paid', compact('reservation'));
        }

        // 2. Récupération du PaymentIntent pour avoir le client_secret
        $intent = $stripeService->getPaymentIntent($intentId);

        return view('IpsumReservation::reservation.caution.stripe', [
            'reservation'  => $reservation,
            'clientSecret' => $intent['client_secret'] ?? '',
            'stripePubKey' => config('services.stripe.key'),
        ]);
    }*/

    public function stripe_success(Reservation $reservation)
    {
        if(!$reservation->caution_url){
            abort(404);
        }
        if ($reservation->paiementCaution) {
            return redirect($reservation->caution_url);
        }

        return view('IpsumReservation::reservation.caution.stripe_success', compact('reservation'));
    }

}