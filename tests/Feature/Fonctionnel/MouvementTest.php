<?php

namespace Tests\Feature\Fonctionnel;

use App\Models\Mouvement;
use Tests\SirhTestCase;

/**
 * Tests fonctionnels — Module Mouvements (Section 4.2.1.1)
 *
 * Vérifie le workflow :
 *   AgentRH (création) → DRH (validation) → Effectué
 *
 * Triade CID :
 *  - Intégrité : DB::transaction, Form Requests, contraintes statut
 *  - Confidentialité : RBAC strict (seul RH/DRH accèdent aux mouvements)
 *  - Disponibilité : audit trail, gestion erreurs
 */
class MouvementTest extends SirhTestCase
{
    private $service;
    private $serviceOrigine;
    private $userRH;
    private $userDRH;
    private $userAgent;
    private $userManager;
    private $agentModel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service        = $this->creerService();
        $this->serviceOrigine = $this->creerService();
        $this->userRH         = $this->creerAgentRH();
        $this->userDRH        = $this->creerDRH();
        $this->userAgent      = $this->creerAgent();
        $this->userManager    = $this->creerManager();
        $this->agentModel     = $this->creerDossierAgent($this->userAgent, $this->serviceOrigine);
    }

    // ─────────────────────────────────────────────
    // ACCÈS PAR RÔLE — Confidentialité CID
    // ─────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_rh_accede_a_la_liste_mouvements(): void
    {
        $this->actingAs($this->userRH)
            ->get('/rh/mouvements')
            ->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_drh_accede_aux_validations_mouvements(): void
    {
        $this->actingAs($this->userDRH)
            ->get('/drh/validations/mouvements')
            ->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_agent_ne_peut_pas_acceder_aux_mouvements_rh(): void
    {
        $this->actingAs($this->userAgent)
            ->get('/rh/mouvements')
            ->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_manager_ne_peut_pas_creer_un_mouvement(): void
    {
        $this->actingAs($this->userManager)
            ->post('/rh/mouvements', [
                'id_agent'        => $this->agentModel->id_agent,
                'type_mouvement'  => 'Mutation',
                'id_service'      => $this->service->id_service,
                'date_mouvement'  => now()->format('Y-m-d'),
                'motif'           => 'Test',
            ])
            ->assertStatus(403);
    }

    // ─────────────────────────────────────────────
    // CRÉATION MOUVEMENT — Intégrité CID
    // ─────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_rh_peut_creer_un_mouvement_mutation(): void
    {
        $response = $this->actingAs($this->userRH)
            ->post('/rh/mouvements', [
                'id_agent'           => $this->agentModel->id_agent,
                'type_mouvement'     => 'Mutation',
                'id_service'         => $this->service->id_service,
                'id_service_origine' => $this->serviceOrigine->id_service,
                'date_mouvement'     => now()->addDays(7)->format('Y-m-d'),
                'motif'              => 'Besoin en personnel au service ' . $this->service->nom_service,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('mouvements', [
            'id_agent'       => $this->agentModel->id_agent,
            'type_mouvement' => 'Mutation',
            'statut'         => 'en_attente',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_mouvement_sans_date_est_rejete(): void
    {
        $response = $this->actingAs($this->userRH)
            ->post('/rh/mouvements', [
                'id_agent'       => $this->agentModel->id_agent,
                'type_mouvement' => 'Mutation',
                'id_service'     => $this->service->id_service,
                'motif'          => 'Test sans date',
                // date_mouvement manquant
            ]);

        $response->assertSessionHasErrors('date_mouvement');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_mouvement_sans_agent_est_rejete(): void
    {
        $response = $this->actingAs($this->userRH)
            ->post('/rh/mouvements', [
                'type_mouvement' => 'Mutation',
                'id_service'     => $this->service->id_service,
                'date_mouvement' => now()->addDays(7)->format('Y-m-d'),
                'motif'          => 'Test',
                // id_agent manquant
            ]);

        $response->assertSessionHasErrors('id_agent');
    }

    // ─────────────────────────────────────────────
    // WORKFLOW VALIDATION DRH
    // ─────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_drh_peut_valider_un_mouvement_en_attente(): void
    {
        $mouvement = Mouvement::create([
            'id_agent'       => $this->agentModel->id_agent,
            'type_mouvement' => 'Mutation',
            'id_service'     => $this->service->id_service,
            'date_mouvement' => now()->addDays(7),
            'motif'          => 'Test validation DRH',
            'statut'         => 'en_attente',
            'cree_par'       => $this->userRH->id,
        ]);

        $this->actingAs($this->userDRH)
            ->post("/drh/validations/mouvements/{$mouvement->id_mouvement}/valider")
            ->assertRedirect();

        $this->assertDatabaseHas('mouvements', [
            'id_mouvement' => $mouvement->id_mouvement,
            'statut'       => 'valide_drh',
            'valide_par'   => $this->userDRH->id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_drh_peut_rejeter_un_mouvement(): void
    {
        $mouvement = Mouvement::create([
            'id_agent'       => $this->agentModel->id_agent,
            'type_mouvement' => 'Mutation',
            'id_service'     => $this->service->id_service,
            'date_mouvement' => now()->addDays(7),
            'motif'          => 'Test rejet DRH',
            'statut'         => 'en_attente',
            'cree_par'       => $this->userRH->id,
        ]);

        $this->actingAs($this->userDRH)
            ->post("/drh/validations/mouvements/{$mouvement->id_mouvement}/rejeter", [
                'motif_rejet' => 'Pas de poste disponible dans ce service actuellement',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('mouvements', [
            'id_mouvement' => $mouvement->id_mouvement,
            'statut'       => 'annule',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_rh_peut_effectuer_un_mouvement_valide_drh(): void
    {
        $mouvement = Mouvement::create([
            'id_agent'       => $this->agentModel->id_agent,
            'type_mouvement' => 'Mutation',
            'id_service'     => $this->service->id_service,
            'date_mouvement' => now()->subDay(),
            'motif'          => 'Test effectuer',
            'statut'         => 'valide_drh',
            'cree_par'       => $this->userRH->id,
            'valide_par'     => $this->userDRH->id,
        ]);

        $this->actingAs($this->userRH)
            ->post("/rh/mouvements/{$mouvement->id_mouvement}/effectuer")
            ->assertRedirect();

        $this->assertDatabaseHas('mouvements', [
            'id_mouvement' => $mouvement->id_mouvement,
            'statut'       => 'effectue',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_agent_rh_ne_peut_pas_valider_un_mouvement_comme_drh(): void
    {
        $mouvement = Mouvement::create([
            'id_agent'       => $this->agentModel->id_agent,
            'type_mouvement' => 'Mutation',
            'id_service'     => $this->service->id_service,
            'date_mouvement' => now()->addDays(7),
            'motif'          => 'Test accès refusé',
            'statut'         => 'en_attente',
            'cree_par'       => $this->userRH->id,
        ]);

        $this->actingAs($this->userRH)
            ->post("/drh/validations/mouvements/{$mouvement->id_mouvement}/valider")
            ->assertStatus(403);
    }
}
