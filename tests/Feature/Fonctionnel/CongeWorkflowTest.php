<?php

namespace Tests\Feature\Fonctionnel;

use App\Models\Conge;
use App\Models\Demande;
use App\Models\SoldeConge;
use Tests\SirhTestCase;

/**
 * Tests fonctionnels — Workflow Congés 3 niveaux (Section 4.2.1.1)
 *
 * Valide le workflow complet :
 *   Agent (demande) → Major (avis) → Manager (validation) → AgentRH (approbation)
 */
class CongeWorkflowTest extends SirhTestCase
{
    private $service;
    private $typeConge;
    private $userAgent;
    private $agentModel;
    private $userManager;
    private $userRH;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->creerService();
        $this->typeConge = $this->creerTypeConge();

        // Créer l'agent
        $this->userAgent = $this->creerAgent();
        $this->agentModel = $this->creerDossierAgent($this->userAgent, $this->service);

        // Manager affecté au service (avec profil agent requis par le contrôleur)
        $this->userManager = $this->creerManager();
        $this->creerDossierAgent($this->userManager, $this->service);
        $this->service->update(['id_agent_manager' => $this->userManager->id]);

        // AgentRH
        $this->userRH = $this->creerAgentRH();

        // Solde congé suffisant
        SoldeConge::create([
            'id_agent'      => $this->agentModel->id_agent,
            'id_type_conge' => $this->typeConge->id_type_conge,
            'annee'         => now()->year,
            'solde_initial' => 30,
            'solde_pris'    => 0,
            'solde_restant' => 30,
        ]);
    }

    // ──────────────────────────────────────────────────────
    // ÉTAPE 1 : Agent soumet une demande de congé
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_agent_peut_acceder_au_formulaire_de_demande_de_conge(): void
    {
        $response = $this->actingAs($this->userAgent)
            ->get('/agent/conges/create');

        $response->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_agent_peut_soumettre_une_demande_de_conge(): void
    {
        $response = $this->actingAs($this->userAgent)
            ->post('/agent/conges', [
                'id_type_conge' => $this->typeConge->id_type_conge,
                'date_debut'    => now()->addDays(5)->format('Y-m-d'),
                'date_fin'      => now()->addDays(10)->format('Y-m-d'),
                'motif'         => 'Congé annuel de repos',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('demandes', [
            'id_agent'       => $this->agentModel->id_agent,
            'type_demande'   => 'Conge',
            'statut_demande' => 'En_attente',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_agent_ne_peut_pas_demander_un_conge_si_solde_insuffisant(): void
    {
        // Épuiser le solde
        SoldeConge::where('id_agent', $this->agentModel->id_agent)->update([
            'solde_pris'    => 30,
            'solde_restant' => 0,
        ]);

        $response = $this->actingAs($this->userAgent)
            ->post('/agent/conges', [
                'id_type_conge' => $this->typeConge->id_type_conge,
                'date_debut'    => now()->addDays(5)->format('Y-m-d'),
                'date_fin'      => now()->addDays(10)->format('Y-m-d'),
                'motif'         => 'Test',
            ]);

        // Doit rediriger avec erreur (pas de nouveau enregistrement)
        $this->assertDatabaseMissing('demandes', [
            'id_agent'     => $this->agentModel->id_agent,
            'type_demande' => 'Conge',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_agent_peut_voir_la_liste_de_ses_conges(): void
    {
        $response = $this->actingAs($this->userAgent)
            ->get('/agent/conges');

        $response->assertStatus(200);
    }

    // ──────────────────────────────────────────────────────
    // ÉTAPE 2 : Manager valide la demande
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_manager_voit_les_conges_en_attente_de_son_service(): void
    {
        $response = $this->actingAs($this->userManager)
            ->get('/manager/conges/pending');

        $response->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_manager_peut_valider_un_conge_de_son_service(): void
    {
        $demande = $this->creerDemande($this->agentModel);
        $conge   = Conge::create([
            'id_demande'    => $demande->id_demande,
            'id_type_conge' => $this->typeConge->id_type_conge,
            'date_debut'    => now()->addDays(5)->format('Y-m-d'),
            'date_fin'      => now()->addDays(10)->format('Y-m-d'),
            'nbres_jours'   => 5,
        ]);

        $response = $this->actingAs($this->userManager)
            ->post("/manager/conges/{$demande->id_demande}/valider");

        $response->assertRedirect();
        $this->assertDatabaseHas('demandes', [
            'id_demande'     => $demande->id_demande,
            'statut_demande' => 'Validé',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_manager_peut_rejeter_un_conge(): void
    {
        $demande = $this->creerDemande($this->agentModel);
        Conge::create([
            'id_demande'    => $demande->id_demande,
            'id_type_conge' => $this->typeConge->id_type_conge,
            'date_debut'    => now()->addDays(5)->format('Y-m-d'),
            'date_fin'      => now()->addDays(10)->format('Y-m-d'),
            'nbres_jours'   => 5,
        ]);

        $response = $this->actingAs($this->userManager)
            ->post("/manager/conges/{$demande->id_demande}/rejeter", [
                'motif_refus' => 'Besoin de service impératif',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('demandes', [
            'id_demande'     => $demande->id_demande,
            'statut_demande' => 'Rejeté',
        ]);
    }

    // ──────────────────────────────────────────────────────
    // ÉTAPE 3 : AgentRH approuve définitivement
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_rh_voit_les_conges_validates_par_manager(): void
    {
        $response = $this->actingAs($this->userRH)
            ->get('/rh/conges/pending');

        $response->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_rh_peut_approuver_un_conge_valide_par_le_manager(): void
    {
        $demande = $this->creerDemande($this->agentModel, 'Validé');
        Conge::create([
            'id_demande'    => $demande->id_demande,
            'id_type_conge' => $this->typeConge->id_type_conge,
            'date_debut'    => now()->addDays(5)->format('Y-m-d'),
            'date_fin'      => now()->addDays(10)->format('Y-m-d'),
            'nbres_jours'   => 5,
        ]);

        $response = $this->actingAs($this->userRH)
            ->post("/rh/conges/{$demande->id_demande}/approuver");

        $response->assertRedirect();
        $this->assertDatabaseHas('demandes', [
            'id_demande'     => $demande->id_demande,
            'statut_demande' => 'Approuvé',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_rh_peut_effectuer_une_saisie_physique_de_conge(): void
    {
        $response = $this->actingAs($this->userRH)
            ->get('/rh/conge-physique');

        $response->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_rh_peut_consulter_les_soldes_de_conges(): void
    {
        $response = $this->actingAs($this->userRH)
            ->get('/rh/conges/soldes');

        $response->assertStatus(200);
    }
}
