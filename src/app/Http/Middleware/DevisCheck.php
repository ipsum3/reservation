<?php

namespace Ipsum\Reservation\app\Http\Middleware;

use Closure;

class DevisCheck
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next)
    {

        if (!config('ipsum.reservation.module_de_paiement')) {
            throw new \RuntimeException('Module de paiement non disponible');
        }


        $reservation = $request->route('reservation');

        if ($reservation->is_confirmed or $reservation->debut_at->isPast()) {
            abort(403, _("Ce devis n'est plus valable. Merci de nous contacter."));
        }
        if (!$reservation->reste_a_paye) {
            abort(403, _("Le montant de ce devis est invalide. Merci de nous contacter."));
        }

        if ($reservation->devis_expiration_at and $reservation->devis_expiration_at->isPast()) {
            abort(403, _("Ce devis a expiré. Merci de nous contacter."));
        }


        $categorie = $reservation->categorie()
            ->withCountBlocage($reservation->debut_at, $reservation->fin_at)
            ->withCountVehiculeDispo($reservation->debut_at, $reservation->fin_at)
            ->whereNotIn('id', function ($query) use ($reservation) {
                $query->select('categorie_id')
                    ->from('categorie_lieux_exclus')
                    ->where('lieu_id', '=', $reservation->debut_lieu_id);
            })
            ->first();

        if (!$categorie->is_dispo) {
            abort(403, _("Aucun véhicule de disponible pour cette réservation. Merci de nous contacter."));
        }

        return $next($request);
    }
}
