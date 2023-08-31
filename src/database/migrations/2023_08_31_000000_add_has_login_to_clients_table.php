<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ipsum\Reservation\app\Models\Client;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->boolean('has_login')->default(1)->after('email');
        });

        Client::whereNull('login')->update(['has_login' => 0]);

        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('login');
            $table->unique(['email','has_login'], 'unique_email');
            $table->string('email')->nullable(false)->change();
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
