<?php

namespace Ipsum\Reservation\database\seeds;

use Illuminate\Database\Seeder;
use Ipsum\Core\app\Models\Setting;
use Ipsum\Reservation\app\Models\Prestation\Taxe;
use Str;
use Ipsum\Reservation\app\Models\Source\Source;
use Ipsum\Reservation\app\Models\Source\Type;

class TaxesTableSeeder extends Seeder
{
    public function run()
    {
        foreach ($this->getTaux() as $taux) {
            Taxe::create($taux);
        }

    }

    private function getTaux()
    {
        return array(
            array(
                'nom' => '20%',
                'taux' => 20,
                'default' => 1,
            ),
            array(
                'nom' => '8.5%',
                'taux' => 8.5,
                'default' => 0,
            ),
            array(
                'nom' => '2.1%',
                'taux' => 2.1,
                'default' => 0,
            ),
            array(
                'nom' => 'Aucune',
                'taux' => 0,
                'default' => 0,
            ),
        );
    }

}
