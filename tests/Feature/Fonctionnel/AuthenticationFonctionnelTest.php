<?php

namespace Tests\Feature\Fonctionnel;

use App\Models\User;
use Tests\SirhTestCase;

/**
 * Tests fonctionnels — Authentification (Section 4.2.1.1)
 *
 * Vérifie le workflow complet de connexion/déconnexion
 * et les mécanismes de sécurité associés (verrouillage).
 */
class AuthenticationFonctionnelTest extends SirhTestCase
{
    // ──────────────────────────────────────────────────────
    // CONNEXION
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_agent_peut_se_connecter_avec_login_valide(): void
    {
        $user = $this->creerAgent();
        $user->update(['login' => 'agent.test', 'password' => bcrypt('Password1!')]);

        $response = $this->post('/login', [
            'login'    => 'agent.test',
            'password' => 'Password1!',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_utilisateur_ne_peut_pas_se_connecter_avec_mauvais_mot_de_passe(): void
    {
        $user = $this->creerAgent();
        $user->update(['login' => 'agent.test2']);

        $this->post('/login', [
            'login'    => 'agent.test2',
            'password' => 'mauvais_password',
        ]);

        $this->assertGuest();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function la_connexion_necessite_le_champ_login(): void
    {
        $response = $this->post('/login', [
            'password' => 'Password1!',
        ]);

        $response->assertSessionHasErrors('login');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function la_connexion_necessite_le_champ_password(): void
    {
        $response = $this->post('/login', [
            'login' => 'quelquun',
        ]);

        $response->assertSessionHasErrors('password');
    }

    // ──────────────────────────────────────────────────────
    // VERROUILLAGE DE COMPTE
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_compte_verrouille_ne_peut_pas_se_connecter(): void
    {
        $user = $this->creerAgent();
        $user->update([
            'login'    => 'agent.verrouille',
            'verouille'=> true,
        ]);

        $response = $this->post('/login', [
            'login'    => 'agent.verrouille',
            'password' => 'Password1!',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('login');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_compte_se_verrouille_apres_5_tentatives_echouees(): void
    {
        $user = $this->creerAgent();
        $user->update(['login' => 'agent.brute']);

        // 4 tentatives échouées
        for ($i = 0; $i < 4; $i++) {
            $this->post('/login', ['login' => 'agent.brute', 'password' => 'mauvais']);
        }

        $user->refresh();
        $this->assertFalse($user->verouille, 'Le compte ne doit pas encore être verrouillé après 4 tentatives');

        // 5ème tentative — verrouillage
        $this->post('/login', ['login' => 'agent.brute', 'password' => 'mauvais']);

        $user->refresh();
        $this->assertTrue($user->verouille, 'Le compte doit être verrouillé après 5 tentatives');
    }

    // ──────────────────────────────────────────────────────
    // REDIRECTION PAR RÔLE
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_agent_rh_est_redirige_vers_son_dashboard(): void
    {
        $user = $this->creerAgentRH();
        $user->update(['login' => 'rh.test']);

        $response = $this->post('/login', [
            'login'    => 'rh.test',
            'password' => 'Password1!',
        ]);

        $response->assertRedirect('/rh/dashboard');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_agent_est_redirige_vers_son_dashboard(): void
    {
        $user = $this->creerAgent();
        $user->update(['login' => 'agent.dashboard']);

        $response = $this->post('/login', [
            'login'    => 'agent.dashboard',
            'password' => 'Password1!',
        ]);

        $response->assertRedirect('/agent/dashboard');
    }

    // ──────────────────────────────────────────────────────
    // DÉCONNEXION
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_utilisateur_peut_se_deconnecter(): void
    {
        $user = $this->creerAgent();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_utilisateur_non_connecte_est_redirige_vers_login(): void
    {
        $response = $this->get('/agent/dashboard');

        $response->assertRedirect('/login');
    }
}
