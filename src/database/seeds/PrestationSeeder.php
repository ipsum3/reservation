<?php

namespace Ipsum\Reservation\database\seeds;

use Illuminate\Database\Seeder;
use Ipsum\Reservation\app\Models\Prestation\Prestation;
use Ipsum\Reservation\app\Models\Prestation\Type;


class PrestationSeeder extends Seeder
{

    public function run()
    {

        foreach ($this->getPrestations() as $data) {
            Prestation::create($data);
        }

        foreach ($this->getTypes() as $data) {
            Type::create($data);
        }
    }



    private function getPrestations()
    {
        return array(
            array(
                'nom' => 'Conducteurs supplémentaires',
                'description' => '',
                'type_id' => 1,
                'tarification_id' => '1',
                'montant' => 5,
                'quantite_max' => 4,
                'order' => 1,
            ),
            array(
                'nom' => 'Rachat de "franchise accident"',
                'description' => '',
                'type_id' => 2,
                'tarification_id' => '1',
                'montant' => null,
                'quantite_max' => 1,
                'order' => 5,
            ),
            array(
                'nom' => 'Siège bébé',
                'description' => '',
                'type_id' => 1,
                'tarification_id' => '1',
                'montant' => 6,
                'quantite_max' => 3,
                'order' => 2,
            ),
            array(
                'nom' => 'Rehausseur',
                'description' => '',
                'type_id' => 1,
                'tarification_id' => '1',
                'montant' => 3,
                'quantite_max' => 3,
                'order' => 3,
            ),
            array(
                'nom' => 'GPS',
                'description' => '',
                'type_id' => 1,
                'tarification_id' => '1',
                'montant' => 7,
                'quantite_max' => 1,
                'order' => 4,
            ),
            array(
                'nom' => 'Taxe aéroport',
                'description' => '',
                'type_id' => 3,
                'tarification_id' => '1',
                'montant' => 7,
                'quantite_max' => 1,
                'order' => 6,
            ),
        );
    }

    private function getTypes()
    {
        return array(
            array(
                'id' => 1,
                'nom' => 'Option',
            ),
            array(
                'id' => 2,
                'nom' => 'Assurance',
            ),
            array(
                'id' => 3,
                'nom' => 'Frais',
            ),
        );
    }

}
