<?php

namespace Ipsum\Reservation\app\Location;


use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Ipsum\Reservation\app\Classes\Carbon;
use Ipsum\Reservation\app\Location\Exceptions\PrixInvalide;
use Ipsum\Reservation\app\Models\Categorie\Type;
use Ipsum\Reservation\app\Models\Lieu\Lieu;
use Ipsum\Reservation\app\Models\Reservation\Condition;
use Ipsum\Reservation\app\Models\Reservation\Pays;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Ipsum\Reservation\app\Models\Tarif\Duree;
use Ipsum\Reservation\app\Models\Tarif\Saison;
use Ipsum\Reservation\app\Location\Concerns\Sessionable;

class Location
{
    use Sessionable;

    protected CarbonInterface $debut_at;

    protected CarbonInterface $fin_at;

    protected ?Lieu $lieu_debut = null;

    protected ?Lieu $lieu_fin = null;

    protected ?string $code_promo = null;

    protected ?int $age_recherche = null;

    protected ?Type $type = null;

    protected ?Categorie $categorie = null;

    protected ?Condition $condition = null;

    protected PrestationCollection $prestations;



    protected ?Collection $saisons = null;

    protected ?Duree $duree = null;


    protected ?string $civilite = null;
    protected ?string $nom = null;
    protected ?string $prenom = null;
    protected ?string $email = null;
    protected ?string $telephone = null;
    protected ?string $adresse = null;
    protected ?string $cp = null;
    protected ?string $ville = null;
    protected ?Pays $pays = null;
    protected ?CarbonInterface $naissance_at = null;
    protected ?string $naissance_lieu = null;
    protected ?string $permis_numero = null;
    protected ?CarbonInterface $permis_at = null;
    protected ?string $permis_delivre = null;
    protected ?string $observation = null;
    protected ?array $custom_fields = null;


    protected ?int $reservation_id = null;


    public function __construct()
    {
        $this->prestations = new PrestationCollection();
        $this->debut_at = Carbon::now()->addDays(7)->hour(config('ipsum.reservation.recherche.heure_debut') ?? 16)->minute(0)->second(0)->micro(0);
        $this->fin_at = Carbon::now()->addDays(14)->hour(config('ipsum.reservation.recherche.heure_fin') ?? 16)->minute(0)->second(0)->micro(0);

    }


    public function setRecherche(array $inputs): self
    {
        $this->setLieuDebut($inputs['debut_lieu_id']);
        $this->setLieuFin($inputs['fin_lieu_id']);
        $this->setDebutAt($inputs['debut_at']);
        $this->setFinAt($inputs['fin_at']);
        $this->setType($inputs['type'] ?? null);
        $this->setCodePromo($inputs['code_promo'] ?? null);
        $this->setAgeRecherche($inputs['age'] ?? null);

        return $this;
    }


    public function setInformations(array $inputs): self
    {
        $this->setCivilite($inputs['civilite'] ?? null);
        $this->setNom($inputs['nom']);
        $this->setPrenom($inputs['prenom'] ?? null);
        $this->setEmail($inputs['email']);
        $this->setTelephone($inputs['telephone'] ?? null);
        $this->setAdresse($inputs['adresse'] ?? null);
        $this->setCp($inputs['cp'] ?? null);
        $this->setVille($inputs['ville'] ?? null);
        $this->setPays($inputs['pays_id'] ?? null);
        $this->setNaissanceAt($inputs['naissance_at'] ?? null);
        $this->setNaissanceLieu($inputs['naissance_lieu'] ?? null);
        $this->setPermisNumero($inputs['permis_numero'] ?? null);
        $this->setPermisAt($inputs['permis_at']  ?? null);
        $this->setPermisDelivre($inputs['permis_delivre'] ?? null);
        $this->setObservation($inputs['observation'] ?? null);
        $this->setCustomFields($inputs['custom_fields'] ?? null);

        return $this;
    }


    /**
     * @return $this
     * @throws \Ipsum\Reservation\app\Models\Tarif\TarifException
     */
    public function loadTarifs(): self
    {
        $this->saisons = Saison::getByDates($this->debut_at, $this->fin_at);
        $this->duree = Duree::findByNbJours($this->getNbJours(), $this->debut_at, $this->fin_at);

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
     * @param Collection $conditions
     * @return CategorieCollection
     * @throws \Ipsum\Reservation\app\Models\Tarif\TarifException
     */
    public function createCategorieCollection(Collection $categories, Collection $conditions): CategorieCollection
    {
        $categorie_collection = [];
        foreach ($categories as $categorie) {
            /* @var $categorie Categorie */
            foreach ($conditions as $condition) {
                try {
                    $categorie->devis->add($this->clone()->setCategorie($categorie)->setCondition($condition)->devis(true)->calculer());
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
                $prestation->setQuantite(1)->calculer($this->getNbJours(), $this->categorie, $this->lieu_debut, $this->lieu_fin, $this->debut_at, $this->fin_at);
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

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?int $type_id): void
    {
        $this->type = is_null($type_id) ? null : Type::findOrFail($type_id);
    }

    public function getCodePromo(): ?string
    {
        return $this->code_promo;
    }

    public function setCodePromo(?string $code_promo): void
    {
        $this->code_promo = $code_promo;
    }

    public function getAgeRecherche(): ?int
    {
        return  $this->age_recherche;
    }

    public function setAgeRecherche($age): void
    {
        $this->age_recherche = $age;
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

    public function hasCondition(): bool
    {
        return $this->condition !== null;
    }

    public function getCondition(): Condition
    {
        return $this->condition;
    }

    public function setCondition(Condition $condition): Location
    {
        $this->condition = $condition;
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
                if (!empty($prestation['quantite']) ) {
                    $quantite = (int) $prestation['quantite'];
                    $presta = Prestation::find($prestation['id']);
                    $presta->setQuantite($quantite);
                    $this->prestations->add($presta);
                }
            }
        }

        return $this;
    }

    public function getCivilite(): ?string
    {
        return $this->civilite;
    }

    public function setCivilite(?string $civilite): void
    {
        $this->civilite = $civilite;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): void
    {
        $this->prenom = $prenom;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): void
    {
        $this->telephone = $telephone;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): void
    {
        $this->adresse = $adresse;
    }

    public function getCp(): ?string
    {
        return $this->cp;
    }

    public function setCp(?string $cp): void
    {
        $this->cp = $cp;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): void
    {
        $this->ville = $ville;
    }

    public function getPays(): ?Pays
    {
        return $this->pays;
    }

    public function setPays(?int $pays_id): void
    {
        $this->pays = Pays::find($pays_id);
    }

    public function getNaissanceAt(): ?CarbonInterface
    {
        return $this->naissance_at;
    }

    public function setNaissanceAt(?string $naissance_at): void
    {
        $this->naissance_at = $naissance_at ? Carbon::createFromFormat(config('ipsum.reservation.recherche.jour_format'), $naissance_at) : null;
    }

    public function getDateNaissanceMinimum()
    {
        return $this->debut_at->clone()->subYears($this->getCategorie()->age_minimum);
    }

    public function age(): ?int
    {
        return $this->naissance_at?->age;
    }

    public function getNaissanceLieu(): ?string
    {
        return $this->naissance_lieu;
    }

    public function setNaissanceLieu(?string $naissance_lieu): void
    {
        $this->naissance_lieu = $naissance_lieu;
    }

    public function getPermisNumero(): ?string
    {
        return $this->permis_numero;
    }

    public function setPermisNumero(?string $permis_numero): void
    {
        $this->permis_numero = $permis_numero;
    }

    public function getPermisAt(): ?CarbonInterface
    {
        return $this->permis_at;
    }

    public function setPermisAt(?string $permis_at): void
    {
        $this->permis_at = $permis_at ? Carbon::createFromFormat(config('ipsum.reservation.recherche.jour_format'), $permis_at): null;
    }

    public function getDatePermisMinimum()
    {
        return $this->debut_at->clone()->subYears($this->getCategorie()->annee_permis_minimum);
    }

    public function getPermisDelivre(): ?string
    {
        return $this->permis_delivre;
    }

    public function setPermisDelivre(?string $permis_delivre): void
    {
        $this->permis_delivre = $permis_delivre;
    }

    public function getObservation(): ?string
    {
        return $this->observation;
    }

    public function setObservation(?string $observation): void
    {
        $this->observation = $observation;
    }

    public function getCustomFields(): ?array
    {
        return $this->custom_fields;
    }

    public function getCustomField($field)
    {
        return isset($this->custom_fields[$field]) ? $this->custom_fields[$field] : null;
    }

    public function setCustomFields(?array $custom_fields): void
    {
        $this->custom_fields = $custom_fields;
    }

    public function hasReservation(): bool
    {
        return $this->reservation_id === null ? false : true;
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
