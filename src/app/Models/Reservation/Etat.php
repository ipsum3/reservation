<?php

namespace Ipsum\Reservation\app\Models\Reservation;

use Ipsum\Core\app\Models\BaseModel;

class Etat extends BaseModel
{

    protected $table = 'reservation_etats';

    public $timestamps = false;

    const NON_VALIDEE_ID = 1;
    const VALIDEE_ID = 2;
    const ANNULEE_ID = 3;


    /*
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
