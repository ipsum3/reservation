<?php

namespace Ipsum\Reservation\app\Models\Tarif;

use Carbon\CarbonInterface;
use Ipsum\Core\app\Models\BaseModel;

/**
 * Ipsum\Reservation\app\Models\Tarif\Duree
 *
 * @property int $id
 * @property int $is_special
 * @property string|null $type
 * @property string|null $nom
 * @property string $tarification
 * @property int $min
 * @property int|null $max
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $is_forfait
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ipsum\Reservation\app\Models\Tarif\Jour[] $jours
 * @property-read int|null $jours_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ipsum\Reservation\app\Models\Tarif\Tarif[] $tarifs
 * @property-read int|null $tarifs_count
 * @method static \Illuminate\Database\Eloquent\Builder|Duree conditions(int $nb_jours, \Carbon\CarbonInterface $date_debut, \Carbon\CarbonInterface $date_fin)
 * @method static \Illuminate\Database\Eloquent\Builder|Duree duree(int $nb_jours)
 * @method static \Illuminate\Database\Eloquent\Builder|Duree newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Duree newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Duree query()
 * @mixin \Eloquent
 */
class Duree extends BaseModel
{
    use Tranche;


    protected $guarded = ['id'];

    const TARIFICATION = ['jour', 'forfait'];



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

    public function scopeConditions($query, int $nb_jours, CarbonInterface $date_debut, CarbonInterface $date_fin)
    {
            // Durée
        $query->duree($nb_jours)

            // Jour de la semaine et heures
            ->where(function ($query) use ($date_debut) {
                $query->whereDoesntHave('jours', function ($query) {
                    $query->where('is_debut', 1);
                })->orWhereHas('jours', function ($query) use ($date_debut) {
                    $query->where('is_debut', 1)->where('value', $date_debut->dayOfWeek)->where(function ($query) use ($date_debut) {
                        $query->where('heure', '<=', $date_debut->format('H:i'))->orWhereNull('heure');
                    });
                });
            })->where(function ($query) use ($date_fin) {
                $query->whereDoesntHave('jours', function ($query) {
                    $query->where('is_debut', 0);
                })->orWhereHas('jours', function ($query) use ($date_fin) {
                    $query->where('is_debut', 0)->where('value', $date_fin->dayOfWeek)->where(function ($query) use ($date_fin) {
                        $query->where('heure', '>=', $date_fin->format('H:i'))->orWhereNull('heure');
                    });
                });
            });
    }




    /*
     * Functions
     */

    public static function findByNbJours(int $nb_jours, CarbonInterface $date_depart, CarbonInterface $date_fin, ?string $type = null)
    {
        $duree = self::conditions($nb_jours, $date_depart, $date_fin)
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

    public function getIsForfaitAttribute()
    {
        return $this->tarification == 'forfait';
    }

}
