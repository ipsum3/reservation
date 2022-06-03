<?php

namespace Ipsum\Reservation\app\Models\Prestation;

use Ipsum\Core\app\Models\BaseModel;

class Type extends BaseModel
{

    protected $table = 'prestation_types';

    public $timestamps = false;

    const OPTION_ID = 1;
    const ASSURANCE_ID = 2;
    const FRAIS_ID = 3;


    public function prestations()
    {
        return $this->hasMany(Prestation::class);
    }
}
