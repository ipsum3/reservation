<?php

namespace Ipsum\Reservation\app\Models\Categorie;

use Ipsum\Admin\Concerns\Sortable;
use Ipsum\Core\app\Models\BaseModel;

class Carrosserie extends BaseModel
{
    use Sortable;

    public $timestamps = false;

    protected $guarded = ['id'];


    /*
     * Relations
     */

    public function categories()
    {
        return $this->hasMany(Categorie::class);
    }
}
