<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('type_conges', function (Blueprint $table) {
            $table->id('id_type_conge');
            
            $table->string('libelle', 100); // Administratif, Maternité, etc.
            $table->string('duree', 50)->nullable(); // Durée légale (texte)
            $table->integer('nb_jours_droit'); // Nombre de jours par an
            $table->boolean('deductible')->default(true); // Déductible du crédit annuel
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('type_conges');
    }
};