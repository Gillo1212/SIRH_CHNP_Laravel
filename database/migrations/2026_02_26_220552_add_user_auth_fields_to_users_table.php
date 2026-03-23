<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Champs manquants pour la gestion de connexion (Utilisateur)
            $table->string('statut_compte', 20)->default('Actif')->after('password');
            $table->boolean('verouille')->default(false)->after('statut_compte');
            $table->integer('tentatives_connexion')->default(0)->after('verouille');
            $table->timestamp('derniere_connexion')->nullable()->after('remember_token');
            
            // Index pour optimisation
            $table->index('statut_compte');
            $table->index('verouille');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['statut_compte']);
            $table->dropIndex(['verouille']);
            
            $table->dropColumn([
                'statut_compte',
                'verouille',
                'tentatives_connexion',
                'derniere_connexion'
            ]);
        });
    }
};