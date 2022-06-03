<?php

namespace Ipsum\Reservation\app\Models\Reservation;

use App\Article\Translatable;
use Ipsum\Core\app\Models\BaseModel;

class Pays extends BaseModel
{

    protected $table = 'pays';

    public $timestamps = false;


    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
