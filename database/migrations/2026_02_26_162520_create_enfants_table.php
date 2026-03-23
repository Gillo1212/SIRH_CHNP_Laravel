<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enfants', function (Blueprint $table) {
            $table->id('id_enfant');
            
            // Relation avec agent (composition - ON DELETE CASCADE)
            $table->foreignId('id_agent')
                  ->constrained('agents', 'id_agent')
                  ->onDelete('cascade');
            
            // Informations enfant
            $table->string('prenom_complet', 100);
            $table->date('date_naissance_enfant');
            $table->enum('lien_filiation', ['Fils', 'Fille']);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enfants');
    }
};