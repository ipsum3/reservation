<?php

namespace Ipsum\Reservation\app\Models\Dommage;
use Ipsum\Core\app\Models\BaseModel;
use Ipsum\Reservation\app\Models\Dommage\Dommage;


class Emplacement extends BaseModel
{

    protected $guarded = ['id'];
    protected $table = 'dommage_emplacements';
    public $timestamps = false;

    public function dommages()
    {
        return $this->hasMany(Dommage::class, 'element_id');
    }

}
