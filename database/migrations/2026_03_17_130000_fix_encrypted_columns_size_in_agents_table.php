<?php

/**
 * Migration : Correction taille colonnes chiffrées (AES-256)
 *
 * PROBLÈME : Le chiffrement Laravel Crypt produit des chaînes base64 de ~200+ chars.
 * telephone et numero_assurance étaient en VARCHAR → troncature → SQLSTATE[22001].
 *
 * SOLUTION : TEXT (65 535 chars) pour les 3 colonnes avec cast 'encrypted'.
 *
 * TRIADE CID :
 *   Confidentialité : chiffrement AES-256 préservé
 *   Intégrité       : données stockées sans troncature
 *   Disponibilité   : module Personnel opérationnel
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE agents MODIFY COLUMN telephone TEXT NULL');
        DB::statement('ALTER TABLE agents MODIFY COLUMN adresse TEXT NULL');
        DB::statement('ALTER TABLE agents MODIFY COLUMN numero_assurance TEXT NULL');
    }

    public function down(): void
    {
        // ATTENTION : peut tronquer des données chiffrées existantes
        DB::statement('ALTER TABLE agents MODIFY COLUMN telephone VARCHAR(50) NULL');
        DB::statement('ALTER TABLE agents MODIFY COLUMN adresse TEXT NULL');
        DB::statement('ALTER TABLE agents MODIFY COLUMN numero_assurance VARCHAR(50) NULL');
    }
};
