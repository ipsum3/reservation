<?php

namespace Ipsum\Reservation\app\Models\Reservation\Casts;


class Prestation
{

    protected $attributes = [];

    public function __construct( ?array $attributes = [] ) {
        $this->attributes = is_array($attributes) ? $attributes : [];

        $this->tarif = $attributes['tarif'] ?? null;
    }

    public function __get( $name ) {
        if (method_exists($this, 'get' . lcfirst($name) . 'Attribute')) {
            return $this->{'get' . lcfirst($name) . 'Attribute'}();
        } elseif (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }

        return null;
    }

    public function __set($name, $value)
    {
        if (method_exists($this, 'set' . lcfirst($name) . 'Attribute')) {
            $this->{'set' . lcfirst($name) . 'Attribute'}($value);
        } else {
            $this->attributes[$name] = $value;
        }
    }


    public function setTarifAttribute(?float $tarif): void
    {
        $this->attributes['tarif_libelle'] = empty($this->attributes['tarif_libelle']) ? \Ipsum\Reservation\app\Location\Prestation::tarifLibelle($tarif, $this->tarification) : $this->attributes['tarif_libelle'];
    }


}
