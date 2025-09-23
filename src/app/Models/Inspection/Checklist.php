<?php

namespace Ipsum\Reservation\app\Models\Inspection;

use Ipsum\Core\app\Models\BaseModel;


class Checklist extends BaseModel
{

    protected $guarded = ['id'];
    protected $table = 'checklist';
    public $timestamps = false;

    public function inspections()
    {
        return $this->belongsToMany(Inspection::class, 'checklist-inspections')
            ->withPivot('value');
    }

}
