<?php

namespace Ipsum\Reservation\app\Models\Reservation;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Ipsum\Core\app\Models\BaseModel;
use Carbon\Carbon;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Ipsum\Reservation\app\Models\Lieu\Lieu;
use Ipsum\Reservation\database\factories\ReservationFactory;
use Ipsum\Reservation\app\Models\Reservation\Concerns\Sessionable;


class Reservation extends BaseModel
{
    use HasFactory, Sessionable;

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
    public static function calculDuree(Carbon $date_debut, Carbon $date_fin)
    {
        return $date_debut->diffInDays($date_fin->subMinutes(61)) + 1;
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

    public function getDates()
    {
        return ['naissance_at', 'permis_at', 'debut_at', 'fin_at', 'created_at', 'updated_at'];
    }
}
