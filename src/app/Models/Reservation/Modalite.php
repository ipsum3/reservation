<?php

namespace Ipsum\Reservation\app\Models\Reservation;

use App\Article\Translatable;
use Ipsum\Core\app\Models\BaseModel;

class Modalite extends BaseModel
{

    protected $table = 'modalite_paiements';

    public $timestamps = false;

    const LIGNE_ID = 1;
    const AGENCE_ID = 2;




    /*
     * 
     * Relations
     */

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }




    /*
     * Scopes
     */




    /*
     * Accessors & Mutators
     */

}
