<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ajoute 'Diffusé' à l'ENUM : statut final après transmission Manager → RH
        DB::statement("ALTER TABLE plannings MODIFY COLUMN statut_planning ENUM('Brouillon','Transmis','Validé','Rejeté','Diffusé') NOT NULL DEFAULT 'Brouillon'");
    }

    public function down(): void
    {
        DB::table('plannings')->where('statut_planning', 'Diffusé')->update(['statut_planning' => 'Validé']);
        DB::statement("ALTER TABLE plannings MODIFY COLUMN statut_planning ENUM('Brouillon','Transmis','Validé','Rejeté') NOT NULL DEFAULT 'Brouillon'");
    }
};
