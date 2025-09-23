<?php

namespace Ipsum\Reservation\database\seeds;

use Illuminate\Database\Seeder;
use Ipsum\Reservation\app\Models\Dommage\Type;


class DommageTypeSeeder extends Seeder
{
    public function run()
    {
        foreach ($this->getTypes() as $index => $nom) {
            Type::create([
                'nom' => $nom,
                'order' => $index + 1
            ]);
        }
    }

    private function getTypes()
    {
        return [
            'Rayure',
            'Enfoncement / bosselage',
            'Cassure',
            'Éclats / bris',
            'Pièce manquante',
            'Autre'
        ];
    }

}
