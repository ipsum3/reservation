<?php

namespace Ipsum\Reservation\app\Models\Categorie;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Ipsum\Core\app\Models\BaseModel;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Ipsum\Reservation\database\factories\VehiculeFactory;


/**
 * Ipsum\Reservation\app\Models\Categorie\Vehicule
 *
 * @property int $id
 * @property string $immatriculation
 * @property int|null $categorie_id
 * @property string $marque_modele
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Ipsum\Reservation\app\Models\Categorie\Categorie|null $categorie
 * @property-read bool $has_no_blocage
 * @property-read mixed $tag_meta_description
 * @property-read mixed $tag_title
 * @property-read mixed $tarif_a_partir
 * @property-read \Illuminate\Database\Eloquent\Collection|Reservation[] $reservations
 * @property-read int|null $reservations_count
 * @method static \Ipsum\Reservation\database\factories\VehiculeFactory factory(...$parameters)
 * @method static Builder|Vehicule newModelQuery()
 * @method static Builder|Vehicule newQuery()
 * @method static Builder|Vehicule query()
 * @mixin \Eloquent
 */
class Vehicule extends BaseModel
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];


    protected $casts = [
        'sortie_at' => 'datetime:Y-m-d',
        'mise_en_circualtion_at' => 'datetime:Y-m-d',
    ];


    
    protected static function newFactory()
    {
        return VehiculeFactory::new();
    }


    /*protected static function booted()
    {
        static::deleting(function (self $categorie) {
            $categorie->blocages()->delete();
        });
    }*/


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

    /*public function blocages()
    {
        return $this->hasMany(Blocage::class);
    }*/






    /*
     * Scopes
     */

    public function scopeWhereDoesntHaveReservationConfirmed(Builder $query, $date_debut, $date_fin)
    {
        $query->whereDoesntHave('reservations', function (Builder $query) use ($date_debut, $date_fin) {
            $query->confirmedBetweenDates($date_debut, $date_fin);
        });
    }



    /*
     * Accessors & Mutators
     */

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

}