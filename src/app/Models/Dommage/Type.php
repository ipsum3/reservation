<?php

namespace Ipsum\Reservation\app\Models\Dommage;

use Ipsum\Core\app\Models\BaseModel;


class Type extends BaseModel
{

    protected $guarded = ['id'];
    protected $table = 'dommage_types';
    public $timestamps = false;

    public function dommages()
    {
        return $this->hasMany(Dommage::class, 'type_id');
    }

}
