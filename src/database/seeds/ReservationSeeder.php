<?php

namespace Ipsum\Reservation\database\seeds;

use Illuminate\Database\Seeder;
use Ipsum\Reservation\app\Models\Reservation\Etat;
use Ipsum\Reservation\app\Models\Reservation\Modalite;
use Ipsum\Reservation\app\Models\Reservation\Reservation;


class ReservationSeeder extends Seeder
{
    public function run()
    {
        Reservation::factory()->count(10)->create();


        foreach ($this->getEtats() as $data) {
            Etat::create($data);
        }
        
        foreach ($this->getModalitesPaiement() as $data) {
            Modalite::create($data);
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
            ),
        );
    }

    private function getModalitesPaiement()
    {
        return array(
            array(
                'id' => 1,
                'nom' => 'En ligne',
            ),
            array(
                'id' => 2,
                'nom' => 'En agence',
            ),
        );
    }

}
