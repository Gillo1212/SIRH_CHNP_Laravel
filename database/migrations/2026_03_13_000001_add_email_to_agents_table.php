<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            // Email professionnel de l'agent (après telephone)
            $table->string('email', 150)
                  ->nullable()
                  ->unique()
                  ->after('telephone')
                  ->comment('Email professionnel - données sensibles');

            // Index pour recherche rapide par email
            // (unique() crée déjà un index)
        });
    }

    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->dropColumn('email');
        });
    }
};
