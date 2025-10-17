<?php

namespace Ipsum\Reservation\database\seeds;

use Illuminate\Database\Seeder;
use Ipsum\Reservation\app\Models\Dommage\Element;
use Ipsum\Reservation\app\Models\Dommage\Type;


class DommageElementSeeder extends Seeder
{
    public function run()
    {
        foreach ($this->getElements() as $index => $nom) {
            Element::create([
                'nom' => $nom,
                'order' => $index + 1
            ]);
        }
    }

    private function getElements()
    {
        return [
            'Carrosserie',
            'Vitrage',
            'Éclairage',
            'Accessoires et divers'
        ];
    }

}
