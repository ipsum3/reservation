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
        Schema::create('vehicules', function (Blueprint $table) {
            $table->id();
            $table->string('immatriculation')->unique();
            $table->date('mise_en_circualtion_at');
            $table->integer('categorie_id')->unsigned()->nullable()->index();
            $table->string('marque_modele');
            $table->date('sortie_at')->nullable();

            $table->timestamps();
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->integer('vehicule_id')->index()->unsigned()->nullable()->after('categorie_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicules');

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('vehicule_id');
        });
    }
};
