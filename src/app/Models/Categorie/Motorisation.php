<?php

namespace Ipsum\Reservation\app\Models\Categorie;


use Ipsum\Core\app\Models\BaseModel;

class Motorisation extends BaseModel
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
