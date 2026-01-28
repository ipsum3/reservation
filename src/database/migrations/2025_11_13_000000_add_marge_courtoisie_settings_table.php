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

        if (\Ipsum\Core\app\Models\Setting::count()) {
            Schema::table('settings', function (Blueprint $table) {
                $table->string('description', 255)->change();
            });

            Artisan::call('db:seed', ['class' => "\Ipsum\Reservation\database\seeds\SettingsTableSeeder", '--force' => true]);
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
