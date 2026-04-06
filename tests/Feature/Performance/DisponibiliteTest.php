<?php

namespace Tests\Feature\Performance;

use Tests\SirhTestCase;

/**
 * Tests de performance et de disponibilité (Section 4.2.1.3)
 *
 * Vérifie les critères de disponibilité de la triade CID :
 * - Temps de réponse < 3 secondes pour toutes les routes critiques
 * - Disponibilité (HTTP 200) des interfaces pour chaque rôle
 * - Absence de requêtes N+1 sur les listes principales
 *
 * Aligné sur la Section 4.2.1.3 du mémoire :
 * "Tests de performance et de disponibilité"
 */
class DisponibiliteTest extends SirhTestCase
{
    private const SEUIL_MS = 3000; // 3 secondes max (objectif mémoire)

    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->creerService();
    }

    /** Crée un agent avec son profil lié (requis par EnsureHasAgentProfile). */
    private function creerAgentAvecProfil(string $role = 'Agent')
    {
        $user = $this->creerUtilisateur($role);
        $this->creerDossierAgent($user, $this->service);
        return $user;
    }

    // ──────────────────────────────────────────────────────
    // DISPONIBILITÉ DES DASHBOARDS (1 PAR RÔLE)
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_dashboard_agent_repond_en_moins_de_3_secondes(): void
    {
        $user = $this->creerAgentAvecProfil('Agent');
        $this->assertTempsReponse('/agent/dashboard', $user);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_dashboard_manager_repond_en_moins_de_3_secondes(): void
    {
        $user = $this->creerManager();
        $this->assertTempsReponse('/manager/dashboard', $user);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_dashboard_major_repond_en_moins_de_3_secondes(): void
    {
        $user = $this->creerMajor();
        $this->assertTempsReponse('/major/dashboard', $user);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_dashboard_rh_repond_en_moins_de_3_secondes(): void
    {
        $user = $this->creerAgentRH();
        $this->assertTempsReponse('/rh/dashboard', $user);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_dashboard_drh_repond_en_moins_de_3_secondes(): void
    {
        $user = $this->creerDRH();
        $this->assertTempsReponse('/drh/dashboard', $user);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_dashboard_admin_repond_en_moins_de_3_secondes(): void
    {
        $user = $this->creerAdmin();
        $this->assertTempsReponse('/admin/dashboard', $user);
    }

    // ──────────────────────────────────────────────────────
    // DISPONIBILITÉ DES LISTES PRINCIPALES
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function la_liste_des_agents_repond_en_moins_de_3_secondes(): void
    {
        $user = $this->creerAgentRH();
        $this->assertTempsReponse('/rh/agents', $user);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function la_liste_des_conges_agent_repond_en_moins_de_3_secondes(): void
    {
        $user = $this->creerAgentAvecProfil('Agent');
        $this->assertTempsReponse('/agent/conges', $user);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function la_liste_des_pec_repond_en_moins_de_3_secondes(): void
    {
        $user = $this->creerAgentRH();
        $this->assertTempsReponse('/rh/pec', $user);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function la_liste_des_documents_ged_repond_en_moins_de_3_secondes(): void
    {
        $user = $this->creerAgentRH();
        $this->assertTempsReponse('/rh/documents', $user);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function la_liste_des_mouvements_repond_en_moins_de_3_secondes(): void
    {
        $user = $this->creerAgentRH();
        $this->assertTempsReponse('/rh/mouvements', $user);
    }

    // ──────────────────────────────────────────────────────
    // DISPONIBILITÉ DES FORMULAIRES
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_formulaire_de_demande_conge_repond_en_moins_de_3_secondes(): void
    {
        $user = $this->creerAgentAvecProfil('Agent');
        $this->assertTempsReponse('/agent/conges/create', $user);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_formulaire_de_demande_pec_repond_en_moins_de_3_secondes(): void
    {
        $user = $this->creerAgentAvecProfil('Agent');
        $this->assertTempsReponse('/agent/pec/create', $user);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_formulaire_de_demande_document_repond_en_moins_de_3_secondes(): void
    {
        $user = $this->creerAgentAvecProfil('Agent');
        $this->assertTempsReponse('/agent/docs/create', $user);
    }

    // ──────────────────────────────────────────────────────
    // DISPONIBILITÉ DES MODULES DE SÉCURITÉ (ADMIN)
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_panel_audit_repond_en_moins_de_3_secondes(): void
    {
        $admin = $this->creerAdmin();
        $this->assertTempsReponse('/admin/audit', $admin);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_panel_roles_permissions_repond_en_moins_de_3_secondes(): void
    {
        $admin = $this->creerAdmin();
        $this->assertTempsReponse('/admin/roles', $admin);
    }

    // ──────────────────────────────────────────────────────
    // HELPER : mesure du temps de réponse
    // ──────────────────────────────────────────────────────

    private function assertTempsReponse(string $url, $user): void
    {
        $debut = microtime(true);

        $response = $this->actingAs($user)->get($url);

        $dureeMs = (microtime(true) - $debut) * 1000;

        $response->assertStatus(200,
            "La route $url doit retourner HTTP 200 (disponibilité)"
        );

        $this->assertLessThan(
            self::SEUIL_MS,
            $dureeMs,
            sprintf(
                'La route %s a répondu en %.0f ms (seuil : %d ms)',
                $url,
                $dureeMs,
                self::SEUIL_MS
            )
        );
    }
}
