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
            $table->string('condition')->nullable()->after('age_max');
            $table->smallInteger('jour')->unsigned()->nullable()->after('age_max');
            $table->time('heure_min')->nullable()->after('age_max');
            $table->time('heure_max')->nullable()->after('age_max');
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
            $table->dropColumn('heure_max');
            $table->dropColumn('heure_min');
            $table->dropColumn('jour');
            $table->dropColumn('condition');
        });
    }
};
