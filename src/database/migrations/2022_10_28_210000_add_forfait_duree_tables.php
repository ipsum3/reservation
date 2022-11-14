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
            $table->string('nom')->nullable()->after('id');
            $table->string('type')->nullable()->after('id');
            $table->boolean('is_special')->default(0)->after('id');
            $table->timestamps();
        });


        Schema::create('jours', function (Blueprint $table) {
            $table->id();
            $table->integer('duree_id')->nullable()->unsigned()->index();
            $table->smallInteger('value')->unsigned();
            $table->time('heure_debut_min')->nullable();
            $table->time('heure_fin_max')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('durees', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('is_special');
            $table->dropColumn('nom');
            $table->dropColumn('tarification');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });

        Schema::dropIfExists('jours');
    }
};
