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
        Schema::table('paiements', function (Blueprint $table) {
            $table->integer('paiement_type_id')->unsigned()->index()->nullable()->after('paiement_moyen_id');
        });

        Schema::create('paiement_types', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
        });

        if (\Ipsum\Core\app\Models\Setting::count()) {
            Artisan::call('db:seed', ['class' => "\Ipsum\Reservation\database\seeds\PaiementTypeSeeder", '--force' => true]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropColumn('paiement_type_id');
        });

        Schema::dropIfExists('paiement_types');
    }
};
