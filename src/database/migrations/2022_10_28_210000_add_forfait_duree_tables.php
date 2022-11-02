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
        Schema::table('durees', function (Blueprint $table) {
            $table->string('tarification')->default('jour')->after('id');
            $table->string('nom')->default('jour')->after('id');
            $table->string('type')->nullable()->after('id');
            $table->boolean('is_special')->default(0)->after('id');
            $table->smallInteger('min_jour')->unsigned()->nullable();
            $table->time('min_heure')->nullable();
            $table->smallInteger('max_jour')->unsigned()->nullable();
            $table->time('max_heure')->nullable();
            $table->timestamps();
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
            $table->dropColumn('type');
            $table->dropColumn('is_special');
            $table->dropColumn('nom');
            $table->dropColumn('tarification');
            $table->dropColumn('min_jour');
            $table->dropColumn('min_heure');
            $table->dropColumn('max_jour');
            $table->dropColumn('max_heure');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
};
