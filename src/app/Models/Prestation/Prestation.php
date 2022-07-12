<?php

namespace Ipsum\Reservation\app\Models\Prestation;

use App\Article\Translatable;
use Ipsum\Admin\Concerns\Sortable;
use Ipsum\Core\app\Models\BaseModel;
use Exception;
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Config;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Ipsum\Reservation\app\Models\Lieu\Lieu;

/**
 * Ipsum\Reservation\app\Models\Prestation\Prestation
 *
 * @property int $id
 * @property int $type_id
 * @property string $tarification
 * @property string|null $class
 * @property string $nom
 * @property string|null $description
 * @property string|null $montant
 * @property int $quantite_max
 * @property int|null $gratuit_apres
 * @property int|null $jour_fact_max
 * @property int|null $age_max
 * @property int $order
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ipsum\Reservation\app\Models\Prestation\Blocage[] $blocages
 * @property-read int|null $blocages_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Categorie[] $categories
 * @property-read int|null $categories_count
 * @property-read mixed $is_obligatoire
 * @property-read mixed $is_tarification_agence
 * @property-read \Illuminate\Database\Eloquent\Collection|Lieu[] $lieux
 * @property-read int|null $lieux_count
 * @property-read \Ipsum\Reservation\app\Models\Prestation\Type|null $type
 * @method static \Illuminate\Database\Eloquent\Builder|Prestation filtreSortable($objet)
 * @method static \Illuminate\Database\Eloquent\Builder|Prestation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Prestation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Prestation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Prestation withoutBlocage($debut_at, $fin_at, $zone_id)
 * @mixin \Eloquent
 */
class Prestation extends BaseModel
{
    use Sortable;

    public $timestamps = false;

    public static $LISTE_TARIFICATION = array('jour', 'forfait', 'agence');


    protected $guarded = ['id'];


    protected static function booted()
    {
        static::deleting(function (self $prestation) {
            $prestation->blocages()->delete();
            $prestation->categories()->detach();
            $prestation->lieux()->detach();
        });
    }


    /*
     * Relations
     */

    public function blocages()
    {
        return $this->hasMany(Blocage::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function categories()
    {
        return $this->morphedByMany(Categorie::class, 'prestable')->withPivot('montant');
    }

    public function lieux()
    {
        return $this->morphedByMany(Lieu::class, 'prestable')->withPivot('montant');
    }


    /*
     * Scopes
     */

    public function scopeWithoutBlocage($query, $debut_at, $fin_at, $zone_id)
    {
        $query->whereDoesntHave('blocages', function ($query) use ($debut_at, $fin_at, $zone_id) {
            $query->betweenDates($debut_at, $fin_at)->where(function($query) use ($zone_id) {
                $query->where('zone_id', $zone_id)->orWhereNull('zone_id');
            });
        });
    }





    /*
     * Functions
     */


    public function tarif($nb_jours, $categorie, $quantite)
    {
        if ($quantite > $this->quantite_max) {
            throw new Exception("La quantité est supérieur à la quantité max de l'option.");
        }

        $duree_pour_calcul = $nb_jours;
        if ($this->jour_fact_max !== null and $nb_jours >= $this->jour_fact_max) {
            $duree_pour_calcul = $this->jour_fact_max;
        }

        switch ($this->type) {
            case 'defaut':
            case 'choix vehicule':
            case 'navette':
                $montant = $this->montant;
                break;

            case 'franchise':
                if (!$categorie) {
                    throw new Exception("La catégorie n'est pas défini.");
                }
                if ($categorie->franchise_rachat === null) {
                    throw new Exception("La catégorie ".$categorie->nom." n'accepte pas le rachat de franchise.");
                }
                $montant = $categorie->franchise_rachat;
                break;

            default:
                throw new Exception("Le type d'option n'existe pas.");
                break;
        }

        switch ($this->tarification) {
            case 'forfait':
                $tarif = $montant * $quantite;
                break;

            case 'jour':
                $tarif = $montant * $quantite * $duree_pour_calcul;
                break;

            case 'agence':
                $tarif = null;
                break;

            default:
                throw new Exception("Le type de tarification n'existe pas.");
                break;
        }

        if ($this->gratuit_apres !== null and $duree_pour_calcul >= $this->gratuit_apres) {
            $tarif = 0;
        }
        return $tarif;
    }




    /*
     * Accessors & Mutators
     */

    public function getIsTarificationAgenceAttribute()
    {
        return $this->tarification === 'agence';
    }

    public function getIsObligatoireAttribute()
    {
        return in_array($this->type, self::$TYPE_OBLIGATOIRES);
    }
    

}
