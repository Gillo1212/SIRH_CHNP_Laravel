<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cette migration est une correction pour aligner le schéma de la table agents
     * avec la structure réelle de la base de données.
     *
     * Structure réelle de la table agents (colonnes de données) :
     * nom, prenom, categorie_cp, famille_d_emploi, statut_agent, sexe, matricule,
     * date_naissance, telephone (encrypted), religion, cni (encrypted), lieu_naissance,
     * situation_familiale, nationalite, adresse (encrypted), email,
     * date_prise_service, fontion, grade, account_pending, photo
     */
    public function up(): void
    {
        Schema::table('agents', function (Blueprint $table) {

            // ── Ajouter les colonnes manquantes si elles n'existent pas ──────────
            if (! Schema::hasColumn('agents', 'famille_d_emploi')) {
                $table->string('famille_d_emploi', 100)->nullable()->after('categorie_cp');
            }

            if (! Schema::hasColumn('agents', 'religion')) {
                $table->string('religion', 50)->nullable()->after('telephone');
            }

            if (! Schema::hasColumn('agents', 'cni')) {
                $table->text('cni')->nullable()->after('religion'); // Chiffré AES-256
            }

            // ── Renommer statut → statut_agent si nécessaire ─────────────────────
            if (Schema::hasColumn('agents', 'statut') && ! Schema::hasColumn('agents', 'statut_agent')) {
                $table->renameColumn('statut', 'statut_agent');
            }

            // ── Renommer statut_contrat → statut_agent si nécessaire ─────────────
            if (Schema::hasColumn('agents', 'statut_contrat') && ! Schema::hasColumn('agents', 'statut_agent')) {
                $table->renameColumn('statut_contrat', 'statut_agent');
            }

            // ── Renommer date_recrutement → date_prise_service si nécessaire ─────
            if (Schema::hasColumn('agents', 'date_recrutement') && ! Schema::hasColumn('agents', 'date_prise_service')) {
                $table->renameColumn('date_recrutement', 'date_prise_service');
            }

            // ── Renommer fonction → fontion si nécessaire ────────────────────────
            if (Schema::hasColumn('agents', 'fonction') && ! Schema::hasColumn('agents', 'fontion')) {
                $table->renameColumn('fonction', 'fontion');
            }
        });
    }

    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            if (Schema::hasColumn('agents', 'statut_agent') && ! Schema::hasColumn('agents', 'statut')) {
                $table->renameColumn('statut_agent', 'statut');
            }
            if (Schema::hasColumn('agents', 'date_prise_service') && ! Schema::hasColumn('agents', 'date_recrutement')) {
                $table->renameColumn('date_prise_service', 'date_recrutement');
            }
            if (Schema::hasColumn('agents', 'fontion') && ! Schema::hasColumn('agents', 'fonction')) {
                $table->renameColumn('fontion', 'fonction');
            }
        });
    }
};
