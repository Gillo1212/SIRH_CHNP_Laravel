<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Refactoring du module Heures Supplémentaires.
 *
 * Nouvelle logique :
 *   - statut_hs : En_attente → Déclaré | Validé → Conforme | + Anomalie
 *   - note_verification : note laissée par la RH lors d'une anomalie
 *
 * La RH ne valide plus — elle vérifie la conformité.
 * Le Major reste l'autorité de déclaration terrain.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Élargir l'enum pour accepter les anciennes ET les nouvelles valeurs
        DB::statement("ALTER TABLE heures_sup MODIFY COLUMN statut_hs ENUM('En_attente','Validé','Payé','Déclaré','Conforme','Anomalie') NOT NULL DEFAULT 'En_attente'");

        // 2. Migrer les données existantes vers les nouveaux statuts
        DB::statement("UPDATE heures_sup SET statut_hs = 'Déclaré'  WHERE statut_hs = 'En_attente'");
        DB::statement("UPDATE heures_sup SET statut_hs = 'Conforme' WHERE statut_hs = 'Validé'");
        DB::statement("UPDATE heures_sup SET statut_hs = 'Conforme' WHERE statut_hs = 'Payé'");

        // 3. Restreindre l'enum aux nouvelles valeurs uniquement
        DB::statement("ALTER TABLE heures_sup MODIFY COLUMN statut_hs ENUM('Déclaré','Conforme','Anomalie') NOT NULL DEFAULT 'Déclaré'");

        // 4. Ajouter la colonne note_verification
        Schema::table('heures_sup', function (Blueprint $table) {
            $table->text('note_verification')->nullable()->after('statut_hs')
                  ->comment('Note laissée par la RH lors du signalement d\'une anomalie');
        });
    }

    public function down(): void
    {
        Schema::table('heures_sup', function (Blueprint $table) {
            $table->dropColumn('note_verification');
        });

        DB::statement("UPDATE heures_sup SET statut_hs = 'En_attente' WHERE statut_hs = 'Déclaré'");
        DB::statement("UPDATE heures_sup SET statut_hs = 'Validé'     WHERE statut_hs = 'Conforme'");
        DB::statement("UPDATE heures_sup SET statut_hs = 'En_attente' WHERE statut_hs = 'Anomalie'");

        DB::statement("ALTER TABLE heures_sup MODIFY COLUMN statut_hs ENUM('En_attente','Validé','Payé') NOT NULL DEFAULT 'En_attente'");
    }
};
