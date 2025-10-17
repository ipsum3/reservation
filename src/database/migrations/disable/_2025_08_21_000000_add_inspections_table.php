<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ipsum\Reservation\app\Models\Client;
use Ipsum\Reservation\app\Models\Dommage\Element;
use Ipsum\Reservation\app\Models\Dommage\Emplacement;
use Ipsum\Reservation\app\Models\Inspection\Checklist;
use Ipsum\Reservation\app\Models\Inspection\Type;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Table inspections (liée à une réservation et un véhicule)
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->index()->unsigned();
            $table->foreignId('admin_id')->index()->unsigned();
            $table->foreignId('type_id')->index()->unsigned();
            $table->integer('kilometrage')->nullable();
            $table->tinyInteger('carburant')->nullable();
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

        if (\Ipsum\Core\app\Models\Setting::count()) {
            Artisan::call('db:seed', ['--class' => \Ipsum\Reservation\database\seeds\InspectionTypeSeeder::class, '--force' => true]);
        }

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

        if (\Ipsum\Core\app\Models\Setting::count()) {
            Artisan::call('db:seed', ['--class' => \Ipsum\Reservation\database\seeds\ChecklistSeeder::class, '--force' => true]);
        }

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

        if (\Ipsum\Core\app\Models\Setting::count()) {
            Artisan::call('db:seed', ['--class' => \Ipsum\Reservation\database\seeds\DommageTypeSeeder::class, '--force' => true]);
        }

        // Table éléments pouvant avoir un dommage
        Schema::create('dommage_elements', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->tinyInteger('order')->default(0);
        });

        if (\Ipsum\Core\app\Models\Setting::count()) {
            Artisan::call('db:seed', ['--class' => \Ipsum\Reservation\database\seeds\DommageElementSeeder::class, '--force' => true]);
        }

        // Table emplacements du véhicule
        Schema::create('dommage_emplacements', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->tinyInteger('order')->default(0);
        });

        if (\Ipsum\Core\app\Models\Setting::count()) {
            Artisan::call('db:seed', ['--class' => \Ipsum\Reservation\database\seeds\DommageEmplacementSeeder::class, '--force' => true]);
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
