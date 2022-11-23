<?php

namespace Ipsum\Reservation\database\seeds;

use Illuminate\Database\Seeder;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Ipsum\Reservation\app\Models\Reservation\Condition;
use Ipsum\Reservation\app\Models\Tarif\Duree;
use Ipsum\Reservation\app\Models\Tarif\Saison;
use Ipsum\Reservation\app\Models\Tarif\Tarif;
use Faker\Generator;


class TarifSeeder extends Seeder
{
    public function run()
    {

        $faker = app(Generator::class);

        foreach ($this->getDurees() as $data) {
            Duree::create($data);
        }
        foreach ($this->getSaisons() as $data) {
            Saison::create($data);
        }

        $Categories = Categorie::all();
        $conditions = Condition::all();
        foreach ($Categories as $Categorie) {
            foreach ($this->getSaisons() as $saison) {
                foreach ($this->getDurees() as $duree) {
                    if( config('reservation.tarif.has_multiple_grille_by_condition')) {
                        foreach ($conditions as $condition) {
                            Tarif::create([
                                'categorie_id' => $Categorie->id,
                                'saison_id' => $saison['id'],
                                'duree_id' => $duree['id'],
                                'montant' => $faker->randomFloat(2, 30, 80),
                                'condition_paiement_id' => $condition->id,
                            ]);
                        }
                    } else {
                        Tarif::create([
                            'categorie_id' => $Categorie->id,
                            'saison_id' => $saison['id'],
                            'duree_id' => $duree['id'],
                            'montant' => $faker->randomFloat(2, 30, 80),
                            'condition_paiement_id' => null,
                        ]);
                    }
                }
            }
        }
    }

    private function getDurees()
    {
        return array(
            array(
                'id' => 1,
                'min' => '1',
                'max' => '7',
            ),
            array(
                'id' => 2,
                'min' => '8',
                'max' => '14',
            ),
            array(
                'id' => 3,
                'min' => '15',
                'max' => null,
            ),
        );
    }

    private function getSaisons()
    {
        return array(
            array(
                'id' => 1,
                'nom' => 'Basse saison 2018',
                'debut_at' => '2018-06-01',
                'fin_at' => '2018-11-30',
            ),
            array(
                'id' => 2,
                'nom' => 'Haute saison 2019',
                'debut_at' => '2018-12-01',
                'fin_at' => '2019-05-31',
            ),
            array(
                'id' => 3,
                'nom' => 'Basse saison 2019',
                'debut_at' => '2019-06-01',
                'fin_at' => '2042-11-30',
            ),
        );
    }

}
