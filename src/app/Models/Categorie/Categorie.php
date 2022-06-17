<?php

namespace Ipsum\Reservation\app\Models\Categorie;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Ipsum\Admin\Concerns\Htmlable;
use Ipsum\Core\app\Models\BaseModel;
use Ipsum\Media\Concerns\Mediable;
use Ipsum\Reservation\app\Models\Prestation\Prestation;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Ipsum\Reservation\app\Models\Tarif\Tarif;
use Ipsum\Reservation\database\factories\CategorieFactory;
use Config;

class Categorie extends BaseModel
{
    use HasFactory, Htmlable, Mediable;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $htmlable = ['description', 'texte'];

    protected $casts = [
        'custom_fields' => 'array',
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

    /*public function vehicules()
    {
        return $this->hasMany('App\Categorie\Vehicule');
    }*/

    public function tarifs()
    {
        return $this->hasMany(Tarif::class);
    }

    public function prestations()
    {
        return $this->morphToMany(Prestation::class, 'prestable')->withPivot('montant');
    }

    
    /*
     * TODO
     */
    /*public function promotionsLignes()
    {
        return $this->hasMany('App\Promotion\Ligne');
    }

    public function promotionsLignesSuperieur()
    {
        return $this->hasMany('App\Promotion\Ligne');
    }*/



    /*
     * Eager Loading spécifiques
     */

    public function tarifsEnCoursOuFutur()
    {
        return $this->tarifs()->hasSaisonEnCoursOuFutur()->orderBy('montant_eur', 'asc');
    }


    // Utilisé avec scopeWithBlocagesCount
    public function blocagesCount()
    {
        return $this->hasOne('App\Categorie\Blocage')
            ->selectRaw('categorie_id, count(*) as aggregate')
            ->groupBy('categorie_id');
    }

    public function scopeWithBlocagesCount($query, $date_debut, $date_fin)
    {
        return $query->with(['blocagesCount' => function ($query) use ($date_debut, $date_fin) {
            $query->betweenDates($date_debut, $date_fin);
        }]);
    }



    /*
     * Scopes
     */





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

    public function getIsDisponibleAttribute()
    {
        if (! array_key_exists('blocagesCount', $this->relations)) {
            return null;
        }

        $blocages_count = $this->getRelation('blocagesCount');

        // $blocages_count ne retourne rien si pas d'enregistrement
        $is_ok_blocage = !$blocages_count or !$blocages_count->aggregate;

        if (!$is_ok_blocage) {
            return false;
        }

        return true;
    }


    public function getTarifAPartirAttribute()
    {
        return $this->tarifsEnCoursOuFutur->count() ? $this->tarifsEnCoursOuFutur->first()->montant : null;
    }

}
