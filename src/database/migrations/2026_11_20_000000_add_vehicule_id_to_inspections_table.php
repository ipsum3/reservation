<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ipsum\Reservation\app\Models\Inspection\Inspection;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inspections', function (Blueprint $table) {
            $table->string('immatriculation')->nullable()->after('reservation_id');
            $table->foreignId('vehicule_id')->nullable()->index()->unsigned()->after('reservation_id');
        });


        if (\Ipsum\Core\app\Models\Setting::count()) {
            \Ipsum\Reservation\app\Models\Inspection\Inspection::with('reservation')->get()->each(function (Inspection $inspection) {
                $inspection->immatriculation = $inspection->reservation?->immatriculation;
                $inspection->vehicule_id = $inspection->reservation?->vehicule_id;
                $inspection->save();
            });
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
