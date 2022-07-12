<?php

namespace Ipsum\Reservation\app\Panier;


use Carbon\Carbon;
use Illuminate\Support\Collection;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Ipsum\Reservation\app\Models\Lieu\Lieu;
use Ipsum\Reservation\app\Models\Reservation\Modalite;
use Ipsum\Reservation\app\Models\Tarif\Duree;
use Ipsum\Reservation\app\Models\Tarif\Saison;
use Ipsum\Reservation\app\Panier\Concerns\Sessionable;

class Devis
{
    use Sessionable;

    protected ?Carbon $debut_at = null;

    protected ?Carbon $fin_at = null;

    protected ?Lieu $lieu_debut = null;

    protected ?Lieu $lieu_fin = null;

    protected Collection $saisons;

    protected Duree $duree;

    protected Categorie $categorie;

    protected Modalite $modalite;

    protected float $total;




    public function setRecherche(array $inputs): void
    {
        $this->setLieuDebut(Lieu::find($inputs['debut_lieu_id']));
        $this->setLieuFin(Lieu::find($inputs['fin_lieu_id']));
        $this->setDebutAt(Carbon::createFromFormat('d/m/Y H:i', $inputs['debut_at']));
        $this->setFinAt(Carbon::createFromFormat('d/m/Y H:i', $inputs['fin_at']));
    }


    public function calculer(bool $load_tarifs = true, bool $without_prestations_optionnelles = false): self
    {
        if ($load_tarifs) {
            // Permet de ne pas refaire toutes les requêtes dans le cas de la liste
            $this->loadTarifs();
        }

        $this->montant_base = $this->_calculerTarif();

        /*$total_options = $without_options ? 0 : $this->_calculerOptions($categorie);
        $taxe_aeroport = $this->_calculerTaxeAeroport();*/

        /*$remise;
        if (isset($this->_promotions_object['reduction'])) {
            $remise = $this->_promotions_object['reduction']->lignes->first()->reduction;
            $this->_promotions_object['reduction']->reduction = floatval($remise);
        }*/

        // Calcul total
        $this->total = $this->montant_base /*+ $total_options - $remise*/;

        return $this;
    }

    /**
     * Calcul du montant de base
     * @desc Le calcul se fait sur toutes les saisons en prenant la duree total comme base de calcul pour les tranches
     */
    protected function _calculerTarif(): float
    {
        $total = $duree_total = 0;
        foreach ($this->saisons as $saison) {
            /* @var $saison Saison */

            $tarif = $this->categorie->tarifs()
                ->where('duree_id', $this->duree->id)
                ->where('saison_id', $saison->id)
                ->where('modalite_paiement_id', $this->modalite->id)
                ->first();

            if ($tarif === null) {
                throw new PanierException(_('Aucun montant trouvé pour la catégorie : ').$this->categorie->nom, PanierException::CATEGORIE_CODE);
            }

            $total += $tarif->montant * $saison->getDuree($this->debut_at, $this->fin_at);
        }

        return $total;
    }


    public function loadTarifs(): self
    {
        $this->saisons = Saison::getByDates($this->debut_at, $this->fin_at);
        $this->duree = Duree::findByNbJours($this->getNbJours());

        return $this;
    }


    public function hasPromotions(): bool
    {
        // TODO

        return true;
    }

    /**
     * @return int
     */
    public function getNbJours(): int
    {
        return \Ipsum\Reservation\app\Models\Reservation\Reservation::calculDuree($this->debut_at, $this->fin_at);
    }

    /**
     * @return Carbon|null
     */
    public function getDebutAt(): ?Carbon
    {
        return $this->debut_at;
    }

    /**
     * @param Carbon $debut_at
     */
    public function setDebutAt(Carbon $debut_at): void
    {
        $this->debut_at = $debut_at;
    }

    /**
     * @return Carbon|null
     */
    public function getFinAt(): ?Carbon
    {
        return $this->fin_at;
    }

    /**
     * @param Carbon $fin_at
     */
    public function setFinAt(Carbon $fin_at): void
    {
        $this->fin_at = $fin_at;
    }

    /**
     * @return Lieu|null
     */
    public function getLieuDebut(): ?Lieu
    {
        return $this->lieu_debut;
    }

    /**
     * @param Lieu $lieu_debut
     */
    public function setLieuDebut(Lieu $lieu_debut): void
    {
        $this->lieu_debut = $lieu_debut;
    }

    /**
     * @return Lieu|null
     */
    public function getLieuFin(): ?Lieu
    {
        return $this->lieu_fin;
    }

    /**
     * @param Lieu $lieu_fin
     */
    public function setLieuFin(Lieu $lieu_fin): void
    {
        $this->lieu_fin = $lieu_fin;
    }

    /**
     * @return Categorie
     */
    public function getCategorie(): Categorie
    {
        return $this->categorie;
    }

    /**
     * @param Categorie $categorie
     */
    public function setCategorie(Categorie $categorie): void
    {
        $this->categorie = $categorie;
    }

    /**
     * @return Modalite
     */
    public function getModalite(): Modalite
    {
        return $this->modalite;
    }

    /**
     * @param Modalite $modalite
     */
    public function setModalite(Modalite $modalite): void
    {
        $this->modalite = $modalite;
    }

    /**
     * @return float
     */
    public function getTotal(): float
    {
        return $this->total;
    }




    public function clone(): self
    {
        return clone $this;
    }


}
