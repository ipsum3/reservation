<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Fix nom de fichier pas bon
        DB::table('migrations')->where('migration', '_2025_10_15_000000_update_reservation_immatriculation_table')->delete();

        if (!Schema::hasColumn('reservations', 'immatriculation')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->string('immatriculation')->nullable()->after('vehicule_id');
            });
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
