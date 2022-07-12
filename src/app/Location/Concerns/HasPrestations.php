<?php

namespace Ipsum\Reservation\app\Panier\Concerns;


use Ipsum\Reservation\app\Models\Prestation\Prestation;
use Session;

trait HasPrestations
{

    protected $prestations;
    

    /**
     * Calcul des prestations
     * @desc
     */
    protected function _calculerPrestations(int $type = null): void
    {

    }

    protected function _checkPrestations(): void
    {
        $this->_loadPrestationsObligatoire();

        if ($this->prestations !== null) {
            foreach ($this->prestations as $prestation) {
                if (!$this->acceptPrestation($prestation)) {
                    $this->delPrestation($prestation->id);
                }
            }
        }
    }

    protected function _loadPrestationsObligatoire(): void
    {
        if ($this->lieuDebut->id != $this->lieuFin->id) {
            $prestation = Prestation::where('type', 'abandon')->first();
            $this->addPrestation($prestation, 1);
        } else {
            $this->delPrestation(null, 'abandon');
        }
    }

    public function acceptPrestations($prestation): bool
    {
        /*if ($prestation->type == 'choix vehicule' and
            // Il y a des modéles sur cette catégorie et il faut un délai mini de 48 h
            (!$this->categorie->choixModeles->count() or $this->debut_at->lt(Carbon::now()->addDays(2)))) {
            return false;
        }*/

        return true;
    }




    public function getPrestation($id): ?Prestation
    {
        if ($this->prestations === null) {
            return null;
        }

        foreach ($this->prestations as $prestation_id => $prestation) {
            if ($prestation->id == $id) {
                return $prestation;
            }
        }
    }

    public function prestationQuantite($prestation_id): ?int
    {
        return isset($this->prestations[$prestation_id]) ? $this->prestations[$prestation_id]->quantite : null;
    }

    public function hasPrestationType($type): bool
    {
        if ($this->prestations === null) {
            return false;
        }
        foreach ($this->prestations as $prestation) {
            if ($prestation->type == $type) {
                return true;
            }
        }
        return false;
    }

    /*public function prestationChoix($prestation_id)
    {
        return isset($this->prestations[$prestation_id]) ? $this->prestations[$prestation_id]->choix : null;
    }*/

    public function addPrestation(Prestation|int $prestation, int $quantite): void
    {
        if (empty($quantite)) {
            return;
        }
        if (!is_object($prestation)) {
            $prestation = Prestation::find($prestation);
        }

        if ($prestation) {
            $prestation->quantite = $quantite;
            $this->prestations[$prestation->id] = $prestation;
        }
    }

    public function addPrestations(array $values): void
    {
        $this->prestations = null;

        foreach ($values as $id => $quantite) {
            $this->addPrestation($id, $quantite);
        }
    }

    public function delPrestation($id): void
    {
        if ($this->prestations == null) {
            return;
        }

        foreach ($this->prestations as $prestation_id => $prestation) {
            if ($prestation->id == $id) {
                unset($this->prestations[$prestation_id]);
            }
        }
    }

    public function prestationsMontantTotal(int $type)
    {
        if ($this->prestations === null) {
            return false;
        }
        $montant = 0;
        foreach ($this->prestations as $prestation) {
            if ($prestation->type == $type) {
                $montant += $prestation->tarif;
            }
        }
        return $montant;
    }
    
}
