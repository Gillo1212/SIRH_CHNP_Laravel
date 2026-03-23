<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrats', function (Blueprint $table) {
            $table->id('id_contrat');
            
            // Relation avec agent
            $table->foreignId('id_agent')
                  ->constrained('agents', 'id_agent')
                  ->onDelete('cascade');
            
            // Informations contrat
            $table->date('date_debut');
            $table->date('date_fin')->nullable(); // NULL pour CDI/PE
            $table->decimal('salaire_base', 10, 2)->nullable(); // Chiffré en base
            
            $table->enum('statut_contrat', [
                'Actif',
                'Expiré',
                'Clôturé',
                'En_renouvellement'
            ])->default('Actif');
            
            $table->enum('type_contrat', [
                'PE',
                'PCH',
                'PU',
                'Vacataire',
                'CMSAS',
                'Interne',
                'Stagiaire'
            ]);
            
            $table->text('observation')->nullable();
            
            $table->timestamps();
            
            // Index pour alertes d'expiration
            $table->index(['statut_contrat', 'date_fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrats');
    }
};