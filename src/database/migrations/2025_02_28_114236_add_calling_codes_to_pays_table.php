<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ipsum\Reservation\app\Models\Reservation\Pays;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Vider la table "pays"
        DB::table('pays')->truncate();

        Schema::table('pays', function (Blueprint $table) {
            $table->string('callingCodes')->nullable()->after('nom');
        });

        // Relancer le seeder "PaysTableSeeder"
        Artisan::call('db:seed', [
            '--class' => \Ipsum\Reservation\database\seeds\PaysTableSeeder::class,
            '--force' => true,
        ]);
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
