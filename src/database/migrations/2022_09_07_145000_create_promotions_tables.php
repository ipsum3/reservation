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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->integer('client_id')->unsigned()->nullable()->index();
            $table->string('type');
            $table->string('reference')->nullable();
            $table->string('nom');
            $table->text('extrait')->nullable();
            $table->text('texte')->nullable();

            $table->integer('modalite_paiement_id')->unsigned()->nullable()->index();
            $table->string('code')->nullable();

            $table->date('debut_at');
            $table->date('fin_at');
            $table->date('activation_at')->nullable();
            $table->date('desactivation_at')->nullable();

            $table->smallInteger('duree_min')->nullable()->unsigned();
            $table->smallInteger('duree_max')->nullable()->unsigned();

            $table->text('reduction_type');
            $table->decimal('reduction_valeur', 10, 2)->nullable();

            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();
        });

        Schema::create('promotionables', function (Blueprint $table) {
            $table->integer('promotion_id')->unsigned()->index();
            $table->morphs('promotionable');
            $table->decimal('reduction', 10, 2)->nullable();

            $table->primary(['promotion_id', 'promotionable_id', 'promotionable_type'], 'promotionables_index_unique');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('promotionables');
    }
};
