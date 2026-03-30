<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organigramme', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 200)->default('Organigramme CHNP');
            $table->longText('donnees_json');
            $table->foreignId('cree_par')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organigramme');
    }
};
