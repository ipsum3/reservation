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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('civilite')->nullable();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique()->nullable();
            $table->string('telephone')->nullable();
            $table->string('adresse')->nullable();
            $table->string('cp')->nullable();
            $table->string('ville')->nullable();
            $table->integer('pays_id')->unsigned()->nullable();
            $table->date('naissance_at')->nullable();
            $table->string('naissance_lieu')->nullable();
            $table->string('permis_numero')->nullable();
            $table->date('permis_at')->nullable();
            $table->string('permis_delivre')->nullable();

            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
