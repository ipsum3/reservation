<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ipsum\Reservation\app\Models\Client;

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
            $table->string('immatriculation')->nullable()->after('vehicule_id');
        });

        // Table inspections (liée à une réservation et un véhicule)
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->index()->unsigned();
            $table->foreignId('admin_id')->index()->unsigned();
            $table->foreignId('type_id')->index()->unsigned();
            $table->integer('kilometrage')->nullable();
            $table->string('carburant')->nullable();
            $table->text('locataire_signature')->nullable();
            $table->timestamp('locataire_signature_at')->nullable();
            $table->text('agent_signature')->nullable();
            $table->timestamp('agent_signature_at')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
        });

        // Table inspection types (initial/final)
        Schema::create('inspection_types', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
        });

        Schema::create('checklist-inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checklist_id')->index()->unsigned();
            $table->foreignId('inspection_id')->index()->unsigned();
            $table->boolean('value')->default(0);
            $table->timestamps();
        });

        Schema::create('checklist', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->tinyInteger('order')->default(0);
        });

        // Table dommages constatés
        Schema::create('dommages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicule_id')->index()->unsigned();
            $table->foreignId('inspection_id')->index()->unsigned();
            $table->foreignId('type_id')->index()->unsigned();
            $table->foreignId('emplacement_id')->index()->unsigned();
            $table->foreignId('element_id')->index()->unsigned();
            $table->text('observations')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });

        // Table dommages types
        Schema::create('dommage_types', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->tinyInteger('order')->default(0);
        });

        // Table éléments pouvant avoir un dommage
        Schema::create('dommage_elements', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->tinyInteger('order')->default(0);
        });

        // Table emplacements du véhicule
        Schema::create('dommage_emplacements', function (Blueprint $table) {
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

    }
};
