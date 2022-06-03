<?php

namespace Ipsum\Reservation\app\Models\Categorie;

use Ipsum\Core\app\Models\BaseModel;

class Blocage extends BaseModel
{
    protected $table = 'categorie_blocages';

    protected $guarded = ['id'];

    public $timestamps = false;

    protected $dates = [
        'debut_at',
        'fin_at',
    ];

    /*
     * Relations
     */

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
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



}
