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
        Schema::create('demandes_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')
                  ->constrained('agents', 'id_agent')
                  ->onDelete('cascade');
            $table->enum('type_document', [
                'attestation_travail',
                'certificat_travail',
                'ordre_mission',
            ]);
            $table->text('motif')->nullable();
            $table->enum('statut', ['en_attente', 'en_cours', 'pret', 'rejete'])->default('en_attente');
            $table->unsignedBigInteger('traite_par')->nullable();
            $table->foreign('traite_par')->references('id')->on('users')->nullOnDelete();
            $table->timestamp('date_traitement')->nullable();
            $table->string('fichier_genere')->nullable();
            $table->text('motif_rejet')->nullable();
            $table->timestamps();

            $table->index(['agent_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demandes_documents');
    }
};
