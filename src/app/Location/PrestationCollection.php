<?php


namespace Ipsum\Reservation\app\Location;


use Illuminate\Support\Collection;
use Ipsum\Reservation\app\Models\Lieu\Lieu;

class PrestationCollection extends Collection
{

    public function groupByType(): Collection
    {
        return $this->groupBy('type_id')->collect();
    }

    public function removeOptionnelle(): Collection
    {
        return $this->filter(function (Prestation $value) {
            return !$value->is_optionnelle;
        });
    }

    public function montantTotal(): ?float
    {
        return $this->sum(function (Prestation $prestation) {
            return $prestation->getTarif();
        });
    }

    public function calculer(int $nb_jours, Categorie $categorie, Lieu $lieu_debut): PrestationCollection
    {
        return $this->each(function (Prestation $prestation) use ($nb_jours, $categorie, $lieu_debut) {
            return $prestation->calculer($nb_jours, $categorie, $lieu_debut);
        });
    }

    public function hasByPrestation(Prestation $prestation): bool
    {
        return $this->contains(function (Prestation $value) use ($prestation) {
            return $value->id === $prestation->id;
        });
    }

    public function getByPrestation(Prestation $prestation): Prestation
    {
        return $this->firstWhere(function (Prestation $value) use ($prestation) {
            return $value->id === $prestation->id;
        });
    }


}