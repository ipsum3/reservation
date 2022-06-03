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
        Schema::create('prestations', function (Blueprint $table) {
            $table->id();
            $table->integer('type_id')->unsigned()->index();
            $table->string('class')->nullable();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->string('tarification');
            $table->decimal('montant', 10, 2)->nullable();
            $table->smallInteger('quantite_max')->unsigned();
            $table->smallInteger('gratuit_apres')->nullable()->unsigned();
            $table->smallInteger('jour_fact_max')->nullable()->unsigned();
            $table->smallInteger('order')->unsigned();
        });

        Schema::create('prestables', function (Blueprint $table) {
            $table->integer('prestation_id')->unsigned()->index();
            $table->integer('prestable_id')->unsigned();
            $table->string('prestable_type');
            $table->decimal('montant', 10, 2)->nullable();

            $table->primary(['prestation_id', 'prestable_id', 'prestable_type'], 'prestables_index_unique');
        });

        Schema::create('prestation_types', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
        });

        Schema::create('prestation_blocages', function (Blueprint $table) {
            $table->id();
            $table->integer('prestation_id')->unsigned();
            $table->string('nom')->nullable();
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
        Schema::dropIfExists('prestations');
        Schema::dropIfExists('prestables');
        Schema::dropIfExists('prestation_blocages');
        Schema::dropIfExists('prestation_types');
    }
};
