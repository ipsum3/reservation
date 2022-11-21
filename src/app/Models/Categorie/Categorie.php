<?php

namespace Ipsum\Reservation\app\Models\Categorie;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Ipsum\Admin\app\Casts\AsCustomFieldsObject;
use Ipsum\Admin\Concerns\Htmlable;
use Ipsum\Core\app\Models\BaseModel;
use Ipsum\Core\Concerns\Translatable;
use Ipsum\Media\Concerns\Mediable;
use Ipsum\Reservation\app\Models\Prestation\Prestation;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Ipsum\Reservation\app\Models\Tarif\Tarif;
use Ipsum\Reservation\database\factories\CategorieFactory;


/**
 * Ipsum\Reservation\app\Models\Categorie\Categorie
 *
 * @property int $id
 * @property int $type_id
 * @property string $nom
 * @property string $modeles
 * @property int|null $nb_vehicules
 * @property string|null $description
 * @property string|null $texte
 * @property int $place
 * @property int $porte
 * @property int $bagage
 * @property int|null $volume
 * @property int|null $longeur
 * @property int|null $largeur
 * @property int|null $hauteur
 * @property int $climatisation
 * @property int $transmission_id
 * @property int $motorisation_id
 * @property int $carrosserie_id
 * @property string|null $caution
 * @property string|null $franchise
 * @property int $age_minimum
 * @property int $annee_permis_minimum
 * @property string|null $seo_title
 * @property string|null $seo_description
 * @property mixed|null $custom_fields
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ipsum\Reservation\app\Models\Categorie\Blocage[] $blocages
 * @property-read int|null $blocages_count
 * @property-read \Ipsum\Reservation\app\Models\Categorie\Carrosserie|null $carrosserie
 * @property-read bool $has_no_blocage
 * @property-read bool $has_vehicule
 * @property-read bool $is_dispo
 * @property-read mixed $tag_meta_description
 * @property-read mixed $tag_title
 * @property-read mixed $tarif_a_partir
 * @property-read \Ipsum\Media\app\Models\Media|null $illustration
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ipsum\Media\app\Models\Media[] $medias
 * @property-read int|null $medias_count
 * @property-read \Ipsum\Reservation\app\Models\Categorie\Motorisation|null $motorisation
 * @property-read \Illuminate\Database\Eloquent\Collection|Prestation[] $prestations
 * @property-read int|null $prestations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Reservation[] $reservations
 * @property-read int|null $reservations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Tarif[] $tarifs
 * @property-read int|null $tarifs_count
 * @property-read \Ipsum\Reservation\app\Models\Categorie\Transmission|null $transmission
 * @property-read \Ipsum\Reservation\app\Models\Categorie\Type|null $type
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ipsum\Reservation\app\Models\Categorie\Vehicule[] $vehicules
 * @property-read int|null $vehicules_count
 * @method static \Ipsum\Reservation\database\factories\CategorieFactory factory(...$parameters)
 * @method static Builder|Categorie newModelQuery()
 * @method static Builder|Categorie newQuery()
 * @method static Builder|Categorie query()
 * @method static Builder|Categorie withCountBlocage(\Carbon\CarbonInterface $date_debut, \Carbon\CarbonInterface $date_fin)
 * @method static Builder|Categorie withCountVehiculeDispo(\Carbon\CarbonInterface $date_debut, \Carbon\CarbonInterface $date_fin)
 * @mixin \Eloquent
 */
class Categorie extends BaseModel
{
    use HasFactory, Htmlable, Mediable, Translatable;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $htmlable = ['description', 'texte'];

    protected $translatable_attributes = ['nom', 'modeles', 'description', 'texte', 'seo_title', 'seo_description'];

    protected $translatable_attributes_adds = 'ipsum.reservation.categorie.translatable_attributes_adds';

    protected $casts = [
        'custom_fields' => AsCustomFieldsObject::class,
    ];

    
    protected static function newFactory()
    {
        return CategorieFactory::new();
    }


    protected static function booted()
    {
        static::deleting(function (self $categorie) {
            $categorie->blocages()->delete();
        });
    }


    /*
     * Relations
     */

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function motorisation()
    {
        return $this->belongsTo(Motorisation::class);
    }

    public function transmission()
    {
        return $this->belongsTo(Transmission::class);
    }

    public function carrosserie()
    {
        return $this->belongsTo(Carrosserie::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function blocages()
    {
        return $this->hasMany(Blocage::class);
    }

    /*public function choixModeles()
    {
        return $this->hasMany('App\Categorie\Modele');
    }*/

    public function vehicules()
    {
        return $this->hasMany(Vehicule::class);
    }

    public function tarifs()
    {
        return $this->hasMany(Tarif::class);
    }

    public function prestations()
    {
        return $this->morphToMany(Prestation::class, 'prestable')->withPivot('montant');
    }



    /*
     * Scopes
     */

    /*public function scopeWithoutBlocage($query, CarbonInterface $date_debut, CarbonInterface $date_fin)
    {
        $query->whereDoesntHave('blocages', function (Builder $query) use ($date_debut, $date_fin) {
            $query->betweenDates($date_debut, $date_fin);
        });
    }*/

    public function scopeWithCountBlocage(Builder $query, CarbonInterface $date_debut, CarbonInterface $date_fin)
    {
        $query->withCount(['blocages' => function (Builder $query) use ($date_debut, $date_fin) {
            $query->betweenDates($date_debut, $date_fin);
        }]);
    }

    public function scopeWithCountVehiculeDispo(Builder $query, CarbonInterface $date_debut, CarbonInterface $date_fin)
    {
        $query->withCount(['vehicules' => function (Builder $query) use ($date_debut, $date_fin) {
            $query->whereDoesntHaveReservationConfirmed($date_debut, $date_fin);
        }]);
    }





    /*
     * Accessors & Mutators
     */

    public function getTagTitleAttribute()
    {
        return $this->attributes['seo_title'] == '' ? 'Catégorie '.$this->nom.' : '. $this->modeles : $this->attributes['seo_title'];
    }

    public function getTagMetaDescriptionAttribute()
    {
        return $this->attributes['seo_description'] == '' ? strip_tags($this->description) : $this->attributes['seo_description'];
    }

    public function hasNoBlocage(?CarbonInterface $date_debut = null, ?CarbonInterface $date_fin = null): bool
    {
        if ($date_debut !== null) {
            $this->loadCount(['blocages' => function (Builder $query) use ($date_debut, $date_fin) {
                $query->betweenDates($date_debut, $date_fin);
            }]);
        } elseif ($this->blocages_count === null) {
            throw new \Exception('A utiliser avec scopeWithCountBlocage');
        }

        return $this->blocages_count === 0;
    }

    public function getHasNoBlocageAttribute(): bool
    {
        return $this->hasNoBlocage();
    }

    public function hasVehicule(?CarbonInterface $date_debut = null, ?CarbonInterface $date_fin = null): bool
    {
        if ($date_debut !== null) {
            $this->loadCount(['vehicules' => function (Builder $query) use ($date_debut, $date_fin) {
                $query->whereDoesntHaveReservationConfirmed($date_debut, $date_fin);
            }]);
        } elseif ($this->vehicules_count === null) {
            throw new \Exception('A utiliser avec scopeWithCountVehiculeDispo');
        }

        return $this->vehicules_count !== 0;
    }

    public function getHasVehiculeAttribute(): bool
    {
        return $this->hasVehicule();
    }

    public function getIsDispoAttribute(): bool
    {
        if (config('ipsum.reservation.check_vehicules_disponible')) {
            return $this->has_vehicule and $this->has_no_blocage;
        } else {
            return $this->has_no_blocage;
        }
    }


    public function getTarifAPartirAttribute()
    {
        return $this->tarifsEnCoursOuFutur->count() ? $this->tarifsEnCoursOuFutur->first()->montant : null;
    }

}
