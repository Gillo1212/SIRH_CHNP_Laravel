<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ligne_plannings', function (Blueprint $table) {
            $table->id('id_ligne');
            
            // Relations
            $table->foreignId('id_planning')
                  ->constrained('plannings', 'id_planning')
                  ->onDelete('cascade'); // Composition
            
            $table->foreignId('id_agent')
                  ->constrained('agents', 'id_agent')
                  ->onDelete('cascade');
            
            $table->foreignId('id_typeposte')
                  ->constrained('type_postes', 'id_typeposte')
                  ->onDelete('restrict');
            
            // Informations ligne
            $table->date('date_poste');
            $table->time('heure_debut');
            $table->time('heure_fin'); // Peut être J+1 pour nuits
            
            $table->timestamps();
            
            // Index
            $table->index(['id_planning', 'date_poste']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ligne_plannings');
    }
};