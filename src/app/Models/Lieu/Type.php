<?php

namespace Ipsum\Reservation\app\Models\Lieu;

use Ipsum\Core\app\Models\BaseModel;

class Type extends BaseModel
{

    protected $table = 'lieu_types';

    public $timestamps = false;

    const AGENCE_ID = 1;
    const DEPOT_ID = 2;

    
    /*
     * Relations
     */

    public function lieux()
    {
        return $this->hasMany(Lieu::class);
    }


    /*
     * Scopes
     */




    /*
     * Accessors & Mutators
     */

}
