<?php

namespace Ipsum\Reservation\app\Models\Tarif;

use Ipsum\Core\app\Models\BaseModel;

class Jour extends BaseModel
{

    protected $guarded = ['id'];

    const VALEURS = [0 => 'dimanche', 1 => 'lundi', 2 => 'mardi', 3 => 'mercredi', 4 => 'jeudi', 5 => 'vendredi', 6 => 'samedi'];


    public $timestamps = false;






    /*
     * Relations
     */

    public function durees()
    {
        return $this->belongsTo(Duree::class);
    }



    /*
     * Scopes
     */




    /*
     * Functions
     */




    /*
     * Accessors & Mutators
     */



}
