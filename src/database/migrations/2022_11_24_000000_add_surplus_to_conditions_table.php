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
        Schema::table('condition_paiements', function (Blueprint $table) {
            $table->decimal('surplus_valeur', 10, 2)->nullable()->after('echeance_nombre');
            $table->text('surplus_type')->nullable()->after('echeance_nombre');
            $table->dropColumn('frais');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('condition_paiements', function (Blueprint $table) {
            $table->dropColumn('surplus_type');
            $table->dropColumn('surplus_valeur');
            $table->decimal('frais', 10, 2)->nullable()->after('echeance_nombre');
        });
    }
};
