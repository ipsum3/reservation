<?php

namespace Ipsum\Reservation\app\Models\Reservation;

use Ipsum\Core\app\Models\BaseModel;

class Moyen extends BaseModel
{
    protected $table = 'paiement_moyens';

    public $timestamps = false;

    const CB_SITE_ID = 1;



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

    public function getIsSiteCbAttribute()
    {
        return $this->id == self::CB_SITE_ID;
    }
}
