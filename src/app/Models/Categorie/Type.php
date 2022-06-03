<?php

namespace Ipsum\Reservation\app\Models\Categorie;

use Ipsum\Core\app\Models\BaseModel;

class Type extends BaseModel
{

    protected $table = 'categorie_types';
    
    public $timestamps = false;

    /*
     * Relations
     */

    public function categories()
    {
        return $this->hasMany(Categorie::class);
    }
}
