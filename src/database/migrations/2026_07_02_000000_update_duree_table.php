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
            $table->integer('min')->unsigned()->change();
            $table->integer('max')->unsigned()->nullable()->change();
            $table->unsignedInteger('priorite')->default(0)->after('tarification');
            $table->unsignedInteger('delai_maximum')->nullable()->after('max');
        });

        $durees = \Ipsum\Reservation\app\Models\Tarif\Duree::all();
        foreach ($durees as $duree) {
            $duree->min = $duree->min ? ($duree->min * 60 * 24) - 1440 : 0;
            if($duree->max){
                $duree->max *= 60 * 24;
            }
            $duree->save();
        }


        // Initialisation des priorités
        DB::table('durees')
            ->where('is_special', 1)
            ->update([
                'priorite' => 1,
            ]);
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
