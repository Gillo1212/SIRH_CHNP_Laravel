<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->id('id_agent');

            // Relation 1-1 avec users (auth)
            $table->foreignId('user_id')
                  ->unique()
                  ->constrained('users', 'id')
                  ->onDelete('cascade');

            // Matricule unique
            $table->string('matricule', 20)->unique();

            // Données personnelles
            $table->string('nom', 100);
            $table->string('prenom', 100);
            $table->date('date_naissance');
            $table->string('lieu_naissance', 100);
            $table->enum('sexe', ['M', 'F']);
            $table->enum('situation_familiale', ['Célibataire', 'Marié', 'Divorcé', 'Veuf'])->nullable();
            $table->string('nationalite', 50)->default('Sénégalaise');

            // Coordonnées (sensibles)
            $table->text('adresse')->nullable();
            $table->string('telephone', 20)->nullable();

            // Données professionnelles
            $table->date('date_recrutement');
            $table->string('fonction', 100)->nullable();
            $table->string('grade', 20)->nullable();
            $table->enum('categorie_cp', [
                'Cadre_Superieur',
                'Cadre_Moyen',
                'Technicien_Superieur',
                'Technicien',
                'Agent_Administratif',
                'Agent_de_Service',
                'Commis_Administration',
                'Ouvrier',
                'Sans_Diplome',
            ])->nullable();
            $table->string('numero_assurance', 50)->nullable();
            $table->enum('statut', ['Actif', 'En_congé', 'Suspendu', 'Retraité'])->default('Actif');
            $table->string('photo', 255)->nullable();

            // service_id / division_id ajoutés après création de ces tables
            // (voir migration add_service_division_to_users_table)

            $table->timestamps();

            $table->index('matricule');
            $table->index('statut');
            $table->index(['nom', 'prenom']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
