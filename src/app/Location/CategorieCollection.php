<?php


namespace Ipsum\Reservation\app\Location;


use Illuminate\Support\Collection;

class CategorieCollection extends Collection
{

    public function whereHasNoBlocage(): self
    {
        return $this->where(function (Categorie $categorie) {
            return $categorie->has_no_blocage;
        });
    }
    public function whereHasBlocage(): self
    {
        return $this->where(function (Categorie $categorie) {
            return !$categorie->has_no_blocage;
        });
    }

    public function sortByTarifAndDispo(): self
    {
        $has_no_blocage = $this->whereHasNoBlocage()->sortBy(function (Categorie $categorie) {
            return $categorie->devis->totalMin();
        });

        $has_blocage = $this->whereHasBlocage()->sortBy('nom');

        return $has_no_blocage->concat($has_blocage);
    }


}