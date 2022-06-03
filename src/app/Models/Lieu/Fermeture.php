<?php

namespace Ipsum\Reservation\app\Models\Lieu;


use Ipsum\Core\app\Models\BaseModel;
use Carbon\Carbon;

class Fermeture extends BaseModel
{

    public $timestamps = false;

    protected $guarded = ['id'];


    protected $dates = [
        'debut_at',
        'fin_at',
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

    public function scopeBetweenDates($query, $date)
    {
        return $query->where(function($query) use ($date) {
            $query->where('debut_at', '<=', $date->format('Y-m-d'))->where('fin_at', '>=', $date->format('Y-m-d'))->whereNotNull('fin_at');
        })->orWhere(function($query) use ($date) {
            $query->where('debut_at', $date->format('Y-m-d'))->whereNull('fin_at');
        });
    }


    /*
     * Accessors & Mutators
     */


}
