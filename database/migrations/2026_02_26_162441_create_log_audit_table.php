<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_audit', function (Blueprint $table) {
            $table->id('id_log');
            
            // Relation avec utilisateur
            $table->foreignId('id_utilisateur')
                  ->constrained('users', 'id')
                  ->onDelete('cascade');
            
            // Informations d'audit
            $table->string('action', 50); // CREATION, MODIFICATION, SUPPRESSION
            $table->string('table_cible', 50);
            $table->text('details')->nullable();
            $table->string('adresse_ip', 45)->nullable(); // Support IPv6
            
            $table->timestamp('date_evenement')->useCurrent();
            
            // Index pour optimiser les recherches
            $table->index('action');
            $table->index('table_cible');
            $table->index('date_evenement');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_audit');
    }
};