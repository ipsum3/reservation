<?php

namespace Ipsum\Reservation\app\Models\Inspection;

use Ipsum\Core\app\Models\BaseModel;


class Type extends BaseModel
{

    protected $guarded = ['id'];
    protected $table = 'inspection_types';
    public $timestamps = false;

    const INITIAL_ID = 1;
    const FINAL_ID = 2;

    public function inspections()
    {
        return $this->hasMany(Inspection::class, 'type_id');
    }

}
