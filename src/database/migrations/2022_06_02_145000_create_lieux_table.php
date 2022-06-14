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
        Schema::create('lieux', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->integer('type_id')->unsigned()->index();
            $table->boolean('is_actif')->default('1');
            $table->string('nom');
            $table->string('telephone');
            $table->text('adresse');
            $table->text('instruction')->nullable();
            $table->text('horaires_txt');
            $table->string('gps');
            $table->string('emails');
            $table->string('emails_reservation');
            $table->tinyInteger('order')->default(0);
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();
        });

        Schema::create('lieu_types', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
        });

        Schema::create('feries', function (Blueprint $table) {
            $table->id();
            $table->integer('lieu_id')->nullable()->unsigned()->index();
            $table->string('nom')->nullable();
            $table->date('jour_at');
        });

        Schema::create('fermetures', function (Blueprint $table) {
            $table->id();
            $table->integer('lieu_id')->nullable()->unsigned()->index();
            $table->string('nom')->nullable();
            $table->date('debut_at');
            $table->date('fin_at')->nullable();
        });

        Schema::create('horaires', function (Blueprint $table) {
            $table->id();
            $table->integer('lieu_id')->unsigned()->index();
            $table->smallInteger('jour')->index();
            $table->time('debut');
            $table->time('fin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lieux');
        Schema::dropIfExists('lieu_types');
        Schema::dropIfExists('feries');
        Schema::dropIfExists('fermetures');
        Schema::dropIfExists('horaires');
    }
};
