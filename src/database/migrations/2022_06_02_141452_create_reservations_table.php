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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();

            $table->string('reference')->nullable()->unique();

            $table->integer('etat_id')->unsigned()->index();
            $table->integer('modalite_paiement_id')->unsigned()->index();

            $table->integer('client_id')->unsigned()->nullable()->index();
            $table->string('nom');
            $table->string('prenom')->nullable();
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            $table->string('adresse')->nullable();
            $table->string('cp')->nullable();
            $table->string('ville')->nullable();
            $table->integer('pays_id')->unsigned()->nullable();
            $table->string('pays_nom')->nullable();
            $table->date('naissance_at')->nullable();
            $table->string('permis_numero')->nullable();
            $table->date('permis_at')->nullable();
            $table->string('permis_delivre')->nullable();
            $table->string('vol')->nullable();
            $table->text('observation')->nullable();

            $table->integer('categorie_id')->unsigned()->index();
            $table->string('categorie_nom');
            $table->decimal('franchise', 10, 2)->nullable();
            $table->dateTime('debut_at');
            $table->dateTime('fin_at');
            $table->integer('debut_lieu_id')->nullable()->unsigned()->index();
            $table->integer('fin_lieu_id')->nullable()->unsigned()->index();
            $table->string('debut_lieu_nom')->nullable();
            $table->string('fin_lieu_nom')->nullable();

            $table->decimal('montant_base', 10, 2)->nullable();
            $table->text('prestations')->nullable();
            $table->text('promotions')->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->decimal('montant_paye', 10, 2)->nullable();

            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('reservation_etats', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
        });

        Schema::create('modalite_paiements', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('reservation_etats');
        Schema::dropIfExists('modalite_paiements');
    }
};
