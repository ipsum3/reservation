<?php

namespace Ipsum\Reservation\app\Models\Prestation;

use App\Article\Translatable;
use Ipsum\Admin\Concerns\Sortable;
use Ipsum\Core\app\Models\BaseModel;
use Exception;
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Config;

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


    public function montant($nb_jours, $categorie, $quantite)
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
