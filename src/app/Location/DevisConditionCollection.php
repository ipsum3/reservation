<?php


namespace Ipsum\Reservation\app\Location;


use Illuminate\Support\Collection;

class DevisConditionCollection extends Collection
{

    public function totalMin(): float
    {
        return $this->min(function (Devis $devis) {
            return $devis->getTotal();
        });
    }

    public function totalMax(): float
    {
        return $this->max(function (Devis $devis) {
            return $devis->getTotal();
        });
    }

    /**
     * @desc  en dessous de 1 euros on considére qu'il n'y as pas d'économie
     */
    public function hasEconomie(): bool
    {
        return $this->economie() > 1;
    }

    public function economie(): float
    {
        return $this->totalMax() - $this->totalMin();
    }

    public function hasPromotions(): bool
    {
        return $this->contains(function (Devis $devis) {
            return $devis->hasPromotions();
        });
    }
}