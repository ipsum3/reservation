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
        Schema::create('tarifs', function (Blueprint $table) {
            $table->id();
            $table->integer('categorie_id')->unsigned();
            $table->integer('duree_id')->unsigned();
            $table->integer('saison_id')->unsigned()->nullable();
            $table->decimal('montant', 10, 2)->nullable();
            $table->string('condition_paiement_id')->nullable();

            $table->unique(['categorie_id', 'duree_id', 'saison_id', 'condition_paiement_id'], 'tarifs_index_unique');
        });

        Schema::create('durees', function (Blueprint $table) {
            $table->id();
            $table->integer('min')->unsigned();
            $table->integer('max')->unsigned()->nullable();
        });

        Schema::create('saisons', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->date('debut_at');
            $table->date('fin_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tarifs');
        Schema::dropIfExists('durees');
        Schema::dropIfExists('saisons');
    }
};
