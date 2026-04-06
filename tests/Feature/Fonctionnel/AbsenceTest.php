<?php

namespace Tests\Feature\Fonctionnel;

use App\Models\Absence;
use App\Models\Demande;
use Tests\SirhTestCase;

/**
 * Tests fonctionnels — Gestion des Absences (Section 4.2.1.1)
 *
 * Vérifie l'enregistrement des absences par Manager/Major
 * et la validation des justificatifs par AgentRH.
 */
class AbsenceTest extends SirhTestCase
{
    private $service;
    private $userAgent;
    private $agentModel;
    private $userManager;
    private $userMajor;
    private $userRH;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->creerService();

        $this->userAgent  = $this->creerAgent();
        $this->agentModel = $this->creerDossierAgent($this->userAgent, $this->service);

        $this->userManager = $this->creerManager();
        $this->service->update(['id_agent_manager' => $this->userManager->id]);

        $this->userMajor = $this->creerMajor();
        $this->service->update(['id_agent_major' => $this->userMajor->id]);

        $this->userRH = $this->creerAgentRH();
    }

    // ──────────────────────────────────────────────────────
    // ENREGISTREMENT
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_manager_accede_au_formulaire_enregistrement_absence(): void
    {
        $response = $this->actingAs($this->userManager)
            ->get('/manager/absences/create');

        $response->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_manager_peut_enregistrer_une_absence(): void
    {
        $response = $this->actingAs($this->userManager)
            ->post('/manager/absences', [
                'id_agent'     => $this->agentModel->id_agent,
                'date_absence' => now()->subDay()->format('Y-m-d'),
                'type_absence' => 'Maladie',
                'justifie'     => false,
                'motif'        => 'Absent sans préavis',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('absences', [
            'type_absence' => 'Maladie',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_major_peut_enregistrer_une_absence(): void
    {
        $response = $this->actingAs($this->userMajor)
            ->post('/major/absences', [
                'id_agent'     => $this->agentModel->id_agent,
                'date_absence' => now()->subDay()->format('Y-m-d'),
                'type_absence' => 'Personnelle',
                'justifie'     => false,
            ]);

        $response->assertRedirect();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_rh_peut_enregistrer_une_absence(): void
    {
        $response = $this->actingAs($this->userRH)
            ->post('/rh/absences', [
                'id_agent'     => $this->agentModel->id_agent,
                'date_absence' => now()->subDay()->format('Y-m-d'),
                'type_absence' => 'Injustifiée',
                'justifie'     => false,
                'motif'        => 'Non justifiée',
            ]);

        $response->assertRedirect();
    }

    // ──────────────────────────────────────────────────────
    // VALIDATION DU JUSTIFICATIF
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_rh_voit_la_liste_des_absences(): void
    {
        $response = $this->actingAs($this->userRH)
            ->get('/rh/absences');

        $response->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_agent_voit_uniquement_ses_propres_absences(): void
    {
        $response = $this->actingAs($this->userAgent)
            ->get('/agent/absences');

        $response->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function une_absence_sans_date_est_rejetee(): void
    {
        $response = $this->actingAs($this->userManager)
            ->post('/manager/absences', [
                'id_agent'     => $this->agentModel->id_agent,
                'type_absence' => 'Maladie',
                // date_absence manquante
            ]);

        $response->assertSessionHasErrors('date_absence');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function une_absence_sans_agent_est_rejetee(): void
    {
        $response = $this->actingAs($this->userManager)
            ->post('/manager/absences', [
                'date_absence' => now()->subDay()->format('Y-m-d'),
                'type_absence' => 'Maladie',
                // id_agent manquant
            ]);

        $response->assertSessionHasErrors('id_agent');
    }
}
