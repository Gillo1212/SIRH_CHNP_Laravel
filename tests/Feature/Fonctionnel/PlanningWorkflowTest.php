<?php

namespace Tests\Feature\Fonctionnel;

use App\Models\Planning;
use App\Models\Service;
use Tests\SirhTestCase;

/**
 * Tests fonctionnels — Module Plannings (Section 4.2.1.1)
 *
 * Vérifie le workflow :
 *   Manager/Major (création) → Transmission RH → Validation/Rejet RH
 *
 * Triade CID :
 *  - Intégrité : transitions de statut contrôlées
 *  - Confidentialité : accès limité par rôle
 *  - Disponibilité : routes accessibles selon rôle
 */
class PlanningWorkflowTest extends SirhTestCase
{
    private Service $service;
    private $userManager;
    private $userMajor;
    private $userRH;
    private $userAgent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userManager = $this->creerManager();
        $this->userMajor   = $this->creerMajor();
        $this->userRH      = $this->creerAgentRH();
        $this->userAgent   = $this->creerAgent();

        // Le service doit avoir le manager ET le major assignés (requis par les middlewares)
        $this->service = $this->creerService([
            'id_agent_manager' => $this->userManager->id,
            'id_agent_major'   => $this->userMajor->id,
        ]);
    }

    // ─────────────────────────────────────────────
    // ACCÈS PAR RÔLE — Confidentialité CID
    // ─────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_manager_accede_a_la_liste_plannings(): void
    {
        $this->actingAs($this->userManager)
            ->get('/manager/planning')
            ->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_major_accede_a_la_liste_plannings(): void
    {
        $this->actingAs($this->userMajor)
            ->get('/major/planning')
            ->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_rh_voit_les_plannings_en_attente(): void
    {
        $this->actingAs($this->userRH)
            ->get('/rh/plannings/pending')
            ->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_agent_ne_peut_pas_acceder_aux_plannings_manager(): void
    {
        $this->actingAs($this->userAgent)
            ->get('/manager/planning')
            ->assertStatus(403);
    }

    // ─────────────────────────────────────────────
    // CRÉATION PLANNING — Intégrité CID
    // ─────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_manager_peut_creer_un_planning(): void
    {
        $response = $this->actingAs($this->userManager)
            ->post('/manager/planning', [
                'id_service'    => $this->service->id_service,
                'periode_debut' => now()->startOfMonth()->format('Y-m-d'),
                'periode_fin'   => now()->endOfMonth()->format('Y-m-d'),
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('plannings', [
            'id_service'      => $this->service->id_service,
            'statut_planning' => 'Brouillon',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_major_peut_acceder_a_son_index_plannings(): void
    {
        // Vérifie que le major a bien accès à son module planning
        $this->actingAs($this->userMajor)
            ->get('/major/planning')
            ->assertStatus(200);
    }

    // ─────────────────────────────────────────────
    // WORKFLOW TRANSMISSION → VALIDATION
    // ─────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_manager_peut_transmettre_un_planning_avec_lignes(): void
    {
        $planning = Planning::create([
            'id_service'      => $this->service->id_service,
            'periode_debut'   => now()->startOfMonth(),
            'periode_fin'     => now()->endOfMonth(),
            'statut_planning' => 'Brouillon',
            'date_creation'   => now(),
        ]);

        // Ajouter une ligne (requis par la logique métier avant transmission)
        $typePoste = \App\Models\TypePoste::create(['libelle' => 'Garde test', 'couleur' => '#000']);
        $agentTest = $this->creerDossierAgent($this->creerAgent(), $this->service);
        \App\Models\LignePlanning::create([
            'id_planning'  => $planning->id_planning,
            'id_agent'     => $agentTest->id_agent,
            'id_typeposte' => $typePoste->id_typeposte,
            'date_poste'   => now()->addDay(),
            'heure_debut'  => '08:00',
            'heure_fin'    => '16:00',
        ]);

        $this->actingAs($this->userManager)
            ->post("/manager/planning/{$planning->id_planning}/transmettre")
            ->assertRedirect();

        $this->assertDatabaseHas('plannings', [
            'id_planning'     => $planning->id_planning,
            'statut_planning' => 'Transmis',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_rh_peut_valider_un_planning_transmis(): void
    {
        $planning = Planning::create([
            'id_service'      => $this->service->id_service,
            'periode_debut'   => now()->startOfMonth(),
            'periode_fin'     => now()->endOfMonth(),
            'statut_planning' => 'Transmis',
            'date_creation'   => now(),
        ]);

        $this->actingAs($this->userRH)
            ->post("/rh/plannings/{$planning->id_planning}/valider")
            ->assertRedirect();

        $this->assertDatabaseHas('plannings', [
            'id_planning'     => $planning->id_planning,
            'statut_planning' => 'Validé',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_rh_peut_rejeter_un_planning_transmis(): void
    {
        $planning = Planning::create([
            'id_service'      => $this->service->id_service,
            'periode_debut'   => now()->startOfMonth(),
            'periode_fin'     => now()->endOfMonth(),
            'statut_planning' => 'Transmis',
            'date_creation'   => now(),
        ]);

        $this->actingAs($this->userRH)
            ->post("/rh/plannings/{$planning->id_planning}/rejeter", [
                'motif_rejet' => 'Planning incomplet, manque les gardes de nuit',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('plannings', [
            'id_planning'     => $planning->id_planning,
            'statut_planning' => 'Rejeté',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_agent_ne_peut_pas_valider_un_planning(): void
    {
        $planning = Planning::create([
            'id_service'      => $this->service->id_service,
            'periode_debut'   => now()->startOfMonth(),
            'periode_fin'     => now()->endOfMonth(),
            'statut_planning' => 'Transmis',
            'date_creation'   => now(),
        ]);

        $this->actingAs($this->userAgent)
            ->post("/rh/plannings/{$planning->id_planning}/valider")
            ->assertStatus(403);
    }

    // ─────────────────────────────────────────────
    // INTÉGRITÉ : VALIDATION FORMULAIRE
    // ─────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_planning_sans_date_est_rejete(): void
    {
        $response = $this->actingAs($this->userManager)
            ->post('/manager/planning', [
                'id_service'  => $this->service->id_service,
                'periode_fin' => now()->endOfMonth()->format('Y-m-d'),
                // periode_debut manquant
            ]);

        $response->assertSessionHasErrors('periode_debut');
    }
}
