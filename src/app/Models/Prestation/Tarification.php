<?php

namespace Ipsum\Reservation\app\Models\Prestation;

use Ipsum\Core\app\Models\BaseModel;

class Tarification extends BaseModel
{
    protected $table = 'prestation_tarifications';
    protected $guarded = ['id'];
    public $timestamps = false;

    const JOUR_ID = 1;
    const FORFAIT_ID = 2;
    const AGENCE_ID = 3;
    const CARBURANT_ID = 4;


    public function prestations()
    {
        return $this->hasMany(Prestation::class, 'tarification_id');
    }

}
