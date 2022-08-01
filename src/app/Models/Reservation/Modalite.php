<?php

namespace Ipsum\Reservation\app\Models\Reservation;

use App\Article\Translatable;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Ipsum\Admin\Concerns\Sortable;
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
 * @property int $order
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ipsum\Reservation\app\Models\Reservation\Reservation[] $reservations
 * @property-read int|null $reservations_count
 * @method static Builder|Modalite byDuree(int $duree)
 * @method static Builder|Modalite filtreSortable($objet)
 * @method static Builder|Modalite newModelQuery()
 * @method static Builder|Modalite newQuery()
 * @method static Builder|Modalite query()
 * @mixin \Eloquent
 */
class Modalite extends BaseModel
{
    use Sortable;

    protected $table = 'modalite_paiements';

    public $timestamps = false;

    protected $guarded = [];

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
        $query->where(function (Builder $query) use ($duree) {
            $query->where('duree_min', '<=', $duree)->orWhereNull('duree_min');
        });
    }




    /*
     * Functions
     */

    public function acompte(float $montant): ?float
    {
        if (!$this->has_acompte) {
            return null;
        }

        switch ($this->acompte_type) {
            case 'pourcentage' :
                $acompte = $this->acompte_value * $montant / 100;
                break;

            case 'forfait' :
                $acompte = $this->acompte_value;
                break;

            default:
                throw new Exception("Le type d'acompte n'existe pas.");
                break;
        }

        return $acompte;
    }




    /*
     * Accessors & Mutators
     */

    protected function getIsLigneAttribute(): bool
    {
        return $this->id === self::LIGNE_ID;
    }

    protected function getIsAgenceAttribute(): bool
    {
        return $this->id === self::AGENCE_ID;
    }

    protected function getHasAcompteAttribute(): bool
    {
        return $this->acompte_value !== null;
    }

}
