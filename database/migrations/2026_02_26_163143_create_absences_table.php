<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absences', function (Blueprint $table) {
            $table->id('id_absence');
            
            // Relation avec demande (UNIQUE car héritage 1-1)
            $table->foreignId('id_demande')
                  ->unique()
                  ->constrained('demandes', 'id_demande')
                  ->onDelete('cascade');
            
            // Informations absence
            $table->date('date_absence');
            $table->enum('type_absence', [
                'Maladie',
                'Personnelle',
                'Professionnelle',
                'Injustifiée'
            ]);
            $table->boolean('justifie')->default(false);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absences');
    }
};