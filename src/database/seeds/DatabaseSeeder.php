<?php

namespace Ipsum\Reservation\database\seeds;

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    public function run()
    {

        $this->call(CategorieSeeder::class);
        $this->call(ReservationSeeder::class);
        $this->call(PaiementSeeder::class);
        $this->call(PrestationSeeder::class);
        $this->call(TarifSeeder::class);
        $this->call(LieuSeeder::class);
        $this->call(PaysTableSeeder::class);
    }


}
