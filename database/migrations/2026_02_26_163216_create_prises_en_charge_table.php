<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prises_en_charge', function (Blueprint $table) {
            $table->id('id_priseenche');
            
            // Relation avec demande (UNIQUE car héritage 1-1)
            $table->foreignId('id_demande')
                  ->unique()
                  ->constrained('demandes', 'id_demande')
                  ->onDelete('cascade');
            
            // Informations prise en charge
            $table->string('raison_medical', 255); // Données sensibles CRITIQUE
            $table->string('ayant_droit', 100)->nullable(); // Agent, conjoint, enfant
            $table->date('date_edition')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prises_en_charge');
    }
};