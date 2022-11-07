<?php

namespace Ipsum\Reservation\app\Models\Tarif;

use Ipsum\Core\app\Models\BaseModel;

/**
 * Ipsum\Reservation\app\Models\Tarif\Duree
 *
 * @property int $id
 * @property int $min
 * @property int|null $max
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ipsum\Reservation\app\Models\Tarif\Tarif[] $tarifs
 * @property-read int|null $tarifs_count
 * @method static \Illuminate\Database\Eloquent\Builder|Duree newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Duree newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Duree query()
 * @mixin \Eloquent
 */
class Duree extends BaseModel
{
    use Tranche;


    protected $guarded = ['id'];

    const JOURS = [0 => 'dimanche', 1 => 'lundi', 2 => 'mardi', 3 => 'mercredi', 4 => 'jeudi', 5 => 'vendredi', 6 => 'samedi'];


    public $timestamps = false;


    protected static function booted()
    {
        static::deleting(function (self $duree) {
            $duree->tarifs()->delete();
        });
    }


    /*
     * Relations
     */

    public function tarifs()
    {
        return $this->hasMany(Tarif::class);
    }




    /*
     * Accessors & Mutators
     */

    public function getMinHeureAttribute()
    {
        return substr($this->attributes['min_heure'], 0, -3);
    }

    public function getMaxHeureAttribute()
    {
        return substr($this->attributes['max_heure'], 0, -3);
    }

}
