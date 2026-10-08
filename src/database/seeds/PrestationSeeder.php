<?php

namespace Ipsum\Reservation\database\seeds;

use Illuminate\Database\Seeder;
use Ipsum\Reservation\app\Models\Prestation\Prestation;
use Ipsum\Reservation\app\Models\Prestation\Taxe;
use Ipsum\Reservation\app\Models\Prestation\Type;


class PrestationSeeder extends Seeder
{


    public function run()
    {
        $taxe = Taxe::where('default', true)->first();
        foreach ($this->getPrestations() as $data) {
            $data['taxe_id'] = $taxe?->id;
            Prestation::create($data);
        }
    }



    private function getPrestations()
    {
        return array(
            array(
                'nom' => 'Location',
                'type_id' => Type::LOCATION_ID,
                'tarification_id' => '1',
            ),
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

}
