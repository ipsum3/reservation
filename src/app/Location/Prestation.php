<?php


namespace Ipsum\Reservation\app\Location;


use Carbon\CarbonInterface;
use Exception;
use Ipsum\Reservation\app\Models\Lieu\Lieu;

class Prestation extends \Ipsum\Reservation\app\Models\Prestation\Prestation
{

    protected int $quantite = 1;

    protected ?float $tarif = null;

    /**
     * @var mixed
     */
    //protected $value = null;


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
    public function calculer(int $nb_jours, Categorie $categorie, Lieu $lieu_debut, Lieu $lieu_fin, CarbonInterface $debut_at, CarbonInterface $fin_at): self
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


        if ($this->is_obligatoire) {

            // Si une prestation de type frais à une condition sur jour, heure_max, heure_min, lieux,
            // vérifier la validité des conditions sur le début et sur la fin
            // pour voir s'il ne faut pas le facturer 2 fois la prestation

            $value = 0;
            if (
                $this->condition != 'retour' and
                ($this->jour === null or $this->jour == $debut_at->dayOfWeekWithFerie($lieu_debut)) and
                ($this->heure_min === null or $this->heure_min < $debut_at->toTimeString()) and
                ($this->heure_max === null or $this->heure_max > $debut_at->toTimeString()) and
                (!$this->lieux->count() or $this->lieux->contains($lieu_debut->id))
            ) {

                $prestation_lieu_debut = $this->lieux->find($lieu_debut->id);
                if ($prestation_lieu_debut) {
                    $value += $montant + $prestation_lieu_debut->pivot->montant;
                } else {
                    $value += $montant;
                }

            }

            if (
                $this->condition != 'depart' and
                ($this->jour === null or $this->jour == $fin_at->dayOfWeekWithFerie($lieu_fin)) and
                ($this->heure_min === null or $this->heure_min < $fin_at->toTimeString()) and
                ($this->heure_max === null or $this->heure_max > $fin_at->toTimeString()) and
                (!$this->lieux->count() or $this->lieux->contains($lieu_fin->id))
            ) {

                $prestation_lieu_fin = $this->lieux->find($lieu_fin->id);
                if ($prestation_lieu_fin) {
                    $value += $montant + $prestation_lieu_fin->pivot->montant;
                } else {
                    $value += $montant;
                }

            }

            $montant = $value;

        } else {

            $prestation_lieu = $this->lieux()->find($lieu_debut->id);
            if ($prestation_lieu) {
                $montant += $prestation_lieu->pivot->montant;
            }

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

    public static function tarifLibelle($tarif, $tarification): string
    {
        if ($tarification == self::TARIFICATION_AGENCE) {
            $libelle = 'en agence';
        } elseif (empty($tarif)) {
            $libelle = 'gratuit';
        } else {
            $libelle = number_format($tarif, ((int) $tarif == $tarif ? 0 : 2), ',', ' ').' €';
        }

        return $libelle;
    }

    public function getTarifLibelle(): string
    {
        return self::tarifLibelle($this->tarif, $this->tarification);
    }
}