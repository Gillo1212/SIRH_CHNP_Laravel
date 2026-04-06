<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migration pour étendre les types de documents administratifs.
 * 
 * Ajoute les nouveaux types de documents conformément aux modèles
 * administratifs officiels du Centre Hospitalier National de Pikine.
 * 
 * @author Gilbert - Mémoire M2 SIRH CHNP
 */
return new class extends Migration
{
    public function up(): void
    {
        // Modifier l'ENUM pour ajouter les nouveaux types
        DB::statement("ALTER TABLE demandes_documents MODIFY COLUMN type_document ENUM(
            'attestation_travail',
            'certificat_travail',
            'ordre_mission',
            'decision_conge_administratif',
            'attestation_jouissance_conge',
            'attestation_cessation_maternite',
            'note_affectation',
            'note_interim',
            'attestation_prime_motivation',
            'attestation_prise_service',
            'attestation_stage',
            'autorisation_sortie_territoire'
        ) NOT NULL");

        // Ajouter les colonnes pour les données spécifiques
        Schema::table('demandes_documents', function (Blueprint $table) {
            $table->json('donnees_specifiques')->nullable()->after('motif')
                  ->comment('Données variables selon le type de document');
            
            $table->string('numero_reference', 100)->nullable()->after('fichier_genere')
                  ->comment('Numéro de référence officiel du document');
            
            $table->date('date_debut_validite')->nullable()->after('numero_reference');
            $table->date('date_fin_validite')->nullable()->after('date_debut_validite');
            
            $table->unsignedBigInteger('agent_remplacant_id')->nullable()->after('date_fin_validite');
            $table->foreign('agent_remplacant_id')
                  ->references('id_agent')
                  ->on('agents')
                  ->nullOnDelete();
            
            $table->unsignedBigInteger('service_destination_id')->nullable()->after('agent_remplacant_id');
            $table->foreign('service_destination_id')
                  ->references('id_service')
                  ->on('services')
                  ->nullOnDelete();

            $table->index(['type_document', 'statut']);
            $table->index('numero_reference');
        });
    }

    public function down(): void
    {
        Schema::table('demandes_documents', function (Blueprint $table) {
            $table->dropForeign(['agent_remplacant_id']);
            $table->dropForeign(['service_destination_id']);
            
            $table->dropColumn([
                'donnees_specifiques',
                'numero_reference',
                'date_debut_validite',
                'date_fin_validite',
                'agent_remplacant_id',
                'service_destination_id',
            ]);
            
            $table->dropIndex(['type_document', 'statut']);
            $table->dropIndex(['numero_reference']);
        });

        DB::statement("ALTER TABLE demandes_documents MODIFY COLUMN type_document ENUM(
            'attestation_travail',
            'certificat_travail',
            'ordre_mission'
        ) NOT NULL");
    }
};
