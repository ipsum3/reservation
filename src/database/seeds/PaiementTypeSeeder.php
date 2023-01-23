<?php

namespace Ipsum\Reservation\database\seeds;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Ipsum\Reservation\app\Models\Reservation\Moyen;
use Ipsum\Reservation\app\Models\Reservation\Type;

class PaiementTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->getTypes() as $data) {
            Type::create($data);
        }
    }

    private function getTypes()
    {
        return array(
            array(
                'id' => 1,
                'nom' => 'Paiement',
            ),
            array(
                'id' => 2,
                'nom' => 'Acompte',
            ),
        );
    }
}
