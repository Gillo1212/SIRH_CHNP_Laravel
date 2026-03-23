<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Rendre user_id nullable dans agents pour permettre la création du dossier
     * AVANT la création du compte utilisateur (workflow séparé RH → Admin).
     */
    public function up(): void
    {
        // Supprimer la FK, modifier la colonne, recréer la FK (sans recréer l'index unique déjà existant)
        Schema::table('agents', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // Modifier directement via SQL pour rendre nullable sans toucher l'index unique
        DB::statement('ALTER TABLE agents MODIFY COLUMN user_id BIGINT UNSIGNED NULL');

        Schema::table('agents', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        DB::statement('ALTER TABLE agents MODIFY COLUMN user_id BIGINT UNSIGNED NOT NULL');

        Schema::table('agents', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }
};
