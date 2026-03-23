<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Corrige la colonne `theme` qui peut avoir une ancienne définition ENUM
     * sans la valeur 'system', causant une erreur "Data truncated".
     */
    public function up(): void
    {
        // Mettre d'abord les valeurs invalides à 'light' avant de modifier l'enum
        DB::table('user_preferences')
            ->whereNotIn('theme', ['light', 'dark', 'system'])
            ->update(['theme' => 'light']);

        // Modifier la colonne avec la définition complète
        DB::statement("ALTER TABLE user_preferences MODIFY COLUMN theme ENUM('light', 'dark', 'system') NOT NULL DEFAULT 'system'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE user_preferences MODIFY COLUMN theme ENUM('light', 'dark') NOT NULL DEFAULT 'light'");
    }
};
