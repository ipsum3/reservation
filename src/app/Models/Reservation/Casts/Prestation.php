<?php

namespace Ipsum\Reservation\app\Models\Reservation\Casts;


use Ipsum\Reservation\app\Models\Prestation\Tarification;

class Prestation
{
    use Objectable;


    public function setTarifAttribute(?float $tarif): void
    {
        if (empty($this->attributes['tarif_libelle'])) {
            $tarification_id = $this->tarification->id ?? ($this->tarification == 'agence' ? Tarification::AGENCE_ID : null);  // agence pour rétrocompatibilité
            $this->attributes['tarif_libelle'] = \Ipsum\Reservation\app\Location\Prestation::tarifLibelle($tarif, $tarification_id);
        }
        $this->attributes['tarif'] = $tarif;
    }

}
