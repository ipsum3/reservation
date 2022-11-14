<?php

namespace Ipsum\Reservation\app\Models\Tarif;

use Carbon\CarbonInterface;
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



    protected static function booted()
    {
        static::deleting(function (self $duree) {
            $duree->tarifs()->delete();
            $duree->jours()->delete();
        });
    }





    /*
     * Relations
     */

    public function tarifs()
    {
        return $this->hasMany(Tarif::class);
    }

    public function jours()
    {
        return $this->hasMany(Jour::class);
    }



    /*
     * Scopes
     */


    /**
     * Forfait weekend, forfait semaine, forfait 1/2 journée, forfait noctambule, forfait kilométrique : 100km/jour, kilométrage illimité...
     */

    public function scopeDuree($query, int $nb_jours)
    {
        $query->where('min', '<=', $nb_jours)
            ->where(function ($query) use ($nb_jours) {
                $query->where('max', '>=', $nb_jours)->orWhereNull('max');
            });
    }

    public function scopeJoursHeures($query, int $nb_jours, CarbonInterface $date_debut, CarbonInterface $date_fin)
    {
        $query->where(function ($query) use ($date_debut, $date_fin) {
            $query->doesntHave('jours')->orWhereHas('jours', function ($query) use ($date_debut, $date_fin) {
                $query->where(function ($query) use ($date_debut) {
                    $query->where('value', $date_debut->dayOfWeek)->where(function ($query) use ($date_debut) {
                        $query->where('heure_debut_min', '>=', $date_debut->format('H:i'))->orWhereNull('heure_debut_min');
                    });
                })->orwhere(function ($query) use ($date_fin) {
                    $query->where('value', $date_fin->dayOfWeek)->where(function ($query) use ($date_fin) {
                        $query->where('heure_fin_max', '<=', $date_fin->format('H:i'))->orWhereNull('heure_fin_max');
                    });
                });
            });
        });
    }




    /*
     * Functions
     */

    public static function findByNbJours(int $nb_jours, CarbonInterface $date_depart, CarbonInterface $date_fin, ?string $type = null)
    {
        $duree = self::duree($nb_jours)
            ->joursHeure($date_depart, $date_fin)
            ->where('type', $type)
            ->orderBy('is_special', 'desc')
            ->first();

        if ($duree === null) {
            throw new TarifException('Aucune tranche pour '.$nb_jours.' jours.');
        }

        return $duree;
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
