<?php

namespace Ipsum\Reservation\database\seeds;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Ipsum\Reservation\app\Models\Reservation\Moyen;
use Ipsum\Reservation\app\Models\Reservation\Type;

class PaiementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!Moyen::count()) {
            foreach ($this->getMoyens() as $data) {
                Moyen::create($data);
            }
        }

        foreach ($this->getTypes() as $data) {
            Type::create($data);
        }
    }

    private function getMoyens()
    {
        return array(
            array(
                'id' => 1,
                'nom' => 'CB site',
            ),
            array(
                'id' => 2,
                'nom' => 'CB',
            ),
            array(
                'id' => 3,
                'nom' => 'Chèque',
            ),
            array(
                'id' => 4,
                'nom' => 'Virement',
            ),
            array(
                'id' => 5,
                'nom' => 'Espèce',
            ),
            array(
                'id' => 6,
                'nom' => 'Chèque vacances',
            ),
            array(
                'id' => 7,
                'nom' => 'Chèque tourisme',
            ),
        );
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
