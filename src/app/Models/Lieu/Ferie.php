<?php

namespace Ipsum\Reservation\app\Models\Lieu;


use Ipsum\Core\app\Models\BaseModel;
use Carbon\Carbon;

class Ferie extends BaseModel
{

    public $timestamps = false;

    protected $guarded = ['id'];



    protected $dates = [
        'jour_at',
    ];





    /*
     * Relations
     */

    public function lieu()
    {
        return $this->belongsTo(Lieu::class);
    }


    /*
     * Scopes
     */




    /*
     * Accessors & Mutators
     */
    


}
