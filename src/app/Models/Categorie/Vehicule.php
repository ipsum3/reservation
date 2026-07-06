<?php

namespace Ipsum\Reservation\app\Models\Categorie;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;
use Ipsum\Core\app\Models\BaseModel;
use Ipsum\Reservation\app\Classes\Carbon;
use Ipsum\Reservation\app\Models\Dommage\Dommage;
use Ipsum\Reservation\app\Models\Inspection\Inspection;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Ipsum\Reservation\database\factories\VehiculeFactory;


/**
 * Ipsum\Reservation\app\Models\Categorie\Vehicule
 *
 * @property int $id
 * @property string $immatriculation
 * @property \Illuminate\Support\Carbon $mise_en_circualtion_at
 * @property int|null $categorie_id
 * @property string $marque_modele
 * @property \Illuminate\Support\Carbon $entree_at
 * @property \Illuminate\Support\Carbon|null $sortie_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Ipsum\Reservation\app\Models\Categorie\Categorie|null $categorie
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Dommage> $dommages
 * @property-read int|null $dommages_count
 * @property-read bool $has_no_blocage
 * @property-read bool $is_hors_parc
 * @property-read mixed $last_inspection
 * @property-read mixed $tag_meta_description
 * @property-read mixed $tag_title
 * @property-read mixed $tarif_a_partir
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Inspection> $inspections
 * @property-read int|null $inspections_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ipsum\Reservation\app\Models\Categorie\Intervention> $interventions
 * @property-read int|null $interventions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Reservation> $reservations
 * @property-read int|null $reservations_count
 * @method static Builder|Vehicule duParc(\Carbon\CarbonInterface $date_debut, \Carbon\CarbonInterface $date_fin)
 * @method static Builder|Vehicule enService(\Carbon\CarbonInterface $date_debut, \Carbon\CarbonInterface $date_fin)
 * @method static \Ipsum\Reservation\database\factories\VehiculeFactory factory($count = null, $state = [])
 * @method static Builder|Vehicule horsParc(\Carbon\CarbonInterface $date_debut, \Carbon\CarbonInterface $date_fin)
 * @method static Builder|Vehicule newModelQuery()
 * @method static Builder|Vehicule newQuery()
 * @method static Builder|Vehicule query()
 * @method static Builder|Vehicule sortie()
 * @method static Builder|Vehicule whereDoesntHaveReservationConfirmed(\Carbon\CarbonInterface $date_debut, \Carbon\CarbonInterface $date_fin)
 * @method static Builder|Vehicule withCountIntervention(\Carbon\CarbonInterface $date_debut, \Carbon\CarbonInterface $date_fin)
 * @method static Builder|Vehicule withCountReservationConfirmed(\Carbon\CarbonInterface $date_debut, \Carbon\CarbonInterface $date_fin)
 * @mixin \Eloquent
 */
class Vehicule extends BaseModel
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];


    protected $casts = [
        'entree_at' => 'datetime:Y-m-d',
        'sortie_at' => 'datetime:Y-m-d',
        'mise_en_circualtion_at' => 'datetime:Y-m-d',
    ];


    
    protected static function newFactory()
    {
        return VehiculeFactory::new();
    }


    protected static function booted()
    {
        static::deleting(function (self $categorie) {
            $categorie->interventions()->delete();
            /*$categorie->dommages()->delete(); Ne pas supprimer les dommages sinon il ne seront plus dispo sur le récap de l'éatat des lieux*/
        });
    }


    /*
     * Relations
     */

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function interventions()
    {
        return $this->hasMany(Intervention::class);
    }

    public function dommages()
    {
        return $this->hasMany(Dommage::class);
    }

    public function allDommages()
    {
        return $this->dommages()->withTrashed();
    }

    public function inspections()
    {
        // hasManyThrough(Target, Through, keyOnThrough, keyOnTarget, localKey, secondLocalKey)
        return $this->hasManyThrough(
            Inspection::class,
            Reservation::class,
            'vehicule_id',   // foreign key on reservations table...
            'reservation_id',// foreign key on inspections table...
            'id',            // local key on vehicules
            'id'             // local key on reservations
        );
    }


    /*
     * Scopes
     */

    public function scopeWhereDoesntHaveReservationConfirmed(Builder $query, CarbonInterface $date_debut, CarbonInterface $date_fin)
    {
        $query->whereDoesntHave('reservations', function (Builder $query) use ($date_debut, $date_fin) {
            $query->confirmedBetweenDates($date_debut, $date_fin);
        })->enService($date_debut, $date_fin);
    }

    public function scopeWithCountReservationConfirmed(Builder $query, CarbonInterface $date_debut, CarbonInterface $date_fin)
    {
        $query->withCount(['reservations' => function (Builder $query) use ($date_debut, $date_fin) {
            $query->confirmedBetweenDates($date_debut, $date_fin);
        }]);
    }

    public function scopeWithCountIntervention(Builder $query, CarbonInterface $date_debut, CarbonInterface $date_fin)
    {
        $query->withCount(['interventions' => function (Builder $query) use ($date_debut, $date_fin) {
            $query->betweenDates($date_debut, $date_fin);
        }]);
    }

    public function scopeDuParc(Builder $query, CarbonInterface $date_debut, CarbonInterface $date_fin)
    {
        return $query->where(function (Builder $query) use ($date_fin) {
            $query->whereNull('sortie_at')
                ->orWhere('sortie_at', '>', $date_fin->copy()->endOfDay());
        })->where(function (Builder $query) use ($date_debut) {
            $query->whereNull('entree_at')
                ->orWhere('entree_at', '<', $date_debut->copy()->endOfDay());
        });
    }

    public function scopeHorsParc(Builder $query, CarbonInterface $date_debut, CarbonInterface $date_fin)
    {
        return $query->where(function (Builder $query) use ($date_debut, $date_fin) {

            $query->where(function (Builder $query) use ($date_fin) {
                $query->whereNotNull('sortie_at')
                    ->where('sortie_at', '<=', $date_fin->copy()->endOfDay());
            })

                ->orWhere(function (Builder $query) use ($date_debut) {
                    $query->whereNotNull('entree_at')
                        ->where('entree_at', '>=', $date_debut->copy()->endOfDay());
                });
        });
    }

    public function scopeEnService(Builder $query, CarbonInterface $date_debut, CarbonInterface $date_fin)
    {
        $query->duParc($date_debut, $date_fin);

        $query->whereDoesntHave('interventions', function (Builder $query) use ($date_debut, $date_fin) {
            $query->betweenDates($date_debut, $date_fin);
        });
    }

    public function scopeSortie(Builder $query)
    {
        $query->where('sortie_at', '<=', Carbon::now()->format( 'Y-m-d' ));
    }

    /*
     * Accessors & Mutators
     */

    public function getIsHorsParcAttribute(): bool
    {
        $now = now();

        if ($this->sortie_at && $this->sortie_at <= $now) {
            return true;
        }

        if ($this->entree_at && $this->entree_at >= $now->copy()->endOfDay()) {
            return true;
        }

        return false;
    }



    /**
     * Dernière inspection (méthode pratique)
     */
    public function getLastInspectionAttribute()
    {
        return $this->inspections()->whereNotNull('agent_signature_at')->latest()->first();
    }

    public function getTagTitleAttribute()
    {
        return $this->attributes['seo_title'] == '' ? $this->titre : $this->attributes['seo_title'];
    }

    public function getTagMetaDescriptionAttribute()
    {
        return $this->attributes['seo_description'] == '' ? $this->extrait : $this->attributes['seo_description'];
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function getHasNoBlocageAttribute(): bool
    {
        if ($this->blocages_count === null) {
            throw new \Exception('A utiliser avec scopeWithCountBlocage');
        }

        return $this->blocages_count === 0;
    }


    public function getTarifAPartirAttribute()
    {
        return $this->tarifsEnCoursOuFutur->count() ? $this->tarifsEnCoursOuFutur->first()->montant : null;
    }

    public function getConflicts(Reservation $reservation = null) :Collection
    {
         if(!$reservation) {
            $reservations = $this->reservations()->confirmedBetweenDates(Carbon::now(), Carbon::now()->addYear())->get();

            $conflits = collect();
            foreach ($reservations as $resa) {
                $conflit = $this->getConflicts($resa);
                if ($conflit->count()) {
                    $conflits[] = ['reservation' => $resa, 'conflits' => $conflit];
                }
            }
            return $conflits;
        }

        $reservations = $this->reservations()
            ->confirmedBetweenDates($reservation->debut_at, $reservation->fin_at)
            ->where('id', '<>', $reservation->id)
            ->where('fin_at', '>', Carbon::now()->startOfDay())
            ->get();

        $interventions = $this->interventions()
            ->betweenDates($reservation->debut_at, $reservation->fin_at)
            ->where('fin_at', '>', Carbon::now()->startOfDay())
            ->get();

        return $reservations->merge($interventions);
    }

}
