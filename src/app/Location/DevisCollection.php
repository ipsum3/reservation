<?php


namespace Ipsum\Reservation\app\Panier;


use Illuminate\Database\Eloquent\Collection;
use Panier;

class Categories
{

    protected Collection $categories;

    protected Collection $modalites;


    public function __construct(Collection $categories, Collection $modalites)
    {
        $this->categories = $categories;
        $this->modalites = $modalites;
    }

    public function calculerTarifs(Panier $panier)
    {
        foreach ($this->categories as $categorie) {
            $this->_calculerTarifsCategorie($panier, $categorie);
        }
    }

    protected function _calculerTarifsCategorie(Panier $panier, Categorie $categorie): Categorie
    {
        $panier = $panier->clone();
        $panier->setCategorie($categorie);

        $categorie->modalites = collect();

        $categorie->has_promotions = false;
        /*$categorie->is_disponible = false;*/
        $categorie->total_min = null;

        foreach ($this->modalites as $modalite) {
            $panier->calculer(false);
            $categorie->modalites->push($panier->clone());
        }

        return $categorie;
    }

    public function orderByTarifAndDispo()
    {
        $this->categories->sortBy('total_min');
    }


    public function get()
    {
        return $this->getCategories();
    }

    /**
     * @param Collection $categories
     */
    public function setCategories(Collection $categories): void
    {
        $this->categories = $categories;
    }

    /**
     * @return Collection
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }
}