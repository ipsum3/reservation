<?php

namespace Ipsum\Reservation\app\Models\Categorie;

use Ipsum\Reservation\app\Classes\Carbon;
use Ipsum\Core\app\Models\BaseModel;

/**
 * Ipsum\Reservation\app\Models\Categorie\Blocage
 *
 * @property int $id
 * @property int $categorie_id
 * @property string|null $nom
 * @property \Illuminate\Support\Carbon $debut_at
 * @property \Illuminate\Support\Carbon $fin_at
 * @property-read \Ipsum\Reservation\app\Models\Categorie\Categorie|null $categorie
 * @method static \Illuminate\Database\Eloquent\Builder|Blocage betweenDates(\Ipsum\Reservation\app\Classes\Carbon $debut_at, \Ipsum\Reservation\app\Classes\Carbon $fin_at)
 * @method static \Illuminate\Database\Eloquent\Builder|Blocage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Blocage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Blocage query()
 * @mixin \Eloquent
 */
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

    public function scopeBetweenDates($query, Carbon $debut_at, Carbon $fin_at)
    {
        $debut_at->copy()->startOfDay();
        $fin_at->copy()->startOfDay();

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
