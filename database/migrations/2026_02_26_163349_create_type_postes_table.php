<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('type_postes', function (Blueprint $table) {
            $table->id('id_typeposte');
            
            $table->string('libelle', 50); // Jour, Nuit, Garde, etc.
            $table->string('description', 255)->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('type_postes');
    }
};