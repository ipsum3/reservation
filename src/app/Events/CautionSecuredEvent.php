<?php

namespace Ipsum\Reservation\app\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Ipsum\Reservation\app\Models\Reservation\Paiement;
use Ipsum\Reservation\app\Models\Reservation\Reservation;

class CautionSecuredEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Reservation $reservation,
        public Paiement $paiement
    ) {}
}