<?php

namespace Ipsum\Reservation\app\Models\Tarif;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;
use Ipsum\Admin\app\Casts\AsCustomFieldsObject;
use Ipsum\Core\app\Models\BaseModel;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Ipsum\Reservation\database\factories\SaisonFactory;

/**
 * Ipsum\Reservation\app\Models\Tarif\Saison
 *
 * @property int $id
 * @property string $nom
 * @property \Illuminate\Support\Carbon $debut_at
 * @property \Illuminate\Support\Carbon $fin_at
 * @property mixed|null $custom_fields
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ipsum\Reservation\app\Models\Tarif\Tarif[] $tarifs
 * @property-read int|null $tarifs_count
 * @method static \Illuminate\Database\Eloquent\Builder|Saison betweenDates(\Carbon\CarbonInterface $debut_at, \Carbon\CarbonInterface $fin_at)
 * @method static \Illuminate\Database\Eloquent\Builder|Saison newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Saison newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Saison query()
 * @mixin \Eloquent
 */
class Saison extends BaseModel
{
    use HasFactory;

    protected $guarded = ['id'];
    
    public $timestamps = false;

    protected static function newFactory()
    {
        return SaisonFactory::new();
    }


    protected $casts = [
        'custom_fields' => AsCustomFieldsObject::class,
        'debut_at' => 'datetime:Y-m-d',
        'fin_at' => 'datetime:Y-m-d',
    ];


    protected static function booted()
    {
        static::deleting(function (self $saison) {
            $saison->tarifs()->delete();
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







    /*
     * Functions
     */

    /**
     * Pour dupliquer les grilles en admin
     */
    public function replicateWithTarifs(): self
    {
        $saison_clone = $this->replicate();
        $saison_clone->push();
        foreach($this->tarifs as $tarif) {
            $saison_clone->tarifs()->create($tarif->toArray());
        }
        return $saison_clone;
    }

    /**
     * @param CarbonInterface $date_arrivee
     * @param CarbonInterface $date_depart
     * @return Collection
     * @throws TarifException
     */
    public static function getByDates(CarbonInterface $date_arrivee, CarbonInterface $date_depart): Collection
    {
        $date_arrivee = $date_arrivee->copy()->startOfDay();
        $date_depart = $date_depart->copy()->startOfDay();

        $saisons = self::betweenDates($date_arrivee, $date_depart)->orderBy('fin_at', 'asc')->get();

        if (!$saisons->count()) {
            throw new TarifException(_('Aucune saison pour la date de départ ').$date_arrivee->format('d/m/Y').'.');
        }

        // TODO vérifier qu'il existe une saison pour toutes les dates de la résa (multi saison)
        // Peut posser un problème dans le cas d'une saison manquante s'il y a plus de 2 saisons
        if ($saisons->last()->fin_at->lt($date_depart)) {
            throw new TarifException(_('La date limite de retour est le ').$saisons->last()->fin_at->format('d/m/Y'));
        }

        return $saisons;
    }


    /**
     * Durée de la réservation sur cette saison
     *
     * @param CarbonInterface $date_debut
     * @param CarbonInterface $date_fin
     * @return int
     * @desc
     * @throws \Exception
     */
    public function getDuree(CarbonInterface $date_debut, CarbonInterface $date_fin): int
    {
        // La saison ne correspond pas
        if ($date_fin->lt($this->debut_at) or $date_debut->gt($this->fin_at->endOfDay())) {
            return 0;
        }

        // Si c'est sur une seule saison
        if ($date_debut->gte($this->debut_at) and $date_fin->lte($this->fin_at->endOfDay())) {
            return Reservation::calculDuree($date_debut, $date_fin);
        }

        // Saison intermédiaire
        if ($date_fin->gt($this->fin_at) and $date_debut->lt($this->debut_at)) {
            return $this->debut_at->diffInDays($this->fin_at->addDay());
        }

        // Première saison
        if (!$date_fin->lt($this->fin_at)) {
            return $date_debut->diffInDays($this->fin_at->endOfDay()) + 1;
        }

        // Dernière saison
        if (!$date_debut->gt($this->debut_at)) {

            $date1 = $this->debut_at->addHours($date_debut->hour)->addMinutes($date_debut->minute);

            if ($date1->copy()->addMinutes(60)->gte($date_fin)) {
                return 0;
            }

            return Reservation::calculDuree($date1, $date_fin);
        }

        throw new \Exception('Saison::getDuree : aucune durée prise en compte');
    }

   /* public static function check()
    {
        $messages = null;


        $saisons = self::orderBy('debut_at', 'asc')->get();

        if ($saisons->first()->debut_at->gt(Carbon::now())) {
            $messages[] = "Aucune saison pour la date d'aujourd'hui";
        }

        foreach ($saisons as $key => $saison) {
            if (isset($saisons[$key + 1]) and $saison->fin_at->startOfday()->addDay()->ne($saisons[$key + 1]->debut_at)) {
                $messages[] = "Il existe des dates sans saisons.";
            }
        }

        $chevauchements = \DB::select(\DB::raw('
            SELECT t.id,
                   t.debut_at,
                   t.fin_at
            FROM   saison AS t,
                   saison AS t2
            WHERE  t.id <> t2.id
                   AND
                   t.debut_at <= t2.fin_at
                   AND
                   t.fin_at >= t2.debut_at
            GROUP BY t.id,
                     t.debut_at,
                     t.fin_at
        '));

        if (!empty($chevauchements)) {
            $messages[] = "Des chevauchements de saisons existe";
        }

        if ($messages) {
            throw new TarifException(implode($messages, ' ; '));
        }

    }*/
}
