<?php

namespace Ipsum\Reservation\app\Models\Reservation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Ipsum\Core\app\Models\BaseModel;
use App\Option\Option;
use App\Promotion\Promotion;
use App\Tarif\Duree;
use App\Tarif\Saison;
use App\Tarif\TarifException;
use Exception;
use Carbon\Carbon;
use Config;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Ipsum\Reservation\app\Models\Lieu\Lieu;
use Ipsum\Reservation\database\factories\ReservationFactory;
use Session;

class Reservation extends BaseModel
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $saisons;
    protected $duree;

    public $code_promo;

    public $_options_object;
    public $_promotions_object;

    const SESSION_ID = 'reservation';

    public static $type_paiement = array('ligne' => "Paiement en ligne", 'agence' => 'Paiement en agence');


    
    
    
    
    
    
    
    

    protected static function newFactory()
    {
        return ReservationFactory::new();
    }

    public static function boot()
    {
        parent::boot();

        self::created(function ($reservation) {
            // Génération de la référence
            $reservation->reference = str_pad($reservation->id, 6, "0", STR_PAD_LEFT);
            $reservation->save();
        });

        self::saving(function ($reservation) {

            if ($reservation->saisons) {
                // Code non exécuté en cas de traitement ipn ou en admin

                // formatage options
                $options = null;
                if ($reservation->_options_object !== null) {
                    foreach ($reservation->_options_object as $option) {
                        $options[$option->id] = [
                            'id' => $option->id,
                            'quantite' => $option->quantite,
                            'nom' => $option->nom,
                            'montant' => $option->montant,
                            'tarif' => $option->tarif,
                            'choix' => $option->choix,
                        ];
                    }
                }
                $reservation->attributes['options'] = $options === null ? null : json_encode($options);

                // formatage promotions
                $promotions = null;
                if ($reservation->hasPromotionsVisible) {
                    foreach ($reservation->promotionsVisible as $promotion) {
                        $promotions[$promotion->id] = [
                            'id' => $promotion->id,
                            'nom' => $promotion->nom,
                            'reference' => $promotion->reference,
                            'reduction' => $promotion->reduction,
                            'type' => $promotion->type,
                        ];
                    }
                }
                $reservation->attributes['promotions'] = $promotions === null ? null : json_encode($promotions);

                $reservation->debut_lieu_nom = $reservation->lieu_debut->nom;
                $reservation->fin_lieu_nom = $reservation->lieu_fin->nom;
                $reservation->categorie_nom = $reservation->categorie->nom;
                $reservation->franchise = $reservation->categorie->franchise;
            }

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
        return $this->belongsTo(Modalite::class);
    }

    public function pays()
    {
        return $this->belongsTo(Pays::class);
    }

    public function lieuDebut()
    {
        return $this->belongsTo(Lieu::class, 'debut_lieu_id')->withTrashed();
    }

    public function lieuFin()
    {
        return $this->belongsTo(Lieu::class, 'fin_lieu_id')->withTrashed();
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

    public function scopeConfirmee($query)
    {
        return $query->where(function ($query) {
            $query->where(function ($query) {
                $query->where('type', 'agence')->whereIn('etat_id', [Etat::NON_PAYEE_ID, Etat::PAYEE_ID]);
            })->orWhere(function ($query) {
                $query->where('type', 'ligne')->where('etat_id', Etat::PAYEE_ID);
            });
        });
    }

    public function scopeNonConfirmee($query)
    {
        return $query->where(function ($query) {
            $query->where(function ($query) {
                $query->where('type', 'agence')->whereNotIn('etat_id', [Etat::NON_PAYEE_ID, Etat::PAYEE_ID]);
            })->orWhere(function ($query) {
                $query->where('type', 'ligne')->where('etat_id', '<>', Etat::PAYEE_ID);
            });
        });
    }




    /*
     * Calcul du montant de la réservation
     */

    /**
     * Calcul du montant total
     * @param $load_tarifs boolean Cas de la liste, pour ne pas faire de requete inutile
     * @param $without_options boolean Calcul sans prendre en compte les options (cas de la liste des catégories)
     * @return $this object
     * @desc
     */
    public function calculer($load_tarifs = true, $without_options = false)
    {
        $categorie = $this->categorie;

        $this->_searchPromotions($categorie);

        if ($load_tarifs) {
            // Permet de ne pas refaire toutes les requêtes dans le cas de la liste
            $this->loadTarifs();
        }

        $this->total = $this->_calculerTarif($categorie);

        $total_options = $without_options ? 0 : $this->_calculerOptions($categorie);
        $taxe_aeroport = $this->_calculerTaxeAeroport();

        $remise = $remise_compte =  0;
        if (isset($this->_promotions_object['reduction'])) {
            $remise = $this->_promotions_object['reduction']->lignes->first()->reduction;
            $this->_promotions_object['reduction']->reduction = floatval($remise);
        }

        // Calcul total
        $this->total = $this->total + $total_options - $remise - $remise_compte + $taxe_aeroport;

        return $this;
    }

    /**
     * Calcul du montant de base
     * @param $categorie object En paramètre pour gérer le surclassement
     * @return float Montant de base
     * @throws Exception
     * @desc Le calcul se fait sur toutes les saisons en prenant la duree total comme base de calcul pour les tranches
     */
    protected function _calculerTarif($categorie)
    {

        if ($this->saisons === null) {
            throw new TarifException(_('Aucun tarif trouvé pour cette réservation.'));
        }

        $total = $duree_total = $this->montant_base = 0;
        foreach ($this->saisons as $saison) {

            $tarif = $categorie->tarifs()->where('duree_id', $this->duree->id)->where('saison_id', $saison->id)->where('type', $this->type)->first();

            if ($tarif === null) {
                throw new TarifException(_('Aucun montant trouvé pour cette catégorie.'), TarifException::CATEGORIE_CODE);
            }

            $total += $tarif->montant * $saison->getDuree($this->debut_at, $this->fin_at);

            //$duree_total += $saison->getDuree($this->debut_at, $this->fin_at);

        }

        $this->montant_base = $total;

        //dd($total, $this->nb_jours, $duree_total, $saison->getDuree($this->debut_at, $this->fin_at));

        return $total;
    }

    /**
     * Calcul des options
     * @param $categorie object En paramètre pour gérer le surclassement
     * @return float Montant total des options
     * @throws Exception
     * @desc
     */
    protected function _calculerOptions($categorie)
    {

        $total_options = 0;

        $this->checkOptions();

        if ($this->_options_object !== null) {
            foreach ($this->_options_object as $key => $option) {
                try {

                    $option->tarif = $option->montant($this->nb_jours, $categorie, $option->quantite);
                    $total_options += $option->tarif;

                } catch (Exception $e) {
                    // Option non disponible pour la catégorie ou pour la résa
                    unset($this->_options_object[$key]);
                }
            }
        }

        return $total_options;
    }

    public function checkOptions()
    {
        // Option obligatoire
        if ($this->lieuDebut->isNavette or $this->lieuFin->isNavette) {
            $option = Option::where('type', 'navette')->first();
            if ($option) {
                $option->quantite = 1;
                $this->_options_object[$option->id] = $option;
            }
        } elseif ($this->_options_object !== null) {
            foreach ($this->_options_object as $id => $option) {
                if ($option->type == 'navette') {
                    unset($this->_options_object[$id]);
                }
            }
        }

        if ($this->_options_object !== null) {
            foreach ($this->_options_object as $option) {
                if (!$this->acceptOption($option)) {
                    $this->delOption($option->id);
                }
            }
        }
    }

    public function acceptOption($option)
    {
        if ($option->type == 'choix vehicule' and !$this->categorie->choixModeles->count()) {
            return false;
        }
        if ($option->type == 'choix vehicule' and $this->type == \App\Tarif\Tarif::TYPE_AGENCE) {
            return false;
        }
        return true;
    }

    protected function _calculerTaxeAeroport()
    {
        $taxe_aeroport = 0;

        // On vérifie si le lieu de début ou de fin à une taxe aéroport
        if ($this->lieuDebut->has_taxe_aeroport) {
            $taxe_aeroport = $this->lieuDebut->calculerTaxeAeroport($this->nb_jours);
        } elseif ($this->lieuFin->has_taxe_aeroport) {
            $taxe_aeroport = $this->lieuFin->calculerTaxeAeroport($this->nb_jours);
        }

        $this->taxe_aeroport = $taxe_aeroport;

        return $taxe_aeroport;
    }

    public function loadTarifs()
    {
        $this->saisons = Saison::getByDates($this->debut_at, $this->fin_at);
        $this->duree = Duree::findByNbJours($this->nb_jours);
    }


    /**
     * Recherche les promotions applicables
     * @param $categorie object
     * @desc Si il y a 2 promos du même type la première sera écrasée
     */
    protected function _searchPromotions($categorie)
    {

        $options = $this->_options_object ? $this->_options_object : [];

        $promotions = Promotion::valide($this->debut_at, $this->fin_at, $this->lieuDebut->id, $this->code_promo)
        ->where(function ($query) use ($options, $categorie) {
            $query->orWhere(function ($query) use ($options, $categorie) {
                // Requete en 2 fois, bug Eloquent https://stackoverflow.com/questions/21930266/how-to-use-orwhere-in-wherehas-in-laravel ?
                $query->whereHas('lignes', function ($query) use ($categorie) {
                    $query->where('categorie_id', $categorie->id);
                })
                ->orWhereHas('lignes', function ($query) use ($options) {
                    $query->whereIn('option_id', array_keys($options));
                });
            });
        })
        ->with(['lignes' => function ($query) use ($options, $categorie) {
                $query->where('categorie_id', $categorie->id)
                    ->orWhereIn('option_id', array_keys($options));
        }])->groupBy('type')->get();

        $this->_promotions_object = null;
        foreach ($promotions as $promotion) {
            $this->_promotions_object[$promotion['type']] = $promotion;
        }

    }


    /*
     * Accessors & Mutators
     */

    public function getIsConfirmeeAttribute()
    {
        return in_array($this->etat_id, [Etat::PAYEE_ID]) or ($this->type == 'agence' and in_array($this->etat_id, [Etat::NON_PAYEE_ID, Etat::PAYEE_ID]));
    }

    public function getIsPayeeAttribute()
    {
        return in_array($this->etat_id, [Etat::PAYEE_ID]);
    }

    /*
     * S'il y a plus d'une heure entre le début et la fin, on rajoute une journée
     */
    public function getNbJoursAttribute()
    {
        return self::nbJours($this->debut_at, $this->fin_at);
    }

    public static function nbJours(Carbon $date_debut, Carbon $date_fin)
    {
        return $date_debut->diffInDays($date_fin->subMinutes(61)) + 1;
    }

    public function getHasTaxeAeroportAttribute()
    {
        return $this->lieuDebut->has_taxe_aeroport or $this->lieuFin->has_taxe_aeroport;
    }

    public function getOptionsAttribute()
    {
        return isset($this->attributes['options']) ? json_decode($this->attributes['options'], true) : null;
    }

    public function getOptionsObjectAttribute()
    {
        return $this->_options_object;
    }

    public function optionQuantite($option_id)
    {
        return isset($this->_options_object[$option_id]) ? $this->_options_object[$option_id]->quantite : null;
    }

    public function optionChoix($option_id)
    {
        return isset($this->_options_object[$option_id]) ? $this->_options_object[$option_id]->choix : null;
    }

    public function hasOptionType($type)
    {
        if ($this->_options_object === null) {
            return false;
        }
        foreach ($this->_options_object as $option) {
            if ($option->type == $type) {
                return true;
            }
        }
        return false;
    }

    public function delOption($option_id)
    {
        unset($this->_options_object[$option_id]);
    }

    public function getPromotionsAttribute()
    {
        return isset($this->attributes['promotions']) ? json_decode($this->attributes['promotions'], true) : null;
    }

    public function getPromotionsVisibleAttribute()
    {
        $promotions = null;
        if ($this->_promotions_object !== null) {
            foreach ($this->_promotions_object as $key => $promotion) {
                if ($promotion->reduction >= 0) {
                    $promotions[$key] = $promotion;
                }
            }
        }

        return $promotions;
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

    public function getAcompteAttribute()
    {
        return $this->total / $this->nb_jours;
    }

    public function getIsManuelleAttribute()
    {
        return $this->email === null;
    }

    public function getDeviseSymboleAttribute()
    {
        return $this->devise === 'usd' ? '$' : '€';
    }

    public function getDeviseCodeAttribute()
    {
        return $this->devise === 'usd' ? '840' : '978';
    }

    public function setDebutAtAttribute($value)
    {
        $this->attributes['debut_at'] = Carbon::createFromFormat('d/m/Y H:i', $value);
    }

    public function setFinAtAttribute($value)
    {
        $this->attributes['fin_at'] = Carbon::createFromFormat('d/m/Y H:i', $value);
    }

    public function setDebutLieuIdAttribute($value)
    {
        $this->attributes['debut_lieu_id'] = $value;
    }

    public function setFinLieuIdAttribute($value)
    {
        $this->attributes['fin_lieu_id'] = $value;
    }

    public function addOption($option, $quantite, $choix = null)
    {
        if (empty($quantite)) {
            return;
        }
        if (!is_object($option)) {
            $option = Option::find($option);
        }

        if ($option) {
            $option->quantite = $quantite;
            $option->choix = $choix;
            $this->_options_object[$option->id] = $option;
        }
    }

    public function addOptions($values, $choix = null)
    {
        $this->_options_object = null;

        if (!empty($values) and is_array($values)) {
            foreach ($values as $id => $quantite) {
                $this->addOption($id, $quantite, isset($choix[$id]) ? $choix[$id] : null);
            }
        }
    }

    public function setOptionsAttribute($values)
    {
        $this->addOptions($values);
    }

    public function setNaissanceAtAttribute($value)
    {
        $this->attributes['naissance_at'] = empty($value) ? null : Carbon::createFromFormat('d/m/Y', $value);
    }

    public function setPermisAtAttribute($value)
    {
        $this->attributes['permis_at'] = empty($value) ? null : Carbon::createFromFormat('d/m/Y', $value);
    }


    public function getDates()
    {
        return ['naissance_at', 'permis_at', 'debut_at', 'fin_at', 'created_at', 'updated_at'];
    }


    /*
     * Session
     */

    static public function hasSession()
    {
        return Session::has(self::SESSION_ID);
    }

    static public function newBySession()
    {
        if (self::hasSession()) {
            $resa = unserialize(Session::get(self::SESSION_ID));
            $resa->relations = [];  /* Suppression des relations */
            return $resa;
        } else {
            return new self;
        }
    }

    public function saveToSession()
    {
        return Session::put(self::SESSION_ID, serialize($this));
    }

    public function forget()
    {
        Session::forget(self::SESSION_ID);
    }

    public function replicate(array $except = null)
    {
        $reservation = parent::replicate($except);
        $reservation->etat_id = null;
        $reservation->reference = null;
        $reservation->_options_object = $this->_options_object;
        $reservation->_promotions_object = $this->_promotions_object;
        return $reservation;
    }
}
