<?php


namespace Ipsum\Reservation\app\Location;


use Illuminate\Support\Collection;

class PromotionCollection extends Collection
{

    public function totalReductions(): ?float
    {
        return $this->sum(function (Promotion $promotion) {
            return $promotion->reduction;
        });
    }

    public function calculer(Devis $devis): PromotionCollection
    {
        return $this->each(function (Promotion $promotion) use ($devis) {
            return $promotion->calculerTotalReduction($devis);
        });
    }

    public function hasByPromotion(Promotion $promotion): bool
    {
        return $this->contains(function (Promotion $value) use ($promotion) {
            return $value->id === $promotion->id;
        });
    }

    public function getByPromotion(Promotion $promotion): Promotion
    {
        return $this->firstWhere(function (Promotion $value) use ($promotion) {
            return $value->id === $promotion->id;
        });
    }


    public function toArray()
    {
        return $this->map(function (Promotion $promotion) {
            return [
                'id' => $promotion->id,
                'nom' => $promotion->nom,
                'reference' => $promotion->reference,
                'reduction' => $promotion->reduction,
            ];
        })->all();

    }


}