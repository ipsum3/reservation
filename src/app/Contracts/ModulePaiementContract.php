<?php

namespace Ipsum\Reservation\app\Contracts;


use Ipsum\Reservation\app\Models\Reservation\Reservation;

interface ModulePaiementContract
{

    public function goToFormPay(Reservation $reservation, $montant): mixed;

}