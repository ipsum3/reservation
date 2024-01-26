<?php

namespace Ipsum\Reservation\database\seeds;

use Illuminate\Database\Seeder;
use Ipsum\Reservation\app\Models\Prestation\Tarification;


class TarificationSeeder extends Seeder
{
    public function run()
    {

        $tarificationsData = [
            ['nom' => 'Jour'],
            ['nom' => 'Forfait'],
            ['nom' => 'Agence'],
            ['nom' => 'Carburant'],
        ];

        foreach ($tarificationsData as $data) {
            Tarification::create($data);
        }

    }


}
