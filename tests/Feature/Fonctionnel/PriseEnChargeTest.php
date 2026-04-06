<?php

namespace Tests\Feature\Fonctionnel;

use App\Models\Demande;
use App\Models\PriseEnCharge;
use Tests\SirhTestCase;

/**
 * Tests fonctionnels — Module Prises en Charge Médicales (Section 4.2.1.1)
 *
 * Vérifie le workflow :
 *   Agent (demande) → AgentRH (validation standard)
 *                   → DRH (validation exceptionnelle)
 */
class PriseEnChargeTest extends SirhTestCase
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
    // DEMANDE AGENT (SELF-SERVICE)
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_agent_peut_acceder_au_formulaire_de_pec(): void
    {
        $response = $this->actingAs($this->userAgent)
            ->get('/agent/pec/create');

        $response->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_agent_peut_soumettre_une_demande_de_pec(): void
    {
        $response = $this->actingAs($this->userAgent)
            ->post('/agent/pec', [
                'raison_medical' => 'Consultation spécialiste cardiologie',
                'ayant_droit'    => 'Agent',
                'type_prise'     => 'Hospitalisation',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('demandes', [
            'id_agent'     => $this->agentModel->id_agent,
            'type_demande' => 'PriseEnCharge',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_agent_voit_la_liste_de_ses_pec(): void
    {
        $response = $this->actingAs($this->userAgent)
            ->get('/agent/pec');

        $response->assertStatus(200);
    }

    // ──────────────────────────────────────────────────────
    // VALIDATION RH (STANDARD)
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_rh_voit_la_liste_des_pec_en_attente(): void
    {
        $response = $this->actingAs($this->userRH)
            ->get('/rh/pec');

        $response->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_rh_peut_valider_une_pec_standard(): void
    {
        $demande = Demande::create([
            'id_agent'       => $this->agentModel->id_agent,
            'type_demande'   => 'PriseEnCharge',
            'statut_demande' => 'En_attente',
        ]);

        $pec = PriseEnCharge::create([
            'id_demande'    => $demande->id_demande,
            'raison_medical'=> 'Soins dentaires',
            'ayant_droit'   => 'Agent',
            'type_prise'    => 'Consultation',
            'exceptionnelle'=> false,
        ]);

        $response = $this->actingAs($this->userRH)
            ->patch("/rh/pec/{$pec->id_priseenche}", ['action' => 'valider']);

        $response->assertRedirect();
        $this->assertDatabaseHas('demandes', [
            'id_demande'     => $demande->id_demande,
            'statut_demande' => 'Validé',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_rh_peut_rejeter_une_pec(): void
    {
        $demande = Demande::create([
            'id_agent'       => $this->agentModel->id_agent,
            'type_demande'   => 'PriseEnCharge',
            'statut_demande' => 'En_attente',
        ]);

        $pec = PriseEnCharge::create([
            'id_demande'    => $demande->id_demande,
            'raison_medical'=> 'Soins non couverts',
            'ayant_droit'   => 'Agent',
            'type_prise'    => 'Consultation',
            'exceptionnelle'=> false,
        ]);

        $response = $this->actingAs($this->userRH)
            ->patch("/rh/pec/{$pec->id_priseenche}", [
                'action'      => 'rejeter',
                'motif_rejet' => 'Prestation non prise en charge par l\'établissement',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('demandes', [
            'id_demande'     => $demande->id_demande,
            'statut_demande' => 'Rejeté',
        ]);
    }

    // ──────────────────────────────────────────────────────
    // VALIDATION DRH (PEC EXCEPTIONNELLE)
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_drh_voit_les_pec_exceptionnelles(): void
    {
        $response = $this->actingAs($this->userDRH)
            ->get('/drh/validations/pec-exceptionnelles');

        $response->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function une_demande_de_pec_sans_raison_medicale_est_rejetee(): void
    {
        $response = $this->actingAs($this->userAgent)
            ->post('/agent/pec', [
                'ayant_droit' => 'Agent',
                'type_prise'  => 'Consultation',
                // raison_medical manquante intentionnellement
            ]);

        $response->assertSessionHasErrors('raison_medical');
    }
}
