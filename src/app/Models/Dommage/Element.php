<?php

namespace Ipsum\Reservation\app\Models\Dommage;
use Ipsum\Core\app\Models\BaseModel;


class Element extends BaseModel
{

    protected $guarded = ['id'];
    protected $table = 'dommage_elements';
    public $timestamps = false;

    public function dommages()
    {
        return $this->hasMany(Dommage::class, 'element_id');
    }

}
