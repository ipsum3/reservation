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
            $table->text('custom_fields')->nullable()->after('categorie_type_id');
        });
        Schema::table('saisons', function (Blueprint $table) {
            $table->text('custom_fields')->nullable()->after('fin_at');
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->text('custom_fields')->nullable()->after('permis_delivre');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prestations', function (Blueprint $table) {
            $table->dropColumn('custom_fields');
        });
        Schema::table('saisons', function (Blueprint $table) {
            $table->dropColumn('custom_fields');
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('custom_fields');
        });
    }
};
