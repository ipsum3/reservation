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
            $table->smallInteger('duree_max')->unsigned()->nullable()->after('condition');
            $table->smallInteger('duree_min')->unsigned()->nullable()->after('condition');
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
            $table->dropColumn('duree_max');
            $table->dropColumn('duree_min');
        });
    }
};
