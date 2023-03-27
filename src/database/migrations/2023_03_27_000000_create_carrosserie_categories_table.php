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
        Schema::create('carrosserie_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('categorie_id');
            $table->integer('carrosserie_id');
        });

        if (\Ipsum\Core\app\Models\Setting::count()) {
            foreach ( Ipsum\Reservation\app\Models\Categorie\Categorie::all() as $categorie ) {
                if( $categorie->carrosserie_id ) {
                    $categorie->carrosseries()->attach($categorie->carrosserie_id);
                }
            }
        }

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('carrosserie_id');
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
