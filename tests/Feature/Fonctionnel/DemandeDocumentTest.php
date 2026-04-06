<?php

namespace Tests\Feature\Fonctionnel;

use App\Models\DemandeDocument;
use Tests\SirhTestCase;

/**
 * Tests fonctionnels — Module Documents Administratifs (Section 4.2.1.1)
 *
 * Vérifie le workflow :
 *   Agent (self-service) → AgentRH (traitement/génération) → DRH (signature)
 */
class DemandeDocumentTest extends SirhTestCase
{
    private $service;
    private $userAgent;
    private $agentModel;
    private $userRH;
    private $userDRH;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service    = $this->creerService();
        $this->userAgent  = $this->creerAgent();
        $this->agentModel = $this->creerDossierAgent($this->userAgent, $this->service);
        $this->userRH     = $this->creerAgentRH();
        $this->userDRH    = $this->creerDRH();
    }

    // ──────────────────────────────────────────────────────
    // SELF-SERVICE AGENT
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_agent_peut_acceder_au_formulaire_de_demande_document(): void
    {
        $response = $this->actingAs($this->userAgent)
            ->get('/agent/docs/create');

        $response->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_agent_peut_demander_une_attestation_de_travail(): void
    {
        $response = $this->actingAs($this->userAgent)
            ->post('/agent/docs', [
                'type_document' => 'attestation_travail',
                'motif'         => 'Demande de visa',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('demandes_documents', [
            'agent_id'      => $this->agentModel->id_agent,
            'type_document' => 'attestation_travail',
            'statut'        => 'en_attente',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_agent_peut_demander_un_ordre_de_mission(): void
    {
        $response = $this->actingAs($this->userAgent)
            ->post('/agent/docs', [
                'type_document' => 'ordre_mission',
                'motif'         => 'Formation à Saint-Louis',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('demandes_documents', [
            'agent_id'      => $this->agentModel->id_agent,
            'type_document' => 'ordre_mission',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_agent_voit_la_liste_de_ses_demandes(): void
    {
        $response = $this->actingAs($this->userAgent)
            ->get('/agent/docs');

        $response->assertStatus(200);
    }

    // ──────────────────────────────────────────────────────
    // TRAITEMENT RH
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_rh_voit_les_demandes_documents_en_attente(): void
    {
        $response = $this->actingAs($this->userRH)
            ->get('/rh/documents-admin');

        $response->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_rh_peut_traiter_une_demande_de_document(): void
    {
        $demande = DemandeDocument::create([
            'agent_id'      => $this->agentModel->id_agent,
            'type_document' => 'attestation_travail',
            'statut'        => 'en_attente',
            'motif'         => 'Test',
        ]);

        $response = $this->actingAs($this->userRH)
            ->post("/rh/demandes-docs/{$demande->id}/traiter");

        $response->assertRedirect();
        $this->assertDatabaseHas('demandes_documents', [
            'id'     => $demande->id,
            'statut' => 'pret',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_rh_peut_rejeter_une_demande_de_document(): void
    {
        $demande = DemandeDocument::create([
            'agent_id'      => $this->agentModel->id_agent,
            'type_document' => 'ordre_mission',
            'statut'        => 'en_attente',
        ]);

        $response = $this->actingAs($this->userRH)
            ->post("/rh/demandes-docs/{$demande->id}/rejeter", [
                'motif_rejet' => 'Dossier incomplet',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('demandes_documents', [
            'id'          => $demande->id,
            'statut'      => 'rejete',
            'motif_rejet' => 'Dossier incomplet',
        ]);
    }

    // ──────────────────────────────────────────────────────
    // VALIDATION FORMULAIRE
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function une_demande_sans_type_document_est_rejetee(): void
    {
        $response = $this->actingAs($this->userAgent)
            ->post('/agent/docs', [
                'motif' => 'Demande sans type',
                // type_document manquant
            ]);

        $response->assertSessionHasErrors('type_document');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_type_document_invalide_est_rejete(): void
    {
        $response = $this->actingAs($this->userAgent)
            ->post('/agent/docs', [
                'type_document' => 'type_inexistant',
                'motif'         => 'Test',
            ]);

        $response->assertSessionHasErrors('type_document');
    }
}
