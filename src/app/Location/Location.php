<?php

namespace Ipsum\Reservation\app\Location;


use Carbon\Carbon;
use Illuminate\Support\Collection;
use Ipsum\Reservation\app\Location\Exceptions\PrixInvalide;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Ipsum\Reservation\app\Models\Lieu\Lieu;
use Ipsum\Reservation\app\Models\Reservation\Modalite;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Ipsum\Reservation\app\Models\Tarif\Duree;
use Ipsum\Reservation\app\Models\Tarif\Saison;
use Ipsum\Reservation\app\Location\Concerns\Sessionable;

class Location
{
    use Sessionable;

    protected ?Carbon $debut_at = null;

    protected ?Carbon $fin_at = null;

    protected ?Lieu $lieu_debut = null;

    protected ?Lieu $lieu_fin = null;

    protected Categorie $categorie;

    protected Modalite $modalite;



    protected Collection $saisons;

    protected Duree $duree;


    public function setRecherche(array $inputs): void
    {
        $this->setLieuDebut($inputs['debut_lieu_id']);
        $this->setLieuFin($inputs['fin_lieu_id']);
        $this->setDebutAt($inputs['debut_at']);
        $this->setFinAt($inputs['fin_at']);
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
    public function devis()
    {
        if ($this->saisons === null or $this->duree === null) {
            // Permet de ne pas refaire toutes les requêtes dans le cas de la liste
            $this->loadTarifs();
        }

        return new Devis($this);
    }


    /**
     * @param Collection $categories
     * @param Collection $modalites
     * @return DevisCollection
     * @throws Exceptions\PrixInvalide
     * @throws \Ipsum\Reservation\app\Models\Tarif\TarifException
     */
    public function createDevisCollection(Collection $categories, Collection $modalites) {
        $devis = [];
        foreach ($categories as $categorie) {
            foreach ($modalites as $modalite) {
                try {
                    $devis[] = $this->clone()->setCategorie($categorie)->setModalite($modalite)->devis()->calculer();
                } catch (PrixInvalide $exception) { }
            }
        }

        return new DevisCollection($devis);
    }






    public function getNbJours(): int
    {
        return Reservation::calculDuree($this->debut_at, $this->fin_at);
    }

    public function getDebutAt(): ?Carbon
    {
        return $this->debut_at;
    }

    public function setDebutAt(string $debut_at): void
    {
        // TODO mettre format en config
        $this->debut_at = Carbon::createFromFormat('d/m/Y H:i', $debut_at);
    }

    public function getFinAt(): ?Carbon
    {
        return $this->fin_at;
    }

    public function setFinAt(string $fin_at): void
    {
        // TODO mettre format en config
        $this->fin_at = Carbon::createFromFormat('d/m/Y H:i', $fin_at);
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

    public function getCategorie(): Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(Categorie $categorie): Location
    {
        $this->categorie = $categorie;
        return $this;
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


    public function getInstance(): self
    {
        return $this;
    }

    public function clone(): self
    {
        return clone $this;
    }
}
