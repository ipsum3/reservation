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
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->integer('reservation_id')->nullable()->unsigned()->index();
            $table->integer('paiement_moyen_id')->unsigned()->index();
            $table->decimal('montant', 10, 2);
            $table->string('transaction_ref', 100)->nullable();
            $table->string('autorisation_ref', 100)->nullable();
            $table->string('erreur', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('paiement_moyens', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paiements');
        Schema::dropIfExists('paiement_moyens');
    }
};
