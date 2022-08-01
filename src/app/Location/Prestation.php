<?php


namespace Ipsum\Reservation\app\Location;


use Exception;
use Ipsum\Reservation\app\Models\Lieu\Lieu;

class Prestation extends \Ipsum\Reservation\app\Models\Prestation\Prestation
{

    protected int $quantite = 1;

    protected ?float $tarif = null;

    /**
     * @var mixed
     */
    protected $value = null;


    /*
     * Functions
     */


    /**
     * @param int $nb_jours
     * @param Categorie $categorie
     * @param Lieu $lieu_debut
     * @return $this
     * @throws Exception
     */
    public function calculer(int $nb_jours, Categorie $categorie, Lieu $lieu_debut): self
    {
        if ($this->quantite > $this->quantite_max) {
            throw new Exception("La quantité est supérieur à la quantité max de la prestation.");
        }

        $duree_pour_calcul = $nb_jours;
        if ($this->jour_fact_max !== null and $nb_jours >= $this->jour_fact_max) {
            $duree_pour_calcul = $this->jour_fact_max;
        }

        $montant = $this->montant;

        $prestation_categorie = $this->categories()->find($categorie->id);
        if ($prestation_categorie) {
            $montant += $prestation_categorie->pivot->montant;
        }

        $prestation_lieu = $this->lieux()->find($lieu_debut->id);
        if ($prestation_lieu) {
            $montant += $prestation_lieu->pivot->montant;
        }

        switch ($this->tarification) {
            case 'forfait':
                $tarif = $montant * $this->quantite;
                break;

            case 'jour':
                $tarif = $montant * $this->quantite * $duree_pour_calcul;
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

        $this->tarif = $tarif;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;
        return $this;
    }

    public function getTarif(): ?float
    {
        return $this->tarif;
    }

    public function getTarifLibelle(): string
    {
        if ($this->is_tarification_agence) {
            $libelle = 'en agence';
        } elseif (empty($this->tarif)) {
            $libelle = 'gratuit';
        } else {
            $libelle = number_format($this->tarif, ((int) $this->tarif == $this->tarif ? 0 : 2), ',', '&nbsp;').' €';
        }

        return $libelle;
    }
}