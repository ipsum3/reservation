<?php

namespace Ipsum\Reservation\database\seeds;

use Illuminate\Database\Seeder;
use Ipsum\Reservation\app\Models\Client;


class DatabaseSeeder extends Seeder
{
    public function run()
    {

        Client::factory(10)->create();

        $this->call(CategorieSeeder::class);
        $this->call(InterventionSeeder::class);
        $this->call(ReservationSeeder::class);
        $this->call(PaiementSeeder::class);
        $this->call(PaiementTypeSeeder::class);
        $this->call(PrestationSeeder::class);
        $this->call(TarifSeeder::class);
        $this->call(LieuSeeder::class);
        $this->call(PaysTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
    }


}
