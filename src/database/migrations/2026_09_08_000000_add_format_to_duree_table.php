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
            $table->string('min_format')->after('min')->default('jour');
            $table->string('max_format')->after('max')->default('jour');
        });


        $durees = \Ipsum\Reservation\app\Models\Tarif\Duree::all();
        foreach ($durees as $duree) {
            $duree->min++;
            $duree->save();
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
