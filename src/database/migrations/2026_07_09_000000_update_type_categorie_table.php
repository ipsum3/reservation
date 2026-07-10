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
        // 1. On ajoute les colonnes (slug est nullable au début pour éviter les erreurs sur les données existantes)
        Schema::table('categorie_types', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('id');
        });

        // 2. On remplit le slug pour chaque enregistrement existant
        // On utilise DB::table()->chunk() au cas où il y aurait beaucoup de lignes (bonne pratique)
        DB::table('categorie_types')->orderBy('id')->chunk(100, function ($types) {
            foreach ($types as $type) {
                // On génère le slug à partir du champ 'nom' (ajuste si ta colonne s'appelle autrement, ex: 'title')
                $slug = Str::slug($type->nom);

                // Optionnel : Sécurité pour gérer les doublons de noms s'il y en a dans ta table
                $originalSlug = $slug;
                $count = 1;
                while (DB::table('categorie_types')->where('slug', $slug)->where('id', '!=', $type->id)->exists()) {
                    $slug = $originalSlug . '-' . $count;
                    $count++;
                }

                DB::table('categorie_types')
                    ->where('id', $type->id)
                    ->update(['slug' => $slug]);
            }
        });

        // 3. Une fois tous les slugs remplis, on applique la contrainte UNIQUE et NON NULL
        Schema::table('categorie_types', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable(false)->change();
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
