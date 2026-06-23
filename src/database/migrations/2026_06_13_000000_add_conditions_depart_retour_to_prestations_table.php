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
            $table->smallInteger('jour_retour')->unsigned()->nullable()->after('jour');
            $table->renameColumn('jour', 'jour_depart');
            $table->time('heure_min_retour')->nullable()->after('heure_min');
            $table->renameColumn('heure_min', 'heure_min_depart');
            $table->time('heure_max_retour')->nullable()->after('heure_max');
            $table->renameColumn('heure_max', 'heure_max_depart');
            $table->boolean('is_cumulable')->default(0)->after('condition');
            $table->smallInteger('age_min')->nullable()->unsigned()->after('age_max');
        });


        if (Ipsum\Reservation\app\Models\Prestation\Prestation::count()) {
            foreach (\Ipsum\Reservation\app\Models\Prestation\Prestation::all() as $prestation) {
                $prestation->jour_retour = $prestation->jour_depart;
                $prestation->heure_min_retour = $prestation->heure_min_depart;
                $prestation->heure_max_retour = $prestation->heure_max_depart;
                $prestation->is_cumulable = ($prestation->condition === 'non_cumulable' and $prestation->condition !== null) ? 0 : 1;
                if ($prestation->condition === 'non_cumulable') {
                    $prestation->condition = null;
                }
                $prestation->save();
            }
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
