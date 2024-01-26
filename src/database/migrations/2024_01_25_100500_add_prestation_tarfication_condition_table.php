<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ipsum\Reservation\app\Models\Prestation\Tarification;
use \Ipsum\Reservation\app\Models\Prestation\Condition;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prestation_tarifications', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
        });

        Schema::table('prestations', function (Blueprint $table) {
            $table->integer('tarification_id')->unsigned()->index()->after('tarification');
        });

        if (\Ipsum\Core\app\Models\Setting::count()) {
            Artisan::call('db:seed', ['--class' => \Ipsum\Reservation\database\seeds\TarificationSeeder::class, '--force' => true]);
        }

        $tarifications = Tarification::all();

        foreach ($tarifications as $tarification){
            DB::table('prestations')->where('tarification', Str::lower($tarification->nom))->update(['tarification_id' => $tarification->id]);
        }


        Schema::table('prestations', function (Blueprint $table) {
            $table->dropColumn('tarification');
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
