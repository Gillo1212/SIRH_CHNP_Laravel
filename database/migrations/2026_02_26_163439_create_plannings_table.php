<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plannings', function (Blueprint $table) {
            $table->id('id_planning');
            
            // Relation avec service
            $table->foreignId('id_service')
                  ->constrained('services', 'id_service')
                  ->onDelete('cascade');
            
            // Informations planning
            $table->date('periode_debut');
            $table->date('periode_fin');
            
            $table->enum('statut_planning', [
                'Brouillon',
                'Transmis',
                'Validé',
                'Rejeté'
            ])->default('Brouillon');
            
            $table->text('motif_rejet')->nullable();
            $table->timestamp('date_creation')->useCurrent();
            
            $table->timestamps();
            
            // Index
            $table->index(['id_service', 'statut_planning']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plannings');
    }
};