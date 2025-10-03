<?php

namespace Ipsum\Reservation\database\seeds;

use Illuminate\Database\Seeder;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Ipsum\Reservation\database\seeds\ChecklistSeeder;
use Ipsum\Reservation\database\seeds\DommageElementSeeder;
use Ipsum\Reservation\database\seeds\DommageEmplacementSeeder;
use Ipsum\Reservation\database\seeds\DommageTypeSeeder;
use Ipsum\Reservation\database\seeds\InspectionTypeSeeder;
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
        $this->call(SourcesTableSeeder::class);
        $this->call(TarificationSeeder::class);
        $this->call(ChecklistSeeder::class);
        $this->call(InspectionTypeSeeder::class);
        $this->call(DommageTypeSeeder::class);
        $this->call(DommageElementSeeder::class);
        $this->call(DommageEmplacementSeeder::class);
    }


}
