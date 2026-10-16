<?php

namespace Ipsum\Reservation\app\Contracts\Nones;


use Ipsum\Reservation\app\Contracts\ModulePaiementContract;
use Ipsum\Reservation\app\Models\Reservation\Reservation;

class ModulePaiementNoneProvider implements ModulePaiementContract
{


    public function goToFormPay(Reservation $reservation, $montant): mixed
    {
        abort(404, 'Aucun module de paiement configuré');
    }
}