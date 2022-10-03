<?php

namespace Ipsum\Reservation\database\seeds;

use Illuminate\Database\Seeder;
use Ipsum\Reservation\app\Models\Reservation\Etat;
use Ipsum\Reservation\app\Models\Reservation\Condition;
use Ipsum\Reservation\app\Models\Reservation\Reservation;


class ReservationSeeder extends Seeder
{
    public function run()
    {
        Reservation::factory()->count(10)->create();


        foreach ($this->getEtats() as $data) {
            Etat::create($data);
        }
        
        foreach ($this->getConditionsPaiement() as $data) {
            Condition::create($data);
        }
    }

    private function getEtats()
    {
        return array(
            array(
                'id' => 1,
                'nom' => 'Non validée',
            ),
            array(
                'id' => 2,
                'nom' => 'Validée',
            ),
            array(
                'id' => 5,
                'nom' => 'Annulée',
            ),/*
            array(
                'id' => 7,
                'nom' => 'No show',
            ),*/
        );
    }

    private function getConditionsPaiement()
    {
        return array(
            array(
                'id' => 1,
                'nom' => 'Paiement comptant',
                'site_nom' => 'En ligne',
                'site_actif' => true,
                'order' => 1
            ),
            array(
                'id' => 2,
                'nom' => 'Paiement partiel',
                'site_nom' => 'En agence',
                'site_actif' => true,
                'acompte_type' => 'pourcentage',
                'acompte_value' => 30,
                'order' => 2
            ),
            array(
                'id' => 3,
                'nom' => 'Paiement à la location',
                'site_nom' => 'En agence',
                'site_actif' => false,
                'order' => 2
            ),
            /*array(
                'id' => 4,
                'nom' => 'Paiement en 3 fois',
                'echeance_nombre' => 3,
                'site_actif' => true,
                'order' => 3
            ),*/
        );
    }

}
