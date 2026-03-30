<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mouvements', function (Blueprint $table) {
            // Service origine (null = affectation initiale / première assignation)
            $table->unsignedBigInteger('id_service_origine')->nullable()->after('id_service');
            $table->foreign('id_service_origine')
                  ->references('id_service')->on('services')
                  ->nullOnDelete();

            // Rendre id_service nullable (Départ = pas de service de destination)
            $table->unsignedBigInteger('id_service')->nullable()->change();

            // Workflow statut (en_attente → valide_drh → effectue | annule)
            $table->enum('statut', ['en_attente', 'valide_drh', 'effectue', 'annule'])
                  ->default('en_attente')
                  ->after('motif');

            // Traçabilité — Intégrité CID
            $table->unsignedBigInteger('cree_par')->nullable()->after('statut');
            $table->foreign('cree_par')->references('id')->on('users')->nullOnDelete();

            $table->unsignedBigInteger('valide_par')->nullable()->after('cree_par');
            $table->foreign('valide_par')->references('id')->on('users')->nullOnDelete();

            $table->timestamp('date_validation')->nullable()->after('valide_par');

            // Document généré (décision d'affectation PDF)
            $table->string('decision_generee')->nullable()->after('date_validation');
        });
    }

    public function down(): void
    {
        Schema::table('mouvements', function (Blueprint $table) {
            $table->dropForeign(['id_service_origine']);
            $table->dropForeign(['cree_par']);
            $table->dropForeign(['valide_par']);
            $table->dropColumn([
                'id_service_origine', 'statut', 'cree_par',
                'valide_par', 'date_validation', 'decision_generee',
            ]);
            $table->unsignedBigInteger('id_service')->nullable(false)->change();
        });
    }
};
