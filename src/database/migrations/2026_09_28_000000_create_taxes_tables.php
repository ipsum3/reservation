<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ipsum\Reservation\app\Models\Prestation\Prestation;
use Ipsum\Reservation\app\Models\Prestation\Taxe;
use Ipsum\Reservation\app\Models\Prestation\Type;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taxes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nom');
            $table->decimal('taux', 10, 2);
            $table->boolean('defaut');
        });

        Schema::table('prestations', function (Blueprint $table) {
            $table->bigInteger('taxe_id')->after('tarification_id');
        });

        if (\Ipsum\Core\app\Models\Setting::count()) {
            Artisan::call('db:seed', ['--class' => \Ipsum\Reservation\database\seeds\TaxesTableSeeder::class, '--force' => true]);

            $defaut_taxe = Taxe::where('defaut', true)->first();

            Prestation::create(['nom' => 'Location',
                'type_id' => Type::LOCATION_ID,
                'tarification_id' => '1',
                'taxe_id' => $defaut_taxe->id,
            ]);

            \Ipsum\Reservation\app\Models\Prestation\Prestation::query()->update(['taxe_id' => $defaut_taxe->id]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
};
