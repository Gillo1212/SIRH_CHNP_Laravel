<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id('id_document');
            
            // Relation avec dossier agent (composition - ON DELETE CASCADE)
            $table->foreignId('id_dossier')
                  ->constrained('dossier_agents', 'id_dossier')
                  ->onDelete('cascade');
            
            // Informations document
            $table->string('reference', 50);
            $table->string('titre', 200);
            $table->string('mots_cles', 255)->nullable(); // Pour indexation
            $table->date('date_creation')->nullable(); // Date création originale
            $table->timestamp('date_archivage')->useCurrent(); // Date archivage système
            $table->string('document_url', 255); // Chemin sécurisé
            
            $table->enum('type_document', [
                'Contrat',
                'Attestation',
                'Décision',
                'Ordre_mission',
                'Nomination',
                'PV',
                'Domiciliation'
            ]);
            
            $table->timestamps();
            
            // Index pour recherche
            $table->index('type_document');
            $table->fullText(['titre', 'mots_cles']); // Recherche textuelle
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};