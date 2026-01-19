<?php

namespace Ipsum\Reservation\app\Models\Dommage;

use Ipsum\Core\app\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ipsum\Media\Concerns\Mediable;
use Ipsum\Reservation\app\Models\Categorie\Vehicule;
use Ipsum\Reservation\app\Models\Inspection\Inspection;


class Dommage extends BaseModel
{
    use SoftDeletes, Mediable;

    protected $guarded = ['id'];
    protected $mediable_delete = true;

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }

    public function inspection()
    {
        return $this->belongsTo(Inspection::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    public function emplacement()
    {
        return $this->belongsTo(Emplacement::class, 'emplacement_id');
    }

    public function element()
    {
        return $this->belongsTo(Element::class, 'element_id');
    }

}
