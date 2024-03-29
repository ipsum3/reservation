<?php


namespace Ipsum\Reservation\app\Location;



class Categorie extends \Ipsum\Reservation\app\Models\Categorie\Categorie
{
    public ?DevisConditionCollection $devis = null;



    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->devis = new DevisConditionCollection();
    }

    /**
     * @desc Permet de faire fonctioner la relation morph avec cette class extends
     */
    public function getMorphClass()
    {
        return parent::class;
    }
}