<?php

namespace Ipsum\Reservation\app\Models\Reservation;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Ipsum\Admin\Concerns\Sortable;
use Ipsum\Core\app\Models\BaseModel;
use DB;
use Ipsum\Reservation\app\Classes\Carbon;
use Ipsum\Reservation\app\Models\Promotion\Promotion;

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
 * @property-read \Illuminate\Database\Eloquent\Collection|Promotion[] $promotions
 * @property-read int|null $promotions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ipsum\Reservation\app\Models\Reservation\Reservation[] $reservations
 * @property-read int|null $reservations_count
 * @method static Builder|Modalite byDuree(int $duree)
 * @method static Builder|Modalite delaiValide(\Ipsum\Reservation\app\Classes\Carbon $debut)
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

    public function promotions()
    {
        return $this->hasMany(Promotion::class);
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

    /**
     * @desc Le paiement en x fois doit être disponible uniquement si la date du début de la réservation est postérieure à la date de la dernière échéance + 2 jours.
     */
    public function scopeDelaiValide(Builder $query, Carbon $debut)
    {
        $query->where(function (Builder $query) use ($debut) {
            $query->whereRaw("'".$debut->format('Y-m-d H:i:s')."' > NOW() + INTERVAL echeance_nombre - 1 MONTH + INTERVAL 2 DAY")->orWhereNull('echeance_nombre');
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


    /**
     * @param float $montant
     * @return array|null
     */
    public function echeancier(float $montant): ?array
    {
        if (!$this->has_echeance) {
            return null;
        }

        $echeances = [];
        $date = Carbon::now();
        $montant_echeance = floor($montant / $this->echeance_nombre);
        $montant_echeance_centimes = round((($montant / $this->echeance_nombre) - $montant_echeance) * $this->echeance_nombre, 2, PHP_ROUND_HALF_DOWN);
        for ($i = 0; $i < $this->echeance_nombre; $i++) {
            $echeances[] = [
                'date' => $date->clone(),
                'montant' => $montant_echeance + ($i == 0 ? $montant_echeance_centimes : 0),
            ];
            $date->addMonth();
        }

        return $echeances;
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

    protected function getHasEcheanceAttribute(): bool
    {
        return $this->echeance_nombre !== null;
    }


}
