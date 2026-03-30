<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Correction hiérarchie hospitalière :
 * Un SERVICE contient des DIVISIONS (et non l'inverse).
 *
 * - Supprime id_division de la table services
 * - Ajoute id_service à la table divisions
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Ajouter id_service sur divisions
        Schema::table('divisions', function (Blueprint $table) {
            $table->foreignId('id_service')
                ->nullable()
                ->after('nom_division')
                ->constrained('services', 'id_service')
                ->onDelete('set null');
        });

        // 2. Supprimer id_division de services
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['id_division']);
            $table->dropColumn('id_division');
        });
    }

    public function down(): void
    {
        // Remettre id_division sur services
        Schema::table('services', function (Blueprint $table) {
            $table->foreignId('id_division')
                ->nullable()
                ->constrained('divisions', 'id_division')
                ->onDelete('set null');
        });

        // Supprimer id_service de divisions
        Schema::table('divisions', function (Blueprint $table) {
            $table->dropForeign(['id_service']);
            $table->dropColumn('id_service');
        });
    }
};
