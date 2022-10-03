<?php

namespace Ipsum\Reservation\app\Models\Reservation;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Ipsum\Admin\app\Casts\AsCustomFieldsObject;
use Ipsum\Admin\app\Models\Admin;
use Ipsum\Core\app\Models\BaseModel;
use Ipsum\Reservation\app\Classes\Carbon;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Ipsum\Reservation\app\Models\Categorie\Vehicule;
use Ipsum\Reservation\app\Models\Lieu\Lieu;
use Ipsum\Reservation\app\Models\Reservation\Casts\EcheancierCollection;
use Ipsum\Reservation\app\Models\Reservation\Casts\PrestationCollection;
use Ipsum\Reservation\app\Models\Reservation\Casts\PromotionCollection;
use Ipsum\Reservation\database\factories\ReservationFactory;


/**
 * Ipsum\Reservation\app\Models\Reservation\Reservation
 *
 * @property int $id
 * @property string|null $reference
 * @property string|null $contrat
 * @property int $etat_id
 * @property int $condition_paiement_id
 * @property int|null $client_id
 * @property string|null $civilite
 * @property string $nom
 * @property string|null $prenom
 * @property string|null $email
 * @property string|null $telephone
 * @property string|null $adresse
 * @property string|null $cp
 * @property string|null $ville
 * @property int|null $pays_id
 * @property string|null $pays_nom
 * @property \Illuminate\Support\Carbon|null $naissance_at
 * @property string|null $naissance_lieu
 * @property string|null $permis_numero
 * @property \Illuminate\Support\Carbon|null $permis_at
 * @property string|null $permis_delivre
 * @property string|null $observation
 * @property mixed|null $custom_fields
 * @property int $categorie_id
 * @property int|null $vehicule_id
 * @property string $categorie_nom
 * @property string|null $franchise
 * @property \Illuminate\Support\Carbon $debut_at
 * @property \Illuminate\Support\Carbon $fin_at
 * @property int $debut_lieu_id
 * @property int $fin_lieu_id
 * @property string|null $debut_lieu_nom
 * @property string|null $fin_lieu_nom
 * @property string|null $montant_base
 * @property mixed|null $prestations
 * @property array|null $promotions
 * @property array|null $echeancier
 * @property string|null $total
 * @property string|null $montant_paye
 * @property string|null $note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Categorie|null $categorie
 * @property-read \App\Models\Client|null $client
 * @property-read \Ipsum\Reservation\app\Models\Reservation\Etat|null $etat
 * @property-read float|null $acompte
 * @property-read mixed $date_naissance_minimum
 * @property-read mixed $date_permis_minimum
 * @property-read bool $has_echeancier
 * @property-read bool $has_promotions_visible
 * @property-read bool $is_confirmed
 * @property-read bool $is_payed
 * @property-read int $nb_jours
 * @property-read mixed $tarif_journalier
 * @property-read Lieu|null $lieuDebut
 * @property-read Lieu|null $lieuFin
 * @property-read \Ipsum\Reservation\app\Models\Reservation\Condition|null $condition
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ipsum\Reservation\app\Models\Reservation\Paiement[] $paiements
 * @property-read int|null $paiements_count
 * @property-read \Ipsum\Reservation\app\Models\Reservation\Pays|null $pays
 * @property-read Vehicule|null $vehicule
 * @method static Builder|Reservation confirmed()
 * @method static Builder|Reservation confirmedBetweenDates(\Carbon\CarbonInterface $debut_at, \Carbon\CarbonInterface $fin_at)
 * @method static \Ipsum\Reservation\database\factories\ReservationFactory factory(...$parameters)
 * @method static Builder|Reservation newModelQuery()
 * @method static Builder|Reservation newQuery()
 * @method static Builder|Reservation notConfirmed()
 * @method static Builder|Reservation query()
 * @mixin \Eloquent
 */
class Reservation extends BaseModel
{
    use HasFactory;

    protected $guarded = ['id', 'reference', 'pays_nom', 'categorie_nom', 'debut_lieu_nom', 'fin_lieu_nom'];



    protected $casts = [
        'custom_fields' => AsCustomFieldsObject::class,
        'prestations' => PrestationCollection::class,
        'promotions' => PromotionCollection::class,
        'echeancier' => EcheancierCollection::class,
    ];



    protected static function newFactory()
    {
        return ReservationFactory::new();
    }

    protected static function booted()
    {
        static::deleting(function (self $reservation) {
            $reservation->paiements()->delete();
        });

        self::created(function (self $reservation) {
            // Génération de la référence
            $reservation->reference = str_pad($reservation->id, 6, "0", STR_PAD_LEFT);
            $reservation->save();
        });

        self::saving(function (self $reservation) {
            // Attribution automatique d'un véhicule pour une réservation confirmée
            // S'il n'y a pas de véhicule dispo cela ne bloque pas
            // Pas de vérification sur la catégorie pour permettre un surbooking
            if ($reservation->is_confirmed
                and (
                    $reservation->etat_id != $reservation->getOriginal('etat_id')
                    or $reservation->vehicule_id != $reservation->getOriginal('vehicule_id')
                    or $reservation->vehicule_id === null
                )
            ) {
                $vehicule = Vehicule::where('categorie_id', $reservation->categorie_id)->whereDoesntHaveReservationConfirmed($reservation->debut_at, $reservation->fin_at)->orderBy('mise_en_circualtion_at', 'desc')->first();
                $reservation->vehicule_id = !is_null($vehicule) ? $vehicule->id : null;
            }

            if($reservation->is_confirmed and !$reservation->contrat) {
                // Création du contrat
                $reservation->generationReferenceContrat();
            }
        });

    }



    /*
     * Relations
     */

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function etat()
    {
        return $this->belongsTo(Etat::class);
    }

    public function condition()
    {
        return $this->belongsTo(Condition::class, 'condition_paiement_id');
    }

    public function pays()
    {
        return $this->belongsTo(Pays::class);
    }

    public function lieuDebut()
    {
        return $this->belongsTo(Lieu::class, 'debut_lieu_id');
    }

    public function lieuFin()
    {
        return $this->belongsTo(Lieu::class, 'fin_lieu_id');
    }

    public function client()
    {
        return $this->belongsTo(config('ipsum.reservation.client.model'));
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }




    /*
     * Scopes
     */

    public function scopeConfirmed(Builder $query)
    {
        return $query->where('etat_id', Etat::VALIDEE_ID);
    }

    public function scopeNotConfirmed(Builder $query)
    {
        return $query->where('etat_id', '!=', Etat::VALIDEE_ID);
    }

    public function scopeConfirmedBetweenDates(Builder $query, CarbonInterface $debut_at, CarbonInterface $fin_at)
    {
        $debut_at->copy()->subHours(config('settings.reservation.battement_entre_reservations'));
        $fin_at->copy()->addHours(config('settings.reservation.battement_entre_reservations'));

        return $query->confirmed()->where(function (Builder $query) use ($debut_at, $fin_at) {
            return $query->where(function (Builder $query) use ($debut_at, $fin_at) {
                $query->where('debut_at', '>=', $debut_at)->where('debut_at', '<=', $fin_at);
            })->orWhere(function ($query) use ($debut_at, $fin_at) {
                $query->where('fin_at', '>=', $debut_at)->where('fin_at', '<=', $fin_at);
            })->orWhere(function ($query) use ($debut_at, $fin_at) {
                $query->where('debut_at', '<=', $debut_at)->where('fin_at', '>=', $fin_at);
            });
        });
    }



    /*
     * Functions
     */

    /**
     * Calcul la durée de la réservation
     * @param CarbonInterface $date_debut
     * @param CarbonInterface $date_fin
     * @return int
     * @note S'il y a plus d'une heure entre le début et la fin, on rajoute une journée
     */
    public static function calculDuree(CarbonInterface $date_debut, CarbonInterface $date_fin): int
    {
        return $date_debut->diffInDays($date_fin->copy()->subMinutes(61)) + 1;
    }

    protected function generationReference($id)
    {
        return date('ymd').str_pad($id, 6, "0", STR_PAD_LEFT);
    }

    public function generationReferenceContrat()
    {
        if ($this->contrat !== null) {
            throw new \Exception('Contrat déjà généré');
        }

        $last_contrat = self::whereNotNull('contrat')->orderBy('contrat', 'desc')->first();
        if(!$last_contrat) {
            $id = 1;
        } else {
            $id = substr($last_contrat->contrat, -6);
            $id++;
        }
        $this->contrat = 'C'.$this->generationReference($id);
    }


    /*
     * Accessors & Mutators
     */

    public function getIsConfirmedAttribute(): bool
    {
        return $this->etat_id == Etat::VALIDEE_ID;
    }

    public function getIsPayedAttribute(): bool
    {
        return $this->total <= $this->montant_paye;
    }

    public function getNbJoursAttribute(): int
    {
        return self::calculDuree($this->debut_at, $this->fin_at);
    }

    public function getHasPromotionsVisibleAttribute(): bool
    {
        return count($this->promotions_visible) ? true : false;
    }

    public function getDateNaissanceMinimumAttribute()
    {
        return $this->debut_at->subYears($this->categorie->age_minimum);
    }

    public function getDatePermisMinimumAttribute()
    {
        return $this->debut_at->subYears($this->categorie->annee_permis_minimum);
    }

    public function getTarifJournalierAttribute()
    {
        return $this->total / $this->nb_jours;
    }

    public function getAcompteAttribute(): ?float
    {
        $condition = $this->condition;
        if (!$condition) {
            return null;
        }
        return $condition->acompte($this->total);
    }


    /**
     * Suppression des champs vides
     * @param array|null $custom_fields
     */
    public function setCustomFieldsAttribute(?array $custom_fields): void
    {
        $fields = null;
        if ($custom_fields !== null) {
            foreach ($custom_fields as $field => $value) {
                if ($value !== null) {
                    $fields[$field] = $value;
                }
            }
        }

        $this->attributes['custom_fields'] = $fields === null ? null : $this->castAttributeAsJson('custom_fields', $fields);
    }




    public function setPaysIdAttribute($value)
    {
        $this->attributes['pays_id'] = $value;
        $this->attributes['pays_nom'] = $this->pays ? $this->pays->nom : '';
    }
    
    public function setCategorieIdAttribute($value)
    {
        $this->attributes['categorie_id'] = $value;
        $this->attributes['categorie_nom'] = $this->categorie ? $this->categorie->nom : '';
    }
    
    public function setDebutLieuIdAttribute($value)
    {
        $this->attributes['debut_lieu_id'] = $value;
        $this->attributes['debut_lieu_nom'] = $this->lieuDebut ? $this->lieuDebut->nom : '';
    }

    public function setFinLieuIdAttribute($value)
    {
        $this->attributes['fin_lieu_id'] = $value;
        $this->attributes['fin_lieu_nom'] = $this->lieuFin ? $this->lieuFin->nom : '';
    }

    public function setDebutAtAttribute($value)
    {
        $this->attributes['debut_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $value);
    }

    public function setFinAtAttribute($value)
    {
        $this->attributes['fin_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $value);
    }

    public function getDates()
    {
        return ['naissance_at', 'permis_at', 'debut_at', 'fin_at', 'created_at', 'updated_at'];
    }
}
