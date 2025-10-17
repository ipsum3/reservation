<?php

namespace Ipsum\Reservation\database\seeds;

use Illuminate\Database\Seeder;
use Ipsum\Reservation\app\Models\Dommage\Emplacement;


class DommageEmplacementSeeder extends Seeder
{
    public function run()
    {
        foreach ($this->getEmplacements() as $index => $nom) {
            Emplacement::create([
                'nom' => $nom,
                'order' => $index + 1
            ]);
        }
    }

    private function getEmplacements()
    {
        return [
            'Avant',
            'Avant droit',
            'Avant gauche',
            'Arrière droit',
            'Arrière gauche',
            'Arrière'
        ];
    }

}
