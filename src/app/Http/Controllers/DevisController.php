<?php

namespace Ipsum\Reservation\app\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Sherlocks\Sherlocks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use PixellWeb\Monetico\app\PaymentRequest;
use PixellWeb\Monetico\Kit\Request\OrderContext;
use PixellWeb\Monetico\Kit\Request\OrderContextBilling;

class DevisController extends Controller
{

    public function show(Reservation $reservation)
    {
        $montant =  $reservation->condition->has_acompte ? $reservation->acompte : $reservation->total;

        $pdf = view(config('ipsum.reservation.devis.view'), compact('reservation'))->render();

        return view('IpsumReservation::reservation.devis', compact('reservation', 'pdf', 'montant'));

    }

    public function redirectBanque(Request $request, Reservation $reservation)
    {
        $request->session()->put('reservation_devis', $reservation->id);

        $montant =  $reservation->condition->has_acompte ? $reservation->acompte : $reservation->total;

        switch (config('ipsum.reservation.module_de_paiement')) {
            case 'monetico':
                $billing = new OrderContextBilling($reservation->adresse ?? 'rue des champs', $reservation->ville ?? 'Pointe-à-Pitre', $reservation->cp ?? '97100', $reservation->pays->alpha2 ?? 'GP');
                $context = new OrderContext($billing);
                $payment_request = new PaymentRequest($reservation->reference, $montant, $context, $reservation->email);
                $payment_request->setUrlRetourOk(URL::route('devis.confirmation', $reservation));
                $payment_request->setUrlRetourErreur(URL::signedRoute('devis.show', $reservation));

                return redirect()->away($payment_request->link());
                break;

            case 'systempay':
                config()->set([
                    'systempay.url_annule' => URL::signedRoute('devis.show', $reservation),
                    'systempay.url_effectue' => URL::route('devis.confirmation', $reservation),
                    'systempay.url_attente' => URL::route('devis.confirmation', $reservation),
                    'systempay.url_refuse' => URL::signedRoute('devis.show', $reservation),
                ]);

                $systempay = new \PixellWeb\Systempay\app\PaymentRequest(
                    $reservation->reference,
                    $montant,
                    $reservation->email,
                );

                return redirect()->away($systempay->link());
                break;

            case 'sherlocksSite':

                return Sherlocks::formPaiement($reservation, URL::route('devis.confirmation', $reservation));

                break;
        }

    }

    public function confirmation(Reservation $reservation)
    {
        // Impossible d'utiliser une route signée à cause de sherlocks qui ne passe aucun query
        if (session('reservation_devis') !== $reservation->id) {
            abort(403, _("Cette page n'est plus valide."));
        }

        if (!$reservation->is_confirmed) {
            abort(403, _('Votre réservation est en cours de traitement. Pour visualiser votre réservation, vous pouvez réactualiser la page dans quelques instants.'));
        }

        return view(config('ipsum.reservation.confirmation.view'), compact('reservation'));
    }
}
