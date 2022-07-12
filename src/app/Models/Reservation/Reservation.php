<?php

namespace Ipsum\Reservation\app\Models\Reservation;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Ipsum\Core\app\Models\BaseModel;
use Carbon\Carbon;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Ipsum\Reservation\app\Models\Lieu\Lieu;
use Ipsum\Reservation\database\factories\ReservationFactory;
use Ipsum\Reservation\app\Models\Reservation\Concerns\Sessionable;


/**
 * Ipsum\Reservation\app\Models\Reservation\Reservation
 *
 * @property int $id
 * @property string|null $reference
 * @property int $etat_id
 * @property int $modalite_paiement_id
 * @property int|null $client_id
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
 * @property string|null $permis_numero
 * @property \Illuminate\Support\Carbon|null $permis_at
 * @property string|null $permis_delivre
 * @property string|null $observation
 * @property array|null $custom_fields
 * @property int $categorie_id
 * @property string $categorie_nom
 * @property string|null $franchise
 * @property \Illuminate\Support\Carbon $debut_at
 * @property \Illuminate\Support\Carbon $fin_at
 * @property int $debut_lieu_id
 * @property int $fin_lieu_id
 * @property string|null $debut_lieu_nom
 * @property string|null $fin_lieu_nom
 * @property string|null $montant_base
 * @property string|null $prestations
 * @property string|null $promotions
 * @property string|null $total
 * @property string|null $montant_paye
 * @property string|null $note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Categorie|null $categorie
 * @property-read \App\Models\User|null $client
 * @property-read \Ipsum\Reservation\app\Models\Reservation\Etat|null $etat
 * @property-read mixed $date_naissance_minimum
 * @property-read mixed $date_permis_minimum
 * @property-read mixed $has_promotions_visible
 * @property-read mixed $is_confirmed
 * @property-read mixed $is_payee
 * @property-read mixed $nb_jours
 * @property-read mixed $tarif_journalier
 * @property-read Lieu|null $lieuDebut
 * @property-read Lieu|null $lieuFin
 * @property-read \Ipsum\Reservation\app\Models\Reservation\Modalite|null $modalite
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ipsum\Reservation\app\Models\Reservation\Paiement[] $paiements
 * @property-read int|null $paiements_count
 * @property-read \Ipsum\Reservation\app\Models\Reservation\Pays|null $pays
 * @method static Builder|Reservation confirmed()
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

    const SESSION_ID = 'reservation';


    protected $casts = [
        'custom_fields' => 'array',
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

        self::created(function ($reservation) {
            // Génération de la référence
            $reservation->reference = str_pad($reservation->id, 6, "0", STR_PAD_LEFT);
            $reservation->save();
        });

    }



    /*
     * Relations
     */

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function etat()
    {
        return $this->belongsTo(Etat::class);
    }

    public function modalite()
    {
        return $this->belongsTo(Modalite::class, 'modalite_paiement_id');
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

    /*
     * TODO ! mettre class en config ?
     */
    public function client()
    {
        return $this->belongsTo(config('ipsum.reservation.client.model'));
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


    /*
     * Functions
     */

    /**
     * Calcul la durée de la réservation
     * @param Carbon $date_debut
     * @param Carbon $date_fin
     * @return int
     * @note S'il y a plus d'une heure entre le début et la fin, on rajoute une journée
     */
    public static function calculDuree(Carbon $date_debut, Carbon $date_fin): int
    {
        return $date_debut->diffInDays($date_fin->copy()->subMinutes(61)) + 1;
    }


    /*
     * Accessors & Mutators
     */

    public function getIsConfirmedAttribute()
    {
        return $this->etat_id === Etat::VALIDEE_ID;
    }

    public function getIsPayeeAttribute()
    {
        return $this->total > $this->montant_paye;
    }

    public function getNbJoursAttribute()
    {
        return self::calculDuree($this->debut_at, $this->fin_at);
    }

    public function getHasPromotionsVisibleAttribute()
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
