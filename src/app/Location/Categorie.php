<?php


namespace Ipsum\Reservation\app\Location;



use Illuminate\Support\Collection;

class Categorie extends \Ipsum\Reservation\app\Models\Categorie\Categorie
{
    public ?DevisModaliteCollection $devis = null;



    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->devis = new DevisModaliteCollection();
    }
}