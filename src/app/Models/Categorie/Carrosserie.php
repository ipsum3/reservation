<?php

namespace Ipsum\Reservation\app\Models\Categorie;

use Ipsum\Core\app\Models\BaseModel;

class Carrosserie extends BaseModel
{

    public $timestamps = false;

    /*
     * Relations
     */

    public function categories()
    {
        return $this->hasMany(Categorie::class);
    }
}
