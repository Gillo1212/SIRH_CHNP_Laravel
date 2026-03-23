<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dossier_agents', function (Blueprint $table) {
            $table->id('id_dossier');
            
            // Relations
            $table->foreignId('id_etagere')
                  ->constrained('etageres', 'id_etagere')
                  ->onDelete('cascade'); // Composition
            
            $table->foreignId('id_agent')
                  ->unique() // Relation 1-1
                  ->constrained('agents', 'id_agent')
                  ->onDelete('cascade');
            
            // Informations dossier
            $table->string('reference', 50)->unique(); // Ex: DOSS-2024-0001
            $table->timestamp('date_creation')->useCurrent();
            
            $table->enum('statut_da', [
                'Actif',
                'Archivé',
                'Clôturé'
            ])->default('Actif');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dossier_agents');
    }
};