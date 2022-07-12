<?php


namespace Ipsum\Reservation\app\Panier;


use Illuminate\Database\Eloquent\Collection;

class Categorie extends \Ipsum\Reservation\app\Models\Categorie\Categorie
{

    protected Collection $modalites;
    protected ?float $total_min = null;


    public function __construct(array $attributes = [])
    {
        $this->modalites = collect();
        parent::__construct($attributes);
    }

}