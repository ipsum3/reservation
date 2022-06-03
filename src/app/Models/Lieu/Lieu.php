<?php

namespace Ipsum\Reservation\app\Models\Lieu;


use App\Article\Translatable;
use Ipsum\Core\app\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Config;
use Ipsum\Core\Concerns\Slug;
use Ipsum\Reservation\app\Models\Reservation\Reservation;

class Lieu extends BaseModel
{
    use Slug;

    protected $table = 'lieux';

    //protected $fillable = array('zone_id', 'nom', 'type', 'telephone', 'adresse', 'instruction', 'horaires', 'taxe_aeroport_eur', 'taxe_aeroport_max_eur', 'taxe_aeroport_usd', 'taxe_aeroport_max_usd', 'gps', 'email', 'email_reservation', 'ordre');


    //static public $types = ['aeroport' => 'Aéroport', 'agence' => 'Agence', 'depot' => 'Dépôt', 'accueil' => 'Accueil et retour', 'navette' => 'Dépôt avec navette'];



    /*
     * Relations
     */

    public function horaires()
    {
        return $this->hasMany(Horaire::class);
    }

    /*public function fermetures()
    {
        // Ne pas utiliser car il faudrait prendre en compre les clés étrangères null
        return $this->hasMany(Fermeture::class);
    }*/

    public function reservationsDebut()
    {
        return $this->hasMany(Reservation::class, 'lieu_debut_id');
    }

    public function reservationsFin()
    {
        return $this->hasMany(Reservation::class, 'lieu_fin_id');
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

    public function calculerTaxeAeroport($nb_jours)
    {
        if (!$this->hasTaxeAeroport) {
            return null;
        }

        $montant = $nb_jours * floatval($this->taxe_aeroport);

        return $montant <  $this->taxe_aeroport_max ? $montant : $this->taxe_aeroport_max;
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

    public function getHasTaxeAeroportAttribute()
    {
        return $this->taxe_aeroport !== null;
    }

    public function getTaxeAeroportAttribute()
    {
        return $this->{'taxe_aeroport_'.Config::get('app.devise')};
    }

    public function getTaxeAeroportMaxAttribute()
    {
        return $this->{'taxe_aeroport_max_'.Config::get('app.devise')};
    }

    public function getIsAeroportAttribute()
    {
        return $this->type === self::TYPE_AEROPORT;
    }

    public function getIsNavetteAttribute()
    {
        return $this->type === self::TYPE_NAVETTE;
    }

    public function getEmailsAttribute()
    {
        return array_map('trim', explode(',', $this->email));
    }

    public function getEmailFirstAttribute()
    {
        return $this->emails[0];
    }

    public function getEmailsReservationAttribute()
    {
        return array_map('trim', explode(',', $this->email_reservation));
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
