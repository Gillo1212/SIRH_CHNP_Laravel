<?php

namespace Tests\Feature\Securite;

use Tests\SirhTestCase;

/**
 * Tests de sécurité — Contrôle d'accès RBAC (Section 4.2.1.2)
 *
 * Vérifie que le RBAC est correctement appliqué :
 * - Chaque rôle accède uniquement à ses routes autorisées
 * - Un rôle insuffisant reçoit un 403 Forbidden
 * - Les routes protégées redirigent les invités vers /login
 *
 * Aligné sur la Section 4.1.2.1 du mémoire :
 * "Implémentation du RBAC via Spatie/laravel-permission et Laravel Policies"
 */
class AccesControleTest extends SirhTestCase
{
    // ──────────────────────────────────────────────────────
    // PROTECTION CONTRE LES INVITÉS (NON AUTHENTIFIÉS)
    // ──────────────────────────────────────────────────────

    /**
     * @test
     * @dataProvider routesProtegees
     */
    public function un_invité_est_redirige_vers_login(string $route): void
    {
        $response = $this->get($route);
        $response->assertRedirect('/login');
    }

    public static function routesProtegees(): array
    {
        return [
            'dashboard agent'  => ['/agent/dashboard'],
            'dashboard manager'=> ['/manager/dashboard'],
            'dashboard RH'     => ['/rh/dashboard'],
            'dashboard DRH'    => ['/drh/dashboard'],
            'dashboard admin'  => ['/admin/dashboard'],
            'agents RH'        => ['/rh/agents'],
            'conges agent'     => ['/agent/conges'],
        ];
    }

    // ──────────────────────────────────────────────────────
    // ISOLATION DES ROUTES ADMIN
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_agent_ne_peut_pas_acceder_au_dashboard_admin(): void
    {
        $user = $this->creerAgent();
        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_manager_ne_peut_pas_acceder_au_dashboard_admin(): void
    {
        $user = $this->creerManager();
        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_agent_rh_ne_peut_pas_acceder_au_dashboard_admin(): void
    {
        $user = $this->creerAgentRH();
        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function seul_admin_peut_acceder_au_panel_admin(): void
    {
        $admin = $this->creerAdmin();
        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    // ──────────────────────────────────────────────────────
    // ISOLATION DES ROUTES DRH
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_agent_rh_ne_peut_pas_acceder_aux_routes_drh(): void
    {
        $user = $this->creerAgentRH();
        $response = $this->actingAs($user)->get('/drh/dashboard');
        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_manager_ne_peut_pas_acceder_aux_routes_drh(): void
    {
        $user = $this->creerManager();
        $response = $this->actingAs($user)->get('/drh/kpis');
        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function seul_le_drh_peut_acceder_aux_indicateurs_drh(): void
    {
        $drh = $this->creerDRH();
        $response = $this->actingAs($drh)->get('/drh/dashboard');
        $response->assertStatus(200);
    }

    // ──────────────────────────────────────────────────────
    // ISOLATION DES ROUTES RH
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_agent_ne_peut_pas_acceder_aux_routes_rh(): void
    {
        $user = $this->creerAgent();
        $response = $this->actingAs($user)->get('/rh/agents');
        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_manager_ne_peut_pas_acceder_aux_routes_rh(): void
    {
        $user = $this->creerManager();
        $response = $this->actingAs($user)->get('/rh/agents');
        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_major_ne_peut_pas_acceder_aux_routes_rh(): void
    {
        $user = $this->creerMajor();
        $response = $this->actingAs($user)->get('/rh/dashboard');
        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_agent_rh_peut_acceder_aux_routes_rh(): void
    {
        $user = $this->creerAgentRH();
        $response = $this->actingAs($user)->get('/rh/dashboard');
        $response->assertStatus(200);
    }

    // ──────────────────────────────────────────────────────
    // ISOLATION DES ROUTES MANAGER
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_agent_ne_peut_pas_acceder_aux_routes_manager(): void
    {
        $user = $this->creerAgent();
        $response = $this->actingAs($user)->get('/manager/dashboard');
        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_major_ne_peut_pas_acceder_aux_routes_manager(): void
    {
        $user = $this->creerMajor();
        $response = $this->actingAs($user)->get('/manager/dashboard');
        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_manager_accede_a_son_dashboard(): void
    {
        $user = $this->creerManager();
        $response = $this->actingAs($user)->get('/manager/dashboard');
        $response->assertStatus(200);
    }

    // ──────────────────────────────────────────────────────
    // ISOLATION DES ROUTES MAJOR
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_agent_ne_peut_pas_acceder_aux_routes_major(): void
    {
        $user = $this->creerAgent();
        $response = $this->actingAs($user)->get('/major/dashboard');
        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_major_accede_a_son_dashboard(): void
    {
        $user = $this->creerMajor();
        $response = $this->actingAs($user)->get('/major/dashboard');
        $response->assertStatus(200);
    }

    // ──────────────────────────────────────────────────────
    // ADMIN BYPASS (before() dans les Policies)
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function ladmin_peut_acceder_a_toutes_les_sections(): void
    {
        $admin = $this->creerAdmin();

        $routes = [
            '/admin/dashboard',
            '/admin/roles',
            '/admin/audit',
        ];

        foreach ($routes as $route) {
            $this->actingAs($admin)
                ->get($route)
                ->assertStatus(200, "L'admin doit accéder à $route");
        }
    }
}
