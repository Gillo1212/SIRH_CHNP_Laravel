<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Supprimer la table pieces_justificatives
        Schema::dropIfExists('pieces_justificatives');

        // Ajouter justificatif_path directement sur absences
        Schema::table('absences', function (Blueprint $table) {
            $table->string('justificatif_path')->nullable()->after('justifie');
        });
    }

    public function down(): void
    {
        Schema::table('absences', function (Blueprint $table) {
            $table->dropColumn('justificatif_path');
        });

        Schema::create('pieces_justificatives', function (Blueprint $table) {
            $table->id('id_piece');
            $table->foreignId('id_absence')
                  ->constrained('absences', 'id_absence')
                  ->onDelete('cascade');
            $table->enum('type_piece', ['Certificat médical', 'Acte décès', 'Convocation']);
            $table->string('fichier_url', 255);
            $table->timestamp('date_depot')->useCurrent();
            $table->boolean('valide')->default(false);
            $table->timestamps();
        });
    }
};
