<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solde_conges', function (Blueprint $table) {
            $table->id('id_solde');
            
            // Relations (classe d'association Agent ↔ Type_Conge)
            $table->foreignId('id_agent')
                  ->constrained('agents', 'id_agent')
                  ->onDelete('cascade');
            
            $table->foreignId('id_type_conge')
                  ->constrained('type_conges', 'id_type_conge')
                  ->onDelete('cascade');
            
            // Informations solde
            $table->integer('annee'); // 2024, 2025...
            $table->integer('solde_initial');
            $table->integer('solde_pris')->default(0);
            $table->integer('solde_restant'); // Calculé : solde_initial - solde_pris
            
            $table->timestamps();
            
            // Contrainte unique : un solde par agent/type/année
            $table->unique(['id_agent', 'id_type_conge', 'annee']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solde_conges');
    }
};