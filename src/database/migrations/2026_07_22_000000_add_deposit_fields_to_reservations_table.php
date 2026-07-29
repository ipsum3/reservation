<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ipsum\Reservation\database\seeds\PaiementTypeSeeder;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('caution_url')->nullable()->after('caution');
            $table->integer('caution_frais')->nullable()->after('caution_url');
            $table->timestamp('caution_send_at')->nullable()->after('caution_frais');
        });

        Artisan::call('db:seed', ['--class' => PaiementTypeSeeder::class, '--force' => true]);
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
