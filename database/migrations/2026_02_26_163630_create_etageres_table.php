<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etageres', function (Blueprint $table) {
            $table->id('id_etagere');
            
            // Relation avec service
            $table->foreignId('id_service')
                  ->constrained('services', 'id_service')
                  ->onDelete('cascade');
            
            // Informations étagère
            $table->string('nom_etagere', 100);
            $table->string('numero', 20)->nullable();
            $table->integer('reference')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etageres');
    }
};