<?php

namespace Ipsum\Reservation\app\Models\Tarif;

use Illuminate\Support\Collection;
use Ipsum\Core\app\Models\BaseModel;
use Ipsum\Reservation\app\Classes\Carbon;

/**
 * Ipsum\Reservation\app\Models\Tarif\Saison
 *
 * @property int $id
 * @property string $nom
 * @property \Illuminate\Support\Carbon $debut_at
 * @property \Illuminate\Support\Carbon $fin_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ipsum\Reservation\app\Models\Tarif\Tarif[] $tarifs
 * @property-read int|null $tarifs_count
 * @method static \Illuminate\Database\Eloquent\Builder|Saison betweenDates(\Ipsum\Reservation\app\Classes\Carbon $debut_at, \Ipsum\Reservation\app\Classes\Carbon $fin_at)
 * @method static \Illuminate\Database\Eloquent\Builder|Saison newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Saison newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Saison query()
 * @mixin \Eloquent
 */
class Saison extends BaseModel
{

    protected $guarded = ['id'];
    
    public $timestamps = false;

    protected $dates = [
        'debut_at',
        'fin_at',
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

    public function scopeBetweenDates($query, Carbon $debut_at, Carbon $fin_at)
    {
        $debut_at->copy()->startOfDay();
        $fin_at->copy()->endOfDay();

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
     * @param Carbon $date_arrivee
     * @param Carbon $date_depart
     * @return Collection
     * @throws TarifException
     */
    public static function getByDates(Carbon $date_arrivee, Carbon $date_depart): Collection
    {
        $saisons = self::betweenDates($date_arrivee, $date_depart)->orderBy('fin_at', 'asc')->get();

        if (!$saisons->count()) {
            throw new TarifException(_('Aucune saison pour la date de départ ').$date_arrivee->format('d/m/Y').'.');
        }

        if ($saisons->last()->fin_at->lt($date_depart)) {
            throw new TarifException(_('La date limite de retour est le ').$saisons->last()->fin_at->format('d/m/Y'));
        }

        return $saisons;
    }


    /**
     * Durée de la réservation sur cette saison
     *
     * @param Carbon $date_debut
     * @param Carbon $date_fin
     * @return int
     * @desc
     */
    public function getDuree(Carbon $date_debut, Carbon $date_fin): int
    {
        // La saison ne correspond pas
        if ($date_fin->lt($this->debut_at) or $date_debut->gt($this->fin_at)) {
            return 0;
        }

        // Si c'est sur une seule saison
        if ($date_debut->gt($this->debut_at) and $date_fin->lt($this->fin_at)) {
            return $date_debut->diffInDays($date_fin->copy()->subMinutes(61)) + 1;
        }

        $date1 = $date_debut->gt($this->debut_at) ? $date_debut : $this->debut_at->copy()->subDay();
        $date2 = $date_fin->lt($this->fin_at) ? $date_fin : $this->fin_at;

        $duree = $date1->diffInDays($date2);

        // Si c'est la dernière saison
        if ($date2 == $date_fin) {
            // Comparaison des heures
            $hd = date('H', strtotime($date_debut)) * 60 + date('i', strtotime($date_debut));
            $hf = date('H', strtotime($date_fin)) * 60 + date('i', strtotime($date_fin));
            if ($hf - $hd > 60) {
                // Ajout d'un jour si plus de 60 minutes de difference
                $duree++;
            }
        }
        return $duree;
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
