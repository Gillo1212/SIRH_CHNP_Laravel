<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conjoints', function (Blueprint $table) {
            $table->id('id_conjoint');
            
            // Relation avec agent (composition - ON DELETE CASCADE)
            $table->foreignId('id_agent')
                  ->constrained('agents', 'id_agent')
                  ->onDelete('cascade');
            
            // Informations conjoint
            $table->string('nom_conj', 100);
            $table->string('prenom_conj', 100);
            $table->date('date_naissance_conj')->nullable();
            $table->enum('type_lien', ['Époux', 'Épouse']);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conjoints');
    }
};