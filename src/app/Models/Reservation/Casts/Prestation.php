<?php

namespace Ipsum\Reservation\app\Models\Reservation\Casts;


class Prestation
{
    use Objectable;


    public function setTarifAttribute(?float $tarif): void
    {
        $this->attributes['tarif_libelle'] = empty($this->attributes['tarif_libelle']) ? \Ipsum\Reservation\app\Location\Prestation::tarifLibelle($tarif, $this->tarification) : $this->attributes['tarif_libelle'];
    }

}
