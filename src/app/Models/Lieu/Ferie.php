<?php

namespace Ipsum\Reservation\app\Models\Lieu;


use App\BaseModel;
use Carbon\Carbon;

class Ferie extends BaseModel
{

    public $timestamps = false;

    //protected $fillable = array('zone_id', 'nom', 'jour_at');

    protected $nullable = ['zone_id', 'nom'];


    protected $dates = [
        'jour_at',
    ];





    /*
     * Relations
     */





    /*
     * Scopes
     */




    /*
     * Accessors & Mutators
     */

    /*public function setJourAtAttribute($value)
    {
        $this->attributes['jour_at'] = Carbon::createFromFormat('d/m/Y', $value);
    }*/


}
