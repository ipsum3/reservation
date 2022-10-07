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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            $table->smallInteger('type_id')->unsigned();

            $table->string('nom')->unique();
            $table->string('modeles');

            $table->smallInteger('nb_vehicules')->unsigned()->nullable();

            $table->text('description')->nullable();
            $table->text('texte')->nullable();

            $table->smallInteger('place')->unsigned();
            $table->smallInteger('porte')->unsigned();
            $table->smallInteger('bagage')->unsigned();
            $table->smallInteger('volume')->unsigned()->nullable();
            $table->smallInteger('longeur')->unsigned()->nullable();
            $table->smallInteger('largeur')->unsigned()->nullable();
            $table->smallInteger('hauteur')->unsigned()->nullable();
            $table->boolean('climatisation');
            $table->smallInteger('transmission_id')->unsigned();
            $table->smallInteger('motorisation_id')->unsigned();
            $table->smallInteger('carrosserie_id')->unsigned();

            $table->decimal('caution', 10, 2)->nullable();
            $table->decimal('franchise', 10, 2)->nullable();
            $table->smallInteger('age_minimum')->unsigned();
            $table->smallInteger('annee_permis_minimum')->unsigned();

            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();

            $table->text('custom_fields')->nullable();
            
            $table->timestamps();
        });

        Schema::create('carrosseries', function (Blueprint $table) {
            $table->id();
            $table->string('class')->nullable();
            $table->string('nom');
            $table->tinyInteger('order')->default(0);
        });

        Schema::create('transmissions', function (Blueprint $table) {
            $table->id();
            $table->string('class')->nullable();
            $table->string('nom');
        });

        Schema::create('motorisations', function (Blueprint $table) {
            $table->id();
            $table->string('class')->nullable();
            $table->string('nom');
        });

        Schema::create('categorie_blocages', function (Blueprint $table) {
            $table->id();
            $table->integer('categorie_id')->unsigned();
            $table->string('nom')->nullable();
            $table->date('debut_at');
            $table->date('fin_at');
        });

        Schema::create('categorie_types', function (Blueprint $table) {
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
        Schema::dropIfExists('categories');
        Schema::dropIfExists('carrosseries');
        Schema::dropIfExists('transmissions');
        Schema::dropIfExists('motorisations');
        Schema::dropIfExists('categorie_blocages');
        Schema::dropIfExists('categorie_types');
    }
};
