<?php

namespace Ipsum\Reservation\database\seeds;

use Illuminate\Database\Seeder;
use Ipsum\Reservation\app\Models\Categorie\Carrosserie;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Ipsum\Reservation\app\Models\Categorie\Motorisation;
use Ipsum\Reservation\app\Models\Categorie\Transmission;
use Ipsum\Reservation\app\Models\Categorie\Type;
use Ipsum\Reservation\app\Models\Categorie\Vehicule;


class CategorieSeeder extends Seeder
{
    public function run()
    {

        foreach (range('A', 'F') as $nom) {
            $categorie = Categorie::factory()->create([
                'nom' => $nom,
            ]);
            $categorie->carrosseries()->attach($this->getCarrosseries()[array_rand($this->getCarrosseries())]['id']);
        }


        Vehicule::factory(10)->create();


        foreach ($this->getMotorisations() as $data) {
            Motorisation::create($data);
        }
        
        foreach ($this->getTransmissions() as $data) {
            Transmission::create($data);
        }

        foreach ($this->getCarrosseries() as $data) {
            Carrosserie::create($data);
        }

        foreach ($this->getTypes() as $data) {
            Type::create($data);
        }
    }

    private function getMotorisations()
    {
        return array(
            array(
                'id' => 1,
                'class' => 'e',
                'nom' => 'Essence',
            ),
            array(
                'id' => 2,
                'class' => 'd',
                'nom' => 'Diesel',
            ),
            array(
                'id' => 3,
                'class' => 'd',
                'nom' => 'Hybride',
            ),
            array(
                'id' => 4,
                'class' => 'd',
                'nom' => 'Electrique',
            ),
        );
    }


    private function getTransmissions()
    {
        return array(
            array(
                'id' => 1,
                'class' => 'm',
                'nom' => 'Manuelle',
            ),
            array(
                'id' => 2,
                'class' => 'a',
                'nom' => 'Automatique',
            ),
        );
    }


    private function getCarrosseries()
    {
        return array(
            array(
                'id' => 1,
                'class' => 'category-1',
                'nom' => 'économique',
            ),
            array(
                'id' => 2,
                'class' => 'category-2',
                'nom' => 'compact',
            ),
            array(
                'id' => 3,

                'class' => 'category-3',
                'nom' => 'familial',
            ),
            array(
                'id' => 4,
                'class' => '',
                'nom' => '4x4',
            ),
            array(
                'id' => 5,
                'class' => '',
                'nom' => 'utilitaire',
            ),
        );
    }

    private function getTypes()
    {
        return array(
            array(
                'id' => 1,
                'nom' => 'Véhicule de tourisme',
            ),
            /*array(
                'id' => 2,
                'nom' => 'Véhicule utilitaire',
            ),
            array(
                'id' => 3,
                'nom' => 'Véhicule industriel',
            ),
            array(
                'id' => 4,
                'nom' => 'Moto / scooter',
            ),*/
        );
    }

}
