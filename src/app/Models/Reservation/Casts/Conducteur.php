<?php

namespace Ipsum\Reservation\app\Models\Reservation\Casts;


use Carbon\Carbon;
use Ipsum\Reservation\app\Models\Prestation\Tarification;

class Conducteur
{
    use Objectable;

    public function getDateNaissanceAttribute()
    {
        return isset($this->attributes['naissance_at'])
            ? Carbon::make($this->attributes['naissance_at'])
            : null;
    }

    /**
     * Retourne la date d’obtention du permis (Carbon instance)
     */
    public function getDatePermisAttribute()
    {
        return isset($this->attributes['permis_at'])
            ? Carbon::make($this->attributes['permis_at'])
            : null;
    }

    /**
     * Alias pratiques pour compatibilité : remplace les accès directs à naissance_at / permis_at
     */
    public function __get($key)
    {
        return match ($key) {
            'naissance_at' => $this->getDateNaissanceAttribute(),
            'permis_at' => $this->getDatePermisAttribute(),
            default => $this->attributes[$key] ?? null,
        };
    }


}
