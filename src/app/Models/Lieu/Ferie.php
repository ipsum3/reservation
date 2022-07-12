<?php

namespace Ipsum\Reservation\app\Models\Lieu;


use Illuminate\Database\Eloquent\Builder;
use Ipsum\Core\app\Models\BaseModel;
use Carbon\Carbon;

/**
 * Ipsum\Reservation\app\Models\Lieu\Ferie
 *
 * @property int $id
 * @property int|null $lieu_id
 * @property string|null $nom
 * @property \Illuminate\Support\Carbon $jour_at
 * @property-read \Ipsum\Reservation\app\Models\Lieu\Lieu|null $lieu
 * @method static Builder|Ferie newModelQuery()
 * @method static Builder|Ferie newQuery()
 * @method static Builder|Ferie query()
 * @mixin \Eloquent
 */
class Ferie extends BaseModel
{

    public $timestamps = false;

    protected $guarded = ['id'];



    protected $dates = [
        'jour_at',
    ];


    static public function isFerie(Carbon $date, Lieu $lieu)
    {
        return self::where('jour_at', $date->format('Y-m-d'))
            ->where(function (Builder $query) use ($lieu) {
                $query->where('lieu_id', $lieu->id)->orWhereNull('lieu_id');
            })->count() ? true : false;
    }





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
