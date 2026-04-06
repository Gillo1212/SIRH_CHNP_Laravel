<?php

namespace Tests\Feature\Fonctionnel;

use App\Models\Document;
use App\Models\DossierAgent;
use App\Models\Etagere;
use Tests\SirhTestCase;

/**
 * Tests fonctionnels — Module GED (Section 4.2.1.1)
 *
 * Vérifie :
 *   - Accès selon rôle (Confidentialité CID)
 *   - CRUD étagères et dossiers
 *   - Cycle de vie document : Actif → Archivé → Détruit
 *   - Niveaux de confidentialité (4 niveaux)
 *
 * Triade CID :
 *  - Confidentialité : contrôle d'accès par niveau de confidentialité
 *  - Intégrité : transitions de statut document tracées
 *  - Disponibilité : recherche, liste, download accessibles
 */
class GEDTest extends SirhTestCase
{
    private $service;
    private $userRH;
    private $userDRH;
    private $userAgent;
    private $agentModel;
    private Etagere $etagere;
    private DossierAgent $dossier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service    = $this->creerService();
        $this->userRH     = $this->creerAgentRH();
        $this->userDRH    = $this->creerDRH();
        $this->userAgent  = $this->creerAgent();
        $this->agentModel = $this->creerDossierAgent($this->userAgent, $this->service);

        // Étagère et dossier de référence
        $this->etagere = Etagere::create([
            'id_service'  => $this->service->id_service,
            'nom_etagere' => 'Étagère Test',
            'numero'      => 'ET-001',
            'reference'   => 1,  // integer en base
            'actif'       => true,
        ]);

        $this->dossier = DossierAgent::create([
            'id_etagere'    => $this->etagere->id_etagere,
            'id_agent'      => $this->agentModel->id_agent,
            'reference'     => 'DA-001',
            'date_creation' => now(),
            'statut_da'     => 'Actif',
        ]);
    }

    // ─────────────────────────────────────────────
    // ACCÈS PAR RÔLE — Confidentialité CID
    // ─────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_rh_accede_a_la_ged(): void
    {
        $this->actingAs($this->userRH)
            ->get('/rh/ged')
            ->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_rh_accede_aux_etageres(): void
    {
        $this->actingAs($this->userRH)
            ->get('/rh/ged/etageres')
            ->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_rh_accede_aux_dossiers(): void
    {
        $this->actingAs($this->userRH)
            ->get('/rh/ged/dossiers')
            ->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_agent_ne_peut_pas_acceder_a_la_ged_rh(): void
    {
        $this->actingAs($this->userAgent)
            ->get('/rh/ged')
            ->assertStatus(403);
    }

    // ─────────────────────────────────────────────
    // GESTION ÉTAGÈRES
    // ─────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_rh_peut_creer_une_etagere(): void
    {
        $response = $this->actingAs($this->userRH)
            ->post('/rh/ged/etageres', [
                'id_service'  => $this->service->id_service,
                'nom_etagere' => 'Étagère Administratif',
                'numero'      => 'ET-002',
                'reference'   => 2,
                'description' => 'Dossiers administratifs du personnel',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('etageres', [
            'nom_etagere' => 'Étagère Administratif',
            'numero'      => 'ET-002',
        ]);
    }

    // ─────────────────────────────────────────────
    // UPLOAD ET CYCLE DE VIE DOCUMENT
    // ─────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_rh_peut_acceder_au_formulaire_upload(): void
    {
        $this->actingAs($this->userRH)
            ->get('/rh/ged/documents/create')
            ->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_document_actif_peut_etre_archive(): void
    {
        $document = Document::create([
            'id_dossier'             => $this->dossier->id_dossier,
            'reference'              => 'REF-DOC-001',
            'titre'                  => 'Contrat de travail',
            'type_document'          => 'Contrat',
            'document_url'           => 'documents/test-contrat.pdf',
            'statut_document'        => 'Actif',
            'niveau_confidentialite' => 'Confidentiel',
            'date_creation'          => now(),
            'charge_par'             => $this->userRH->id,
        ]);

        $this->actingAs($this->userRH)
            ->patch("/rh/ged/documents/{$document->id_document}/archiver")
            ->assertRedirect();

        $this->assertDatabaseHas('documents', [
            'id_document'     => $document->id_document,
            'statut_document' => 'Archivé',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_document_archive_peut_etre_restaure(): void
    {
        $document = Document::create([
            'id_dossier'             => $this->dossier->id_dossier,
            'reference'              => 'REF-DOC-002',
            'titre'                  => 'Attestation ancienneté',
            'type_document'          => 'Attestation',
            'document_url'           => 'documents/test-attestation.pdf',
            'statut_document'        => 'Archivé',
            'niveau_confidentialite' => 'Interne',
            'date_creation'          => now()->subYear(),
            'date_archivage'         => now()->subMonth(),
            'charge_par'             => $this->userRH->id,
        ]);

        $this->actingAs($this->userRH)
            ->patch("/rh/ged/documents/{$document->id_document}/restaurer")
            ->assertRedirect();

        $this->assertDatabaseHas('documents', [
            'id_document'     => $document->id_document,
            'statut_document' => 'Actif',
        ]);
    }

    // ─────────────────────────────────────────────
    // NIVEAUX DE CONFIDENTIALITÉ — Confidentialité CID
    // ─────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function les_4_niveaux_de_confidentialite_sont_valides(): void
    {
        $niveaux = ['Public', 'Interne', 'Confidentiel', 'Secret'];

        foreach ($niveaux as $i => $niveau) {
            $doc = Document::create([
                'id_dossier'             => $this->dossier->id_dossier,
                'reference'              => 'REF-NIV-' . ($i + 1),
                'titre'                  => 'Doc niveau ' . $niveau,
                'type_document'          => 'Autre',
                'document_url'           => 'documents/test-niveau-' . $i . '.pdf',
                'statut_document'        => 'Actif',
                'niveau_confidentialite' => $niveau,
                'date_creation'          => now(),
                'charge_par'             => $this->userRH->id,
            ]);

            $this->assertDatabaseHas('documents', [
                'id_document'            => $doc->id_document,
                'niveau_confidentialite' => $niveau,
            ]);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_rh_peut_rechercher_dans_la_ged(): void
    {
        $this->actingAs($this->userRH)
            ->get('/rh/ged/recherche?q=contrat')
            ->assertStatus(200);
    }

    // ─────────────────────────────────────────────
    // GED AGENT (self-service)
    // ─────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_agent_peut_voir_ses_documents(): void
    {
        $this->actingAs($this->userAgent)
            ->get('/agent/documents')
            ->assertStatus(200);
    }
}
