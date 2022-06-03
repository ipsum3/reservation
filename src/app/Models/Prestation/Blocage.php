<?php

namespace Ipsum\Reservation\app\Models\Prestation;


use Ipsum\Core\app\Models\BaseModel;
use Carbon\Carbon;

class Blocage extends BaseModel
{

    protected $table = 'prestation_blocages';

    public $timestamps = false;


    //protected $fillable = array('option_id', 'zone_id', 'nom', 'debut_at', 'fin_at');

    protected $dates = [
        'debut_at', 
        'fin_at'
    ];


    /*
     * Relations
     */

    public function prestation()
    {
        return $this->belongsTo(Prestation::class);
    }




    /*
     * Scopes
     */

    public function scopeBetweenDates($query, $debut_at, $fin_at)
    {
        $debut_at->startOfDay();
        $fin_at->startOfDay();

        return $query->where(function ($query) use ($debut_at, $fin_at) {
            return $query->where(function ($query) use ($debut_at, $fin_at) {
                $query->where('debut_at', '>=', $debut_at)->where('debut_at', '<=', $fin_at);
            })->orWhere(function ($query) use ($debut_at, $fin_at) {
                $query->where('fin_at', '>=', $debut_at)->where('fin_at', '<=', $fin_at);
            })->orWhere(function ($query) use ($debut_at, $fin_at) {
                $query->where('debut_at', '<=', $debut_at)->where('fin_at', '>=', $fin_at);
            });
        });
    }




    /*
     * Accessors & Mutators
     */

    /*public function setDebutAtAttribute($value)
    {
        $this->attributes['debut_at'] = Carbon::createFromFormat('d/m/Y', $value);
    }
    public function setFinAtAttribute($value)
    {
        $this->attributes['fin_at'] = Carbon::createFromFormat('d/m/Y', $value);
    }*/
}
