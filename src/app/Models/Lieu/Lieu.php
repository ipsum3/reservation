<?php

namespace Ipsum\Reservation\app\Models\Lieu;


use App\Article\Translatable;
use Ipsum\Admin\Concerns\Sortable;
use Ipsum\Core\app\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Config;
use Ipsum\Core\Concerns\Slug;
use Ipsum\Reservation\app\Models\Prestation\Prestation;
use Ipsum\Reservation\app\Models\Reservation\Reservation;

class Lieu extends BaseModel
{
    use Slug, Sortable;

    protected $table = 'lieux';

    protected $guarded = ['id'];

    //static public $types = ['aeroport' => 'Aéroport', 'agence' => 'Agence', 'depot' => 'Dépôt', 'accueil' => 'Accueil et retour', 'navette' => 'Dépôt avec navette'];

    protected $slugBase = 'nom';

    protected $casts = [
        'emails' => 'array',
        'emails_reservation' => 'array',
    ];


    protected static function booted()
    {
        static::deleting(function (self $lieu) {
            $lieu->fermetures()->delete();
            $lieu->horaires()->delete();
            $lieu->feries()->delete();
        });
    }

    

    /*
     * 
     * Relations
     */

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function horaires()
    {
        return $this->hasMany(Horaire::class);
    }

    public function fermetures()
    {
        // Ne pas utiliser car il faudrait prendre en compre les clés étrangères null
        return $this->hasMany(Fermeture::class);
    }

    public function reservationsDebut()
    {
        return $this->hasMany(Reservation::class, 'lieu_debut_id');
    }

    public function reservationsFin()
    {
        return $this->hasMany(Reservation::class, 'lieu_fin_id');
    }

    public function prestations()
    {
        return $this->morphToMany(Prestation::class, 'prestable')->withPivot('montant');
    }




    /*
     * Functions
     */

    public function isOuvert(Carbon $date)
    {
        // Vérification fermeture
        $count = Fermeture::where(function ($query) {
            $query->where('lieu_id', $this->id)->orWhereNull('lieu_id');
        })
        ->betweenDates($date)
        ->count();
        if($count) {
            return false;
        }

        return true;
    }

    public function isOuvertHoraire(Carbon $date)
    {
        $count = $this->horaires()
            ->creneaux($date)
            ->count();

        if ($count) {
            // Vérification jour férié
            $ferie = $this->zone()->first()->isFerie($date->copy());

            $count = $this->horaires()
                ->date($date, $ferie)
                ->count();
        }

        return $count ? true : false;
    }

    public function creneauxHorairesToString(Carbon $date)
    {
        $horaires = $this->horaires()->creneaux($date)->get();
        if ($horaires->count() and $this->zone->isFerie($date->copy())) {
            $horaires = $this->horaires()->creneaux($date, true)->get();
        }

        $crenaux = false;
        foreach ($horaires as $key => $horaire) {
            $crenaux .= ($key ? ' et ' : '').$horaire->creneauToString;
        }

        return $crenaux;
    }




    /*
     * Scopes
     */

    public function scopeAgences($query)
    {
        return $query->where('type', 'agence');
    }




    /*
     * Accessors & Mutators
     */

    public function getEmailFirstAttribute()
    {
        return $this->emails[0];
    }

    public function getEmailReservationFirstAttribute()
    {
        return $this->emails_reservation[0];
    }

    public function getLatAttribute()
    {
        $gps = explode(',', $this->gps);
        return isset($gps[0]) ? $gps[0] : null;
    }

    public function getLngAttribute()
    {
        $gps = explode(',', $this->gps);
        return isset($gps[1]) ? $gps[1] : null;
    }

    public function getTagTitleAttribute()
    {
        return $this->attributes['seo_title'] == '' ? $this->titre : $this->attributes['seo_title'];
    }

    public function getTagMetaDescriptionAttribute()
    {
        return $this->attributes['seo_description'] == '' ? $this->extrait : $this->attributes['seo_description'];
    }

}
