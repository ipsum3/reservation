<?php

namespace Ipsum\Reservation\app\Models\Reservation;

use App\Article\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Ipsum\Core\app\Models\BaseModel;

/**
 * Ipsum\Reservation\app\Models\Reservation\Modalite
 *
 * @property int $id
 * @property string $nom
 * @property string|null $description
 * @property int|null $duree_min
 * @property string|null $acompte_type
 * @property int|null $acompte_value
 * @property int|null $echeance_nombre
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ipsum\Reservation\app\Models\Reservation\Reservation[] $reservations
 * @property-read int|null $reservations_count
 * @method static Builder|Modalite byDuree(int $duree)
 * @method static Builder|Modalite newModelQuery()
 * @method static Builder|Modalite newQuery()
 * @method static Builder|Modalite query()
 * @mixin \Eloquent
 */
class Modalite extends BaseModel
{

    protected $table = 'modalite_paiements';

    public $timestamps = false;

    const LIGNE_ID = 1;
    const AGENCE_ID = 2;




    /*
     * 
     * Relations
     */

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }




    /*
     * Scopes
     */

    public function scopeByDuree(Builder $query, int $duree)
    {
        return $query->where(function (Builder $query) use ($duree) {
            $query->where('duree_min', '<=', $duree)->orWhereNull('duree_min');
        });
    }


    /*
     * Accessors & Mutators
     */

}
