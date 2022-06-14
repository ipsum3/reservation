<?php

namespace Ipsum\Reservation\app\Models\Tarif;

use Ipsum\Core\app\Models\BaseModel;

class Duree extends BaseModel
{
    use Tranche;


    protected $guarded = ['id'];


    public $timestamps = false;



    /*
     * Relations
     */

    public function tarifs()
    {
        return $this->hasMany(Tarif::class);
    }

}
