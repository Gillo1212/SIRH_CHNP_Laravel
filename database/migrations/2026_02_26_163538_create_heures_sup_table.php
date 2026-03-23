<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('heures_sup', function (Blueprint $table) {
            $table->id('id_hsup');
            
            // Relation avec ligne_planning (1-1 ou 1-0..1)
            $table->foreignId('id_ligne')
                  ->constrained('ligne_plannings', 'id_ligne')
                  ->onDelete('cascade');
            
            // Informations heures sup
            $table->decimal('nb_heures', 5, 2);
            $table->decimal('taux', 5, 2);
            $table->decimal('montant', 10, 2); // Calculé : nb_heures * taux (CRITIQUE - chiffré)
            
            $table->enum('periode', ['Trimestre', 'Semestre']);
            $table->enum('statut_hs', [
                'En_attente',
                'Validé',
                'Payé'
            ])->default('En_attente');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('heures_sup');
    }
};