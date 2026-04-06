<?php

namespace Tests\Feature\Securite;

use App\Models\Agent;
use Spatie\Activitylog\Models\Activity;
use Tests\SirhTestCase;

/**
 * Tests de sécurité — Audit Trail / Journalisation (Section 4.2.1.2)
 *
 * Vérifie que toutes les actions critiques sont tracées
 * dans le journal d'audit Spatie Activity Log.
 *
 * Aligné sur la Section 4.1.2.4 du mémoire :
 * "Journalisation et traçabilité des actions critiques"
 * et la Section 2.2.4 : "Traçabilité et journalisation"
 */
class AuditTrailTest extends SirhTestCase
{
    private $service;
    private $userRH;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->creerService();
        $this->userRH  = $this->creerAgentRH();
    }

    // ──────────────────────────────────────────────────────
    // JOURNALISATION DE LA CONNEXION
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function la_connexion_reussie_est_journalisee(): void
    {
        $user = $this->creerAgent();
        $user->update(['login' => 'audit.agent']);

        $countAvant = Activity::count();

        $this->post('/login', [
            'login'    => 'audit.agent',
            'password' => 'Password1!',
        ]);

        $this->assertGreaterThan(
            $countAvant,
            Activity::count(),
            'La connexion doit générer une entrée dans le journal d\'audit'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function la_deconnexion_est_journalisee(): void
    {
        $user = $this->creerAgent();

        $countAvant = Activity::count();

        $this->actingAs($user)->post('/logout');

        $this->assertGreaterThan(
            $countAvant,
            Activity::count(),
            'La déconnexion doit générer une entrée dans le journal d\'audit'
        );
    }

    // ──────────────────────────────────────────────────────
    // JOURNALISATION DES ACTIONS RH CRITIQUES
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function la_modification_d_un_agent_est_journalisee(): void
    {
        $userAgent  = $this->creerAgent();
        $agentModel = $this->creerDossierAgent($userAgent, $this->service);

        $countAvant = Activity::count();

        // Modifier le statut de l'agent
        $agentModel->update(['statut_agent' => 'suspendu']);

        $this->assertGreaterThan(
            $countAvant,
            Activity::count(),
            'La modification d\'un agent doit être journalisée'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function la_creation_d_un_agent_est_journalisee(): void
    {
        $userAgent = $this->creerAgent();

        $countAvant = Activity::count();

        // Créer un agent (via le modèle)
        $this->creerDossierAgent($userAgent, $this->service, ['matricule' => 'CHNP-99999']);

        $this->assertGreaterThan(
            $countAvant,
            Activity::count(),
            'La création d\'un agent doit être journalisée'
        );
    }

    // ──────────────────────────────────────────────────────
    // ACCÈS À L'AUDIT TRAIL
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function seul_ladmin_peut_acceder_aux_logs_daudit(): void
    {
        $admin   = $this->creerAdmin();
        $agentRH = $this->creerAgentRH();

        // Admin peut accéder
        $this->actingAs($admin)
            ->get('/admin/audit')
            ->assertStatus(200);

        // AgentRH ne peut pas accéder aux logs admin
        $this->actingAs($agentRH)
            ->get('/admin/audit')
            ->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function les_logs_de_connexion_sont_accessibles_via_ladmin(): void
    {
        $admin = $this->creerAdmin();

        $response = $this->actingAs($admin)
            ->get('/admin/audit/connexions');

        $response->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function les_echecs_de_connexion_sont_accessibles_via_ladmin(): void
    {
        $admin = $this->creerAdmin();

        $response = $this->actingAs($admin)
            ->get('/admin/audit/echecs');

        $response->assertStatus(200);
    }

    // ──────────────────────────────────────────────────────
    // INTÉGRITÉ DU JOURNAL (IMMUABILITÉ)
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function les_logs_d_audit_ne_peuvent_pas_etre_modifies_par_un_agent(): void
    {
        // Un agent ne peut ni voir ni modifier les logs d'audit
        $user = $this->creerAgent();

        $this->actingAs($user)
            ->get('/admin/audit')
            ->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_log_audit_contient_les_informations_essentielles(): void
    {
        $userAgent  = $this->creerAgent();
        $agentModel = $this->creerDossierAgent($userAgent, $this->service);

        // Déclencher une action journalisée
        $agentModel->update(['fonction' => 'Médecin Chef']);

        $dernierLog = Activity::latest()->first();

        $this->assertNotNull($dernierLog, 'Un log doit exister');
        $this->assertNotNull($dernierLog->subject_type, 'Le type de sujet doit être renseigné');
        $this->assertNotNull($dernierLog->created_at, 'La date doit être renseignée');
    }
}
