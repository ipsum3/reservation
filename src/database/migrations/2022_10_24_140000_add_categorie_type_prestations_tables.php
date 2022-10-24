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
        Schema::table('prestations', function (Blueprint $table) {
            $table->integer('categorie_type_id')->nullable()->unsigned()->after('condition');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prestations', function (Blueprint $table) {
            $table->dropColumn('categorie_type_id');
        });
    }
};
