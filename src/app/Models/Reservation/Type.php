<?php

namespace Ipsum\Reservation\app\Models\Reservation;

use Ipsum\Core\app\Models\BaseModel;


class Type extends BaseModel
{
    protected $table = 'paiement_types';

    public $timestamps = false;


    protected $guarded = ['id'];



    /*
     * 
     * Relations
     */
    
    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }



    /*
     * Scopes
     */




    /*
     * Accessors & Mutators
     */


}
