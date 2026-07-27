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
        $types = Type::all()->pluck('id')->toArray();
        foreach ($this->getTypes() as $type) {
            if (!in_array($type['id'], $types)) {
                Type::create($type);
            }
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
            array(
                'id' => 3,
                'nom' => 'Avoir',
            ),
            array(
                'id' => 4,
                'nom' => 'Remboursement',
            ),
            array(
                'id' => 5,
                'nom' => 'Caution',
            ),
        );
    }
}
