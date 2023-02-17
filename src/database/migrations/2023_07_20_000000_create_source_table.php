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
        Schema::create('sources', function (Blueprint $table) {
            $table->id();
            $table->integer('type_id');
            $table->string('nom');
            $table->string('ref_tracking');
        });

        Schema::create('source_types', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('icon');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->renameColumn('source', 'source_id');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->integer('source_id')->nullable()->change();
        });

        if (\Ipsum\Core\app\Models\Setting::count()) {
            Artisan::call('db:seed', ['class' => "\Ipsum\Reservation\database\seeds\SourcesTableSeeder", '--force' => true]);
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
