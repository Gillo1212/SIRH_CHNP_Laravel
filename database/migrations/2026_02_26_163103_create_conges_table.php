<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conges', function (Blueprint $table) {
            $table->id('id_conge');
            
            // Relation avec demande (UNIQUE car héritage 1-1)
            $table->foreignId('id_demande')
                  ->unique()
                  ->constrained('demandes', 'id_demande')
                  ->onDelete('cascade');
            
            // Relation avec type de congé
            $table->foreignId('id_type_conge')
                  ->constrained('type_conges', 'id_type_conge')
                  ->onDelete('restrict');
            
            // Informations congé
            $table->date('date_debut');
            $table->date('date_fin');
            $table->integer('nbres_jours'); // Calculé automatiquement
            $table->date('date_approbation')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conges');
    }
};