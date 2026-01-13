<?php

namespace Ipsum\Reservation\app\Models\Reservation\Casts;


use Carbon\Carbon;
use Ipsum\Reservation\app\Models\Prestation\Tarification;

class Conducteur
{
    use Objectable;

    public function getNaissance_AtAttribute()
    {
        return Carbon::make($this->attributes['naissance_at']);
    }

    public function getPermis_AtAttribute()
    {
        return Carbon::make($this->attributes['permis_at']);
    }


}
