<?php


namespace Ipsum\Reservation\app\Location;


use Illuminate\Support\Collection;

class CategorieCollection extends Collection
{

    public function whereHasNoDispo(): self
    {
        return $this->where(function (Categorie $categorie) {
            return !$categorie->is_dispo;
        });
    }
    public function whereHasDispo(): self
    {
        return $this->where(function (Categorie $categorie) {
            return $categorie->is_dispo;
        });
    }

    public function sortByTarifAndDispo(): self
    {
        $has_no_blocage = $this->whereHasDispo()->sortBy(function (Categorie $categorie) {
            return $categorie->devis->totalMin();
        });

        $has_blocage = $this->whereHasNoDispo()->sortBy('nom');

        return $has_no_blocage->concat($has_blocage);
    }


}