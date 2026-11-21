<?php

namespace Tests\Feature\Fonctionnel;

use App\Models\Demande;
use App\Models\PriseEnCharge;
use Tests\SirhTestCase;

/**
 * Tests fonctionnels-Module Prises en Charge Médicales (Section 4.2.1.1)
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
        $this->creerPec('Validé', true);

        $response = $this->actingAs($this->userDRH)
            ->get('/drh/validations/pec-exceptionnelles');

        $response->assertStatus(200)
            ->assertSee('Intervention chirurgicale')
            ->assertSee('Approuver');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_rh_peut_valider_une_pec_comme_exceptionnelle(): void
    {
        $pec = $this->creerPec('En_attente', false);

        $this->actingAs($this->userRH)
            ->patch("/rh/pec/{$pec->id_priseenche}", ['action' => 'valider', 'exceptionnelle' => '1'])
            ->assertRedirect();

        $this->assertDatabaseHas('prises_en_charge', ['id_priseenche' => $pec->id_priseenche, 'exceptionnelle' => true]);
        $this->assertDatabaseHas('demandes', ['id_demande' => $pec->id_demande, 'statut_demande' => 'Validé']);
        $this->assertTrue($pec->fresh()->en_attente_drh);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_drh_peut_approuver_une_pec_exceptionnelle(): void
    {
        $pec = $this->creerPec('Validé', true);

        $this->actingAs($this->userDRH)
            ->post("/drh/validations/pec/{$pec->id_priseenche}/decider", ['action' => 'approuver'])
            ->assertRedirect();

        $this->assertDatabaseHas('demandes', ['id_demande' => $pec->id_demande, 'statut_demande' => 'Approuvé']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_drh_peut_rejeter_une_pec_exceptionnelle_avec_motif(): void
    {
        $pec = $this->creerPec('Validé', true);

        $this->actingAs($this->userDRH)
            ->post("/drh/validations/pec/{$pec->id_priseenche}/decider", [
                'action'      => 'rejeter',
                'motif_rejet' => 'Montant hors plafond de prise en charge',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('demandes', [
            'id_demande'     => $pec->id_demande,
            'statut_demande' => 'Rejeté',
            'motif_refus'    => 'Montant hors plafond de prise en charge',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_rejet_drh_sans_motif_est_refuse(): void
    {
        $pec = $this->creerPec('Validé', true);

        $this->actingAs($this->userDRH)
            ->post("/drh/validations/pec/{$pec->id_priseenche}/decider", ['action' => 'rejeter'])
            ->assertSessionHasErrors('motif_rejet');

        $this->assertDatabaseHas('demandes', ['id_demande' => $pec->id_demande, 'statut_demande' => 'Validé']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_drh_ne_peut_pas_decider_une_pec_standard(): void
    {
        $pec = $this->creerPec('Validé', false);

        $this->actingAs($this->userDRH)
            ->post("/drh/validations/pec/{$pec->id_priseenche}/decider", ['action' => 'approuver'])
            ->assertStatus(404);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_agent_rh_ne_peut_pas_approuver_a_la_place_du_drh(): void
    {
        $pec = $this->creerPec('Validé', true);

        $this->actingAs($this->userRH)
            ->post("/drh/validations/pec/{$pec->id_priseenche}/decider", ['action' => 'approuver'])
            ->assertStatus(403);

        $this->assertDatabaseHas('demandes', ['id_demande' => $pec->id_demande, 'statut_demande' => 'Validé']);
    }

    // ──────────────────────────────────────────────────────
    // ATTESTATION (fin de workflow)
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function l_attestation_suit_le_workflow_standard_ou_exceptionnel(): void
    {
        $this->assertTrue($this->creerPec('Validé', false)->attestation_disponible, 'Standard validée RH');
        $this->assertFalse($this->creerPec('Validé', true)->attestation_disponible, 'Exceptionnelle en attente DRH');
        $this->assertTrue($this->creerPec('Approuvé', true)->attestation_disponible, 'Exceptionnelle approuvée DRH');
        $this->assertFalse($this->creerPec('En_attente', false)->attestation_disponible, 'En attente');
        $this->assertFalse($this->creerPec('Rejeté', false)->attestation_disponible, 'Rejetée');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function l_agent_peut_telecharger_l_attestation_d_une_pec_standard_validee(): void
    {
        $pec = $this->creerPec('Validé', false);

        $this->actingAs($this->userAgent)
            ->get("/agent/pec/{$pec->id_priseenche}/telecharger")
            ->assertRedirect();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function l_agent_ne_peut_pas_telecharger_avant_l_approbation_drh(): void
    {
        $pec = $this->creerPec('Validé', true);

        $this->actingAs($this->userAgent)
            ->get("/agent/pec/{$pec->id_priseenche}/telecharger")
            ->assertStatus(404);
    }

    private function creerPec(string $statut, bool $exceptionnelle): PriseEnCharge
    {
        $demande = Demande::create([
            'id_agent'       => $this->agentModel->id_agent,
            'type_demande'   => 'PriseEnCharge',
            'statut_demande' => $statut,
        ]);

        return PriseEnCharge::create([
            'id_demande'     => $demande->id_demande,
            'raison_medical' => 'Intervention chirurgicale',
            'ayant_droit'    => 'Agent',
            'type_prise'     => 'Hospitalisation',
            'exceptionnelle' => $exceptionnelle,
        ]);
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
