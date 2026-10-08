<?php

namespace Ipsum\Reservation\database\seeds;

use Illuminate\Database\Seeder;
use Ipsum\Reservation\app\Models\Prestation\Prestation;
use Ipsum\Reservation\app\Models\Prestation\Type;


class PrestationTypeSeeder extends Seeder
{

    public function run()
    {

        $types = Type::all()->pluck('id')->toArray();

        foreach ($this->getTypes() as $data) {
            if (!in_array($data['id'], $types)) {
                Type::create($data);
            }
        }
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
            array(
                'id' => 4,
                'nom' => 'Location',
            ),
            array(
                'id' => 5,
                'nom' => 'Frais de restitution',
            ),
        );
    }

}
