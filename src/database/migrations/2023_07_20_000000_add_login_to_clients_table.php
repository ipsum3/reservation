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
        // Vérifier si la colonne "email" existe avec l'index unique avant de le supprimer
        $tableExists = Schema::hasColumn('clients', 'email');
        $indexExists = false;
        if ($tableExists) {
            $indexes = DB::select("SHOW INDEX FROM clients WHERE Column_name = 'email' AND Non_unique = 0");
            $indexExists = count($indexes) > 0;
        }

        if ($tableExists && $indexExists) {
            Schema::table('clients', function (Blueprint $table) {
                $table->dropUnique(['email']);
            });
        }

        // Ajouter le champ "login" dans la table "clients"
        Schema::table('clients', function (Blueprint $table) {
            $table->string('login')->unique()->nullable()->after('email');
        });

        // Pré-remplir le champ "login" avec la valeur de "email"
        Client::whereNull('login')->update(['login' => Client::raw('email')]);
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
