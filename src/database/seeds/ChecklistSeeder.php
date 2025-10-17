<?php

namespace Ipsum\Reservation\database\seeds;

use Illuminate\Database\Seeder;
use Ipsum\Reservation\app\Models\Inspection\Checklist;


class ChecklistSeeder extends Seeder
{
    public function run()
    {
        foreach ($this->getChecklists() as $index => $nom) {
            Checklist::create([
                'nom' => $nom,
                'order' => $index + 1
            ]);
        }
    }

    private function getChecklists()
    {
        return [
            'Propreté intérieure',
            'Propreté extérieure',
            'Papiers du véhicule',
            'Kit de sécurité',
            'Niveaux',
            'Roue de secours'
        ];
    }

}
