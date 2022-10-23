<?php

namespace Ipsum\Reservation\app\Models\Categorie;

use Ipsum\Admin\app\Casts\AsCustomFieldsObject;
use Ipsum\Core\app\Models\BaseModel;

class InterventionType extends BaseModel
{

    protected $table = 'intervention_types';

    protected $guarded = ['id'];

    public $timestamps = false;




    /*
     * Relations
     */

    public function interventions()
    {
        return $this->hasMany(Intervention::class);
    }
}
