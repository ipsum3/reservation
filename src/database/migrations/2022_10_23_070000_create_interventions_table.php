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
        Schema::create('interventions', function (Blueprint $table) {
            $table->id();
            $table->integer('vehicule_id')->unsigned();
            $table->integer('type_id')->unsigned();
            $table->dateTime('debut_at');
            $table->dateTime('fin_at');
            $table->string('intervenant')->nullable();
            $table->text('information')->nullable();
            $table->decimal('cout', 10, 2)->nullable();

            $table->text('custom_fields')->nullable();

            $table->timestamps();
        });

        Schema::create('intervention_types', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->tinyInteger('order')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('interventions');
        Schema::dropIfExists('intervention_types');
    }
};
