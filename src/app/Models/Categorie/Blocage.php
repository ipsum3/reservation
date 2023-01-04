<?php

namespace Ipsum\Reservation\app\Models\Categorie;

use Carbon\CarbonInterface;
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
 * @method static \Illuminate\Database\Eloquent\Builder|Blocage betweenDates(\Carbon\CarbonInterface $debut_at, \Carbon\CarbonInterface $fin_at)
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

    public function scopeBetweenDates($query, CarbonInterface $debut_at, CarbonInterface $fin_at)
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
