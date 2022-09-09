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
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('civilite')->nullable()->after('client_id');
            $table->string('naissance_lieu')->nullable()->after('naissance_at');
            $table->text('echeancier')->nullable()->after('promotions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('civilite');
            $table->dropColumn('naissance_at');
            $table->dropColumn('echeancier');
        });
    }
};
