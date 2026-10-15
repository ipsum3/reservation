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
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique()->nullable();
            $table->bigInteger('reservation_id')->index();
            $table->bigInteger('client_id')->index();
            $table->string('etat');
            $table->string('type');
            $table->string('provider')->nullable();
            $table->string('provider_reference')->nullable();
            $table->text('url')->nullable();
            $table->date('emission_at')->nullable();
            $table->date('echeance_at');
            $table->dateTime('send_at')->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->decimal('montant_paye', 10, 2)->nullable();
            $table->timestamps();

            $table->unique(['provider', 'provider_reference'], 'factures_provider_unique');
        });

        Schema::table('paiements', function (Blueprint $table) {
            $table->bigInteger('facture_id')->index()->nullable()->after('reservation_id');
        });

        Schema::table('prestations', function (Blueprint $table) {
            $table->string('reference_externe')->nullable()->after('id');
            $table->smallInteger('quantite_max')->nullable()->unsigned()->change();
            $table->smallInteger('order')->nullable()->unsigned()->change();
        });

        Schema::table('prestables', function (Blueprint $table) {
            $table->text('description')->nullable()->after('montant');
            $table->smallInteger('quantite')->nullable()->unsigned()->after('montant');
            $table->decimal('remise', 10, 2)->nullable()->unsigned();
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->string('reference_externe')->nullable()->after('id');
            $table->boolean('is_entreprise')->default(false)->after('code');
            $table->string('vat_numero')->nullable()->after('email');
            $table->string('siren')->nullable()->after('email');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->string('reference_externe')->nullable()->after('reference');
            $table->bigInteger('entreprise_id')->index()->nullable()->after('client_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('reference_externe')->nullable()->after('id');
        });

        Schema::table('lieux', function (Blueprint $table) {
            $table->string('reference_externe')->nullable()->after('id');
        });

        if (\Ipsum\Core\app\Models\Setting::count()) {
            Artisan::call('db:seed', ['--class' => \Ipsum\Reservation\database\seeds\PrestationTypeSeeder::class, '--force' => true]);
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
