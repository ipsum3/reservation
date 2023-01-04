<?php

namespace Ipsum\Reservation\app\Models\Prestation;


use Ipsum\Core\app\Models\BaseModel;


/**
 * Ipsum\Reservation\app\Models\Prestation\Blocage
 *
 * @property int $id
 * @property int $prestation_id
 * @property string|null $nom
 * @property \Illuminate\Support\Carbon $debut_at
 * @property \Illuminate\Support\Carbon $fin_at
 * @property-read \Ipsum\Reservation\app\Models\Prestation\Prestation|null $prestation
 * @method static \Illuminate\Database\Eloquent\Builder|Blocage betweenDates($debut_at, $fin_at)
 * @method static \Illuminate\Database\Eloquent\Builder|Blocage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Blocage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Blocage query()
 * @mixin \Eloquent
 */
class Blocage extends BaseModel
{

    protected $table = 'prestation_blocages';

    public $timestamps = false;

    protected $guarded = ['id'];


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
