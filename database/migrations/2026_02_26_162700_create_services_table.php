<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id('id_service');
            
            // Relation avec division (agrégation - nullable)
            $table->foreignId('id_division')
                  ->nullable()
                  ->constrained('divisions', 'id_division')
                  ->onDelete('set null');
            
            // Relation avec agent manager (nullable)
            $table->foreignId('id_agent_manager')
                  ->nullable()
                  ->constrained('users', 'id')
                  ->onDelete('set null');
            
            // Informations service
            $table->string('nom_service', 100);
            $table->enum('type_service', [
                'Clinique',
                'Administratif',
                'Aide_diagnostic',
                'Support'
            ]);
            $table->string('tel_service', 20)->nullable();
            $table->integer('nbre_agents')->default(0);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};