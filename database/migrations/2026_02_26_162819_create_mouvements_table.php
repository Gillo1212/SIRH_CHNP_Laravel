<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mouvements', function (Blueprint $table) {
            $table->id('id_mouvement');
            
            // Relations
            $table->foreignId('id_agent')
                  ->constrained('agents', 'id_agent')
                  ->onDelete('cascade');
            
            $table->foreignId('id_service')
                  ->constrained('services', 'id_service')
                  ->onDelete('cascade');
            
            // Informations mouvement
            $table->date('date_mouvement');
            $table->enum('type_mouvement', [
                'Affectation initiale',
                'Mutation',
                'Retour',
                'Départ'
            ]);
            $table->string('motif', 255)->nullable();
            
            $table->timestamps();
            
            // Index
            $table->index(['id_agent', 'date_mouvement']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mouvements');
    }
};