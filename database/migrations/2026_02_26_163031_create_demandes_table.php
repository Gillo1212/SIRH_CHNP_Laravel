<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demandes', function (Blueprint $table) {
            $table->id('id_demande');
            
            // Relation avec agent
            $table->foreignId('id_agent')
                  ->constrained('agents', 'id_agent')
                  ->onDelete('cascade');
            
            // Type de demande (discriminant pour héritage)
            $table->enum('type_demande', ['Conge', 'Absence', 'PriseEnCharge']);
            
            // Workflow de validation
            $table->timestamp('date_demande')->useCurrent();
            $table->enum('statut_demande', [
                'En_attente',
                'Validé',
                'Approuvé',
                'Rejeté'
            ])->default('En_attente');
            
            $table->text('motif_refus')->nullable();
            $table->timestamp('date_traitement')->nullable();
            
            $table->timestamps();
            
            // Index
            $table->index(['id_agent', 'statut_demande']);
            $table->index('type_demande');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demandes');
    }
};