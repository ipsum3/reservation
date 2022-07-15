<?php

namespace Ipsum\Reservation\app\Location;


use Carbon\Carbon;
use Illuminate\Support\Collection;
use Ipsum\Reservation\app\Location\Exceptions\PrixInvalide;
use Ipsum\Reservation\app\Models\Lieu\Lieu;
use Ipsum\Reservation\app\Models\Reservation\Modalite;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Ipsum\Reservation\app\Models\Tarif\Duree;
use Ipsum\Reservation\app\Models\Tarif\Saison;
use Ipsum\Reservation\app\Location\Concerns\Sessionable;

class Location
{
    use Sessionable;

    protected Carbon $debut_at;

    protected Carbon $fin_at;

    protected ?Lieu $lieu_debut = null;

    protected ?Lieu $lieu_fin = null;

    protected ?Categorie $categorie = null;

    protected ?Modalite $modalite = null;

    protected PrestationCollection $prestations;



    protected ?Collection $saisons = null;

    protected ?Duree $duree = null;



    protected ?int $reservation_id = null;


    public function __construct()
    {
        $this->prestations = new PrestationCollection();
        $this->debut_at = Carbon::now()->addDays(7);
        $this->fin_at = Carbon::now()->addDays(14);

    }


    public function setRecherche(array $inputs): self
    {
        $this->setLieuDebut($inputs['debut_lieu_id']);
        $this->setLieuFin($inputs['fin_lieu_id']);
        $this->setDebutAt($inputs['debut_at']);
        $this->setFinAt($inputs['fin_at']);

        return $this;
    }


    /**
     * @return $this
     * @throws \Ipsum\Reservation\app\Models\Tarif\TarifException
     */
    public function loadTarifs(): self
    {
        $this->saisons = Saison::getByDates($this->debut_at, $this->fin_at);
        $this->duree = Duree::findByNbJours($this->getNbJours());

        return $this;
    }

    /**
     * @return Devis
     * @throws \Ipsum\Reservation\app\Models\Tarif\TarifException
     */
    public function devis(bool $without_prestations_optionnelles = false)
    {
        if ($this->saisons === null or $this->duree === null) {
            // Permet de ne pas refaire toutes les requêtes dans le cas de la liste
            $this->loadTarifs();
        }

        return new Devis($this, $without_prestations_optionnelles);
    }


    /**
     * @param Collection $categories
     * @param Collection $modalites
     * @return CategorieCollection
     * @throws \Ipsum\Reservation\app\Models\Tarif\TarifException
     */
    public function createCategorieCollection(Collection $categories, Collection $modalites): CategorieCollection
    {
        $categorie_collection = [];
        foreach ($categories as $categorie) {
            /* @var $categorie Categorie */
            foreach ($modalites as $modalite) {
                try {
                    $categorie->devis->add($this->clone()->setCategorie($categorie)->setModalite($modalite)->devis(true)->calculer(true));
                } catch (PrixInvalide $exception) { }
            }
            if ($categorie->devis->count()) {
                $categorie_collection[] = $categorie;
            }
        }

        return new CategorieCollection($categorie_collection);
    }


    /**
     * @param Collection $prestations
     * @return PrestationCollection
     */
    public function createPrestationCollection(Collection $prestations): PrestationCollection
    {
        $prestation_collection = [];
        foreach ($prestations as $prestation) {
            /* @var $prestation Prestation */
            try {
                $prestation->setQuantite(1)->calculer($this->getNbJours(), $this->categorie, $this->lieu_debut);
                $prestation_collection[] = $prestation;
            } catch (\Exception $exception) { }
        }

        return new PrestationCollection($prestation_collection);
    }


    public function getNbJours(): int
    {
        return Reservation::calculDuree($this->debut_at, $this->fin_at);
    }

    public function getDebutAt(): Carbon
    {
        return $this->debut_at;
    }

    public function setDebutAt(string $debut_at): void
    {
        $this->debut_at = Carbon::createFromFormat(config('ipsum.reservation.recherche.date_format'), $debut_at);
    }

    public function getFinAt(): Carbon
    {
        return $this->fin_at;
    }

    public function setFinAt(string $fin_at): void
    {
        $this->fin_at = Carbon::createFromFormat(config('ipsum.reservation.recherche.date_format'), $fin_at);
    }

    public function getLieuDebut(): ?Lieu
    {
        return $this->lieu_debut;
    }

    public function setLieuDebut(int $lieu_debut): void
    {
        $this->lieu_debut = Lieu::findOrFail($lieu_debut);
    }

    public function getLieuFin(): ?Lieu
    {
        return $this->lieu_fin;
    }

    public function setLieuFin(int $lieu_fin): void
    {
        $this->lieu_fin = Lieu::findOrFail($lieu_fin);
    }

    public function hasCategorie(): bool
    {
        return $this->categorie !== null;
    }

    public function getCategorie(): Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(Categorie $categorie): Location
    {
        $this->categorie = $categorie;
        return $this;
    }

    public function hasModalite(): bool
    {
        return $this->modalite !== null;
    }

    public function getModalite(): Modalite
    {
        return $this->modalite;
    }

    public function setModalite(Modalite $modalite): Location
    {
        $this->modalite = $modalite;
        return $this;
    }

    public function getSaisons(): Collection
    {
        return $this->saisons;
    }

    public function getDuree(): Duree
    {
        return $this->duree;
    }

    public function getPrestations(): PrestationCollection
    {
        return $this->prestations;
    }

    public function setPrestations(?array $prestations): self
    {
        $this->prestations = new PrestationCollection();
        if ($prestations !== null) {
            foreach ($prestations as $prestation) {
                if (isset($prestation['has'])) {
                    $quantite = (int) isset($prestation['quantite']) ? $prestation['quantite'] : 1;
                    $presta = Prestation::find($prestation['has']);
                    $presta->setQuantite($quantite);
                    $this->prestations->add($presta);
                }
            }
        }

        return $this;
    }

    public function getReservationId(): ?int
    {
        return $this->reservation_id;
    }

    public function setReservationId(int $reservation_id): self
    {
        $this->reservation_id = $reservation_id;
        return $this;
    }

    public function getInstance(): self
    {
        return $this;
    }

    public function clone(): self
    {
        return clone $this;
    }

}
