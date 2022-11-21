<?php

namespace Ipsum\Reservation\app\Location;


use Illuminate\Database\Eloquent\Builder;
use Ipsum\Reservation\app\Location\Exceptions\PrixInvalide;
use Ipsum\Reservation\app\Models\Reservation\Etat;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Ipsum\Reservation\app\Models\Tarif\Saison;

class Devis
{

    protected float $montant_base;
    protected float $total;

    protected Location $location;

    protected ?PrestationCollection $prestations = null;
    protected ?PromotionCollection $promotions = null;



    public function __construct(Location $location, bool $without_prestations_optionnelles = false)
    {
        $this->location = $location;
        $this->prestations = $without_prestations_optionnelles ? new PrestationCollection() : $this->location->getPrestations();
    }



    public function calculer(): self
    {
        $this->montant_base = $this->_calculerTarif();


        $this->_loadPrestationsObligatoire()->_calculerPrestations();

        $total_prestations = $this->prestations->montantTotal();

        $this->_loadPromotions();

        // Calcul total
        $this->total = $this->montant_base + $total_prestations + $this->location->getCondition()->frais - $this->promotions->totalReductions();

        if (!$this->total) {
            throw new PrixInvalide(_('Aucun montant trouvé pour la catégorie : ').$this->location->getCategorie()->nom);
        }

        return $this;
    }


    /**
     * Calcul du montant de base
     * @desc Le calcul se fait sur toutes les saisons en prenant la duree total comme base de calcul pour les tranches.
     *       Sauf pour les forfaits pris sur le tarif de la première saison
     */
    protected function _calculerTarif(): float
    {
        $total = $duree_total = 0;
        foreach ($this->location->getSaisons() as $saison) {
            /* @var $saison Saison */

            $tarif = $this->location->getCategorie()->tarifs()
                ->where('duree_id', $this->location->getDuree()->id)
                ->where('saison_id', $saison->id)
                ->where(function (Builder $query) {
                    $query->where('condition_paiement_id', $this->location->getCondition()->id)->orWhereNull('condition_paiement_id');
                })
                ->first();


            if ($tarif === null or $tarif->montant === null) {
                throw new PrixInvalide(_('Aucun montant trouvé pour la catégorie : ').$this->location->getCategorie()->nom);
            }

            if ($this->location->getDuree()->is_forfait) {
                $total = $tarif->montant;
                break;
            } else {
                $total += $tarif->montant * $saison->getDuree($this->location->getDebutAt(), $this->location->getFinAt());
            }
        }

        return $total;
    }

    /**
     * Calcul des prestations
     * @desc
     */
    protected function _calculerPrestations(): self
    {
        $this->prestations->calculer($this->getLocation()->getNbJours(), $this->getLocation()->getCategorie(), $this->getLocation()->getLieuDebut());

        return $this;
    }

    protected function _loadPrestationsObligatoire(): self
    {
        $prestations = Prestation::withoutBlocage($this->getLocation()->getDebutAt(), $this->getLocation()->getFinAt())
            ->obligatoire()
            ->condition($this->getLocation()->getCategorie(), $this->getLocation()->getLieuDebut(), $this->getLocation()->getLieuFin(), $this->getLocation()->getDebutAt(), $this->getLocation()->getFinAt(), $this->getLocation()->getAgeRecherche())
            ->orderBy('order', 'asc')
            ->get();

        $this->prestations = $this->prestations->merge($prestations)->unique('id');

        return $this;
    }

    public function getPrestations(): ?PrestationCollection
    {
        return $this->prestations;
    }

    protected function _loadPromotions(): self
    {
        $promotions = Promotion::conditionScope($this)->get();

        // Posibilité de ne pas cumuler les promos ?
        $promotion_collection = new PromotionCollection($promotions);
        $this->promotions = $promotion_collection->unique('id')->calculer($this);

        return $this;
    }

    public function getPromotions(): ?PromotionCollection
    {
        return $this->promotions;
    }

    public function hasPromotions(): bool
    {
        return $this->promotions->count() != 0;
    }



    /**
     * @return float
     */
    public function getTotal(): float
    {
        return $this->total;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function getMontantBase(): float
    {
        return $this->montant_base;
    }



    public function updateOrCreateReservation(): Reservation
    {

        $reservation = Reservation::notConfirmed()->updateOrCreate([
            'id' => $this->getLocation()->getReservationId()
        ],
        [
            'id' => $this->getLocation()->getReservationId(),
            'etat_id' => Etat::NON_VALIDEE_ID,
            'condition_paiement_id' => $this->getLocation()->getCondition()->id,
            'client_id' => auth()->check() ? auth()->id() : null,
            'civilite' => $this->getLocation()->getCivilite(),
            'nom' => $this->getLocation()->getNom(),
            'prenom' => $this->getLocation()->getPrenom(),
            'email' => $this->getLocation()->getEmail(),
            'telephone' => $this->getLocation()->getTelephone(),
            'adresse' => $this->getLocation()->getAdresse(),
            'cp' => $this->getLocation()->getCp(),
            'ville' => $this->getLocation()->getVille(),
            'pays_id' => $this->getLocation()->getPays()->id,
            'naissance_at' => $this->getLocation()->getNaissanceAt(),
            'naissance_lieu' => $this->getLocation()->getNaissanceLieu(),
            'permis_numero' => $this->getLocation()->getPermisNumero(),
            'permis_at' => $this->getLocation()->getPermisAt(),
            'permis_delivre' => $this->getLocation()->getPermisDelivre(),
            'observation' => $this->getLocation()->getObservation(),
            'custom_fields' => $this->getLocation()->getCustomFields(),
            'categorie_id' => $this->getLocation()->getCategorie()->id,
            'caution' => $this->getLocation()->getCategorie()->caution,
            'franchise' => $this->getLocation()->getCategorie()->franchise,
            'debut_at' => $this->getLocation()->getDebutAt(),
            'fin_at' => $this->getLocation()->getFinAt(),
            'debut_lieu_id' => $this->getLocation()->getLieuDebut()->id,
            'fin_lieu_id' => $this->getLocation()->getLieuFin()->id,
            'montant_base' => $this->montant_base,
            'prestations' => $this->getPrestations()->toArray(), // Ne pas prendre dans Location sinon il n'y aura pas les obligatoire
            'promotions' => $this->getPromotions()->toArray(),
            'echeancier' => $this->getLocation()->getCondition()->echeancier($this->total),
            'total' => $this->total,
            'montant_paye' => null,
        ]);

        return $reservation;
    }


}
