<?php

namespace Ipsum\Reservation\app\Models\Reservation\Casts;


use Carbon\Carbon;

class Echeancier
{
    use Objectable;


    public function getDateAttribute()
    {
        return Carbon::make($this->attributes['date']);
    }
}
