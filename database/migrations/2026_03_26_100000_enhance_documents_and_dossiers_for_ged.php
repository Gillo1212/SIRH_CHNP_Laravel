<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Colonnes GED sur la table documents ─────────────────────
        Schema::table('documents', function (Blueprint $table) {
            // Cycle de vie du document
            $table->enum('statut_document', ['Actif', 'Archivé', 'Détruit'])
                  ->default('Actif')
                  ->after('type_document');

            // Niveau de confidentialité (Triade CID — Confidentialité)
            $table->enum('niveau_confidentialite', ['Public', 'Interne', 'Confidentiel', 'Secret'])
                  ->default('Interne')
                  ->after('statut_document');

            // Métadonnées fichier
            $table->string('format_fichier', 50)->nullable()->after('document_url');   // pdf, jpg, docx…
            $table->unsignedBigInteger('taille_fichier')->nullable()->after('format_fichier'); // octets

            // Versioning
            $table->string('version', 10)->default('1.0')->after('taille_fichier');

            // Description / notes
            $table->text('description')->nullable()->after('version');

            // Qui a déposé le document
            $table->foreignId('charge_par')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->after('description');

            // Date de destruction effective
            $table->timestamp('date_destruction')->nullable()->after('charge_par');

            // Index supplémentaire
            $table->index('statut_document');
            $table->index('niveau_confidentialite');
        });

        // Modifier l'enum type_document pour ajouter de nouveaux types
        DB::statement("ALTER TABLE documents MODIFY COLUMN type_document ENUM(
            'Contrat',
            'Attestation',
            'Décision',
            'Ordre_mission',
            'Nomination',
            'PV',
            'Domiciliation',
            'Diplome',
            'Certificat_medical',
            'Fiche_evaluation',
            'Piece_identite',
            'Autre'
        ) NOT NULL");

        // ── 2. Colonnes supplémentaires sur dossier_agents ─────────────
        Schema::table('dossier_agents', function (Blueprint $table) {
            $table->text('description')->nullable()->after('statut_da');
            $table->string('notes', 500)->nullable()->after('description');
            $table->timestamp('date_archivage')->nullable()->after('notes');
            $table->timestamp('date_cloture')->nullable()->after('date_archivage');
        });

        // ── 3. Colonnes supplémentaires sur etageres ───────────────────
        Schema::table('etageres', function (Blueprint $table) {
            $table->text('description')->nullable()->after('reference');
            $table->boolean('actif')->default(true)->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['statut_document']);
            $table->dropIndex(['niveau_confidentialite']);
            $table->dropForeign(['charge_par']);
            $table->dropColumn([
                'statut_document', 'niveau_confidentialite',
                'format_fichier', 'taille_fichier',
                'version', 'description', 'charge_par', 'date_destruction',
            ]);
        });
        Schema::table('dossier_agents', function (Blueprint $table) {
            $table->dropColumn(['description', 'notes', 'date_archivage', 'date_cloture']);
        });
        Schema::table('etageres', function (Blueprint $table) {
            $table->dropColumn(['description', 'actif']);
        });
    }
};
