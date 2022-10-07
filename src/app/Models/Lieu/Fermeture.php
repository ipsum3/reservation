<?php

namespace Ipsum\Reservation\app\Models\Lieu;


use Ipsum\Core\app\Models\BaseModel;


/**
 * Ipsum\Reservation\app\Models\Lieu\Fermeture
 *
 * @property int $id
 * @property int|null $lieu_id
 * @property string|null $nom
 * @property \Illuminate\Support\Carbon $debut_at
 * @property \Illuminate\Support\Carbon|null $fin_at
 * @property-read \Ipsum\Reservation\app\Models\Lieu\Lieu|null $lieu
 * @method static \Illuminate\Database\Eloquent\Builder|Fermeture betweenDates($date)
 * @method static \Illuminate\Database\Eloquent\Builder|Fermeture newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Fermeture newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Fermeture query()
 * @mixin \Eloquent
 */
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
            $query->where(function($query) use ($date) {
                $query->where('debut_at', '<=', $date->format('Y-m-d'))->where('fin_at', '>=', $date->format('Y-m-d'))->whereNotNull('fin_at');
            })->orWhere(function($query) use ($date) {
                $query->where('debut_at', '<=', $date->format('Y-m-d'))->whereNull('fin_at');
            });
        });
    }


    /*
     * Accessors & Mutators
     */


}
