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
            $table->string('numero')->unique();
            $table->bigInteger('reservation_id')->index();
            $table->string('type');
            $table->string('provider');
            $table->string('provider_reference');
            $table->text('url')->nullable();
            $table->dateTime('send_at')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'provider_reference'], 'factures_provider_unique');
        });

        Schema::table('paiements', function (Blueprint $table) {
            $table->bigInteger('facture_id')->index()->nullable()->after('reservation_id');
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
