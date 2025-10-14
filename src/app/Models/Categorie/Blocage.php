<?php

namespace Ipsum\Reservation\app\Models\Categorie;

use Carbon\CarbonInterface;
use Ipsum\Core\app\Models\BaseModel;
use Ipsum\Reservation\app\Models\Lieu\Lieu;

/**
 * Ipsum\Reservation\app\Models\Categorie\Blocage
 *
 * @property int $id
 * @property int $categorie_id
 * @property int|null $lieu_id
 * @property string|null $nom
 * @property \Illuminate\Support\Carbon $debut_at
 * @property \Illuminate\Support\Carbon $fin_at
 * @property-read \Ipsum\Reservation\app\Models\Categorie\Categorie|null $categorie
 * @property-read Lieu|null $lieu
 * @method static \Illuminate\Database\Eloquent\Builder|Blocage betweenDates(\Carbon\CarbonInterface $debut_at, \Carbon\CarbonInterface $fin_at)
 * @method static \Illuminate\Database\Eloquent\Builder|Blocage betweenDatesAndLieu(\Carbon\CarbonInterface $debut_at, \Carbon\CarbonInterface $fin_at, ?\Ipsum\Reservation\app\Models\Lieu\Lieu $lieu = null)
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

    public function lieu()
    {
        return $this->belongsTo(Lieu::class);
    }



    /*
     * Scopes
     */

    public function scopeBetweenDates($query, CarbonInterface $debut_at, CarbonInterface $fin_at)
    {
        $debut_at = $debut_at->copy()->startOfDay();
        $fin_at = $fin_at->copy()->startOfDay();

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

    public function scopeBetweenDatesAndLieu($query, CarbonInterface $debut_at, CarbonInterface $fin_at, Lieu $lieu = null)
    {
        return  $query->betweenDates($debut_at, $fin_at)
                    ->where(function ($query) use ($lieu) {
                        if ($lieu) {
                            $query->where('lieu_id', $lieu->id)->orWhereNull('lieu_id');
                        }
                    });
    }



    /*
     * Accessors & Mutators
     */



}
