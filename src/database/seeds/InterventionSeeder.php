<?php

namespace Ipsum\Reservation\database\seeds;

use Illuminate\Database\Seeder;
use Ipsum\Reservation\app\Models\Categorie\InterventionType;


class InterventionSeeder extends Seeder
{
    public function run()
    {
        foreach ($this->getTypes() as $key => $data) {
            InterventionType::create(['nom' => $data, 'order' => $key + 1]);
        }
    }


    private function getTypes()
    {
        return ['Contrôle technique', 'Révision', 'Entretien', 'Problème mécanique', 'Carrosserie', 'Nettoyage', 'Pneus', 'Expertise', 'Réparation', 'Autre'];
    }

}
