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
        Schema::table('motorisations', function (Blueprint $table) {
            $table->decimal('montant', 10, 2)->nullable();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->decimal('reservoir_capacite', 10, 2)->nullable();
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
