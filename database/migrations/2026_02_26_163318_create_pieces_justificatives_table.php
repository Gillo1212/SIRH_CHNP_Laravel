<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pieces_justificatives', function (Blueprint $table) {
            $table->id('id_piece');
            
            // Relation avec absence (composition - ON DELETE CASCADE)
            $table->foreignId('id_absence')
                  ->constrained('absences', 'id_absence')
                  ->onDelete('cascade');
            
            // Informations pièce
            $table->enum('type_piece', [
                'Certificat médical',
                'Acte décès',
                'Convocation'
            ]);
            $table->string('fichier_url', 255); // Chemin sécurisé
            $table->timestamp('date_depot')->useCurrent();
            $table->boolean('valide')->default(false);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pieces_justificatives');
    }
};