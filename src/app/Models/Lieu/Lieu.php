<?php

namespace Ipsum\Reservation\app\Models\Lieu;


use Carbon\CarbonInterface;
use Ipsum\Admin\app\Casts\AsCustomFieldsObject;
use Ipsum\Admin\Concerns\Sortable;
use Ipsum\Core\app\Models\BaseModel;
use Ipsum\Core\Concerns\Slug;
use Ipsum\Core\Concerns\Translatable;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Ipsum\Reservation\app\Models\Prestation\Prestation;
use Ipsum\Reservation\app\Models\Reservation\Reservation;

/**
 * Ipsum\Reservation\app\Models\Lieu\Lieu
 *
 * @property int $id
 * @property string $slug
 * @property int $type_id
 * @property int $is_actif
 * @property string $nom
 * @property string $telephone
 * @property string $adresse
 * @property string|null $instruction
 * @property string $horaires_txt
 * @property string $gps
 * @property array $emails
 * @property array $emails_reservation
 * @property AsCustomFieldsObject|null $custom_fields
 * @property int $order
 * @property string|null $seo_title
 * @property string|null $seo_description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ipsum\Reservation\app\Models\Lieu\Fermeture> $fermetures
 * @property-read int|null $fermetures_count
 * @property-read mixed $email_first
 * @property-read mixed $email_reservation_first
 * @property-read mixed $is_aeroport
 * @property-read mixed $lat
 * @property-read mixed $lng
 * @property-read mixed $tag_meta_description
 * @property-read mixed $tag_title
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ipsum\Reservation\app\Models\Lieu\Horaire> $horaires
 * @property-read int|null $horaires_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Prestation> $prestations
 * @property-read int|null $prestations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Reservation> $reservationsDebut
 * @property-read int|null $reservations_debut_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Reservation> $reservationsFin
 * @property-read int|null $reservations_fin_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ipsum\Core\app\Models\Translate> $translates
 * @property-read int|null $translates_count
 * @property-read \Ipsum\Reservation\app\Models\Lieu\Type|null $type
 * @method static \Illuminate\Database\Eloquent\Builder|Lieu agences()
 * @method static \Illuminate\Database\Eloquent\Builder|Lieu filtreSortable($objet)
 * @method static \Illuminate\Database\Eloquent\Builder|Lieu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Lieu newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Lieu query()
 * @mixin \Eloquent
 */
class Lieu extends BaseModel
{
    use Slug, Sortable, Translatable;

    protected $table = 'lieux';

    protected $guarded = ['id'];

    protected $translatable_attributes = ['nom', 'adresse', 'instruction', 'horaires_txt', 'seo_title', 'seo_description'];

    protected $translatable_attributes_adds = 'ipsum.reservation.lieu.translatable_attributes_adds';

    //static public $types = ['aeroport' => 'Aéroport', 'agence' => 'Agence', 'depot' => 'Dépôt', 'accueil' => 'Accueil et retour', 'navette' => 'Dépôt avec navette'];

    protected $slugBase = 'nom';

    protected $casts = [
        'emails' => 'array',
        'emails_reservation' => 'array',
        'custom_fields' => AsCustomFieldsObject::class,
    ];


    protected static function booted()
    {
        static::deleting(function (self $lieu) {
            $lieu->fermetures()->delete();
            $lieu->horaires()->delete();
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

    public function categoriesExclus()
    {
        return $this->belongsToMany(Categorie::class, 'categorie_lieux_exclus');
    }




    /*
     * Functions
     */

    public function isOuvert(CarbonInterface $date): bool
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

    public function isOuvertHoraire(CarbonInterface $date)
    {
        $count = $this->horaires()
            ->creneaux($date)
            ->count();

        if ($count) {
            // Vérification jour férié
            $count = $this->horaires()
                ->date($date, $date->isFerie($this))
                ->count();
        }

        return $count ? true : false;
    }

    public function creneauxHorairesToString(CarbonInterface $date)
    {
        $horaires = $this->horaires()->creneaux($date)->get();
        if ($horaires->count() and Ferie::isFerie($date->copy(), $this)) {
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
        return $query->where('type_id', Type::AGENCE_ID);
    }




    /*
     * Accessors & Mutators
     */

    public function getIsAeroportAttribute()
    {
        return $this->type_id === Type::AEROPORT_ID;
    }

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
        return isset($gps[0]) ? trim($gps[0]) : null;
    }

    public function getLngAttribute()
    {
        $gps = explode(',', $this->gps);
        return isset($gps[1]) ? trim($gps[1]) : null;
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
