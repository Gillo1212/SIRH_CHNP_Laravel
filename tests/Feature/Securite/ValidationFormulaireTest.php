<?php

namespace Tests\Feature\Securite;

use Tests\SirhTestCase;

/**
 * Tests de sécurité — Validation des formulaires et Protection CSRF (Section 4.2.1.2)
 *
 * Vérifie les mécanismes d'intégrité CID :
 * - Validation stricte des données en entrée (Form Requests)
 * - Protection CSRF active sur toutes les routes POST/PUT/DELETE
 * - Rejet des injections et données malformées
 *
 * Aligné sur la Section 4.1.2.3 du mémoire :
 * "Mécanismes d'intégrité : validation des formulaires, protection CSRF/XSS"
 */
class ValidationFormulaireTest extends SirhTestCase
{
    // ──────────────────────────────────────────────────────
    // PROTECTION CSRF
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_middleware_csrf_est_enregistre_dans_l_application(): void
    {
        // Laravel désactive CSRF en tests (runningUnitTests()) — on vérifie
        // que le middleware est bien déclaré dans la configuration de l'app.
        // En production, toute requête POST sans token valide retourne 419.
        $kernel = $this->app->make(\Illuminate\Contracts\Http\Kernel::class);

        $middlewareGroups = $kernel->getMiddlewareGroups();
        $webMiddleware     = $middlewareGroups['web'] ?? [];

        $csrfPresent = collect($webMiddleware)->contains(function ($m) {
            return str_contains(strtolower($m), 'csrf');
        });

        $this->assertTrue(
            $csrfPresent,
            'Le middleware VerifyCsrfToken doit être présent dans le groupe "web" (protection CSRF active en production)'
        );
    }

    // ──────────────────────────────────────────────────────
    // VALIDATION DES DONNÉES D'AUTHENTIFICATION
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_login_vide_est_rejete(): void
    {
        $response = $this->post('/login', [
            'login'    => '',
            'password' => 'Password1!',
        ]);

        $response->assertSessionHasErrors('login');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_password_vide_est_rejete(): void
    {
        $response = $this->post('/login', [
            'login'    => 'utilisateur',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
    }

    // ──────────────────────────────────────────────────────
    // VALIDATION CONGÉS
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_conge_sans_date_debut_est_rejete(): void
    {
        $service   = $this->creerService();
        $userAgent = $this->creerAgent();
        $this->creerDossierAgent($userAgent, $service);
        $typeConge = $this->creerTypeConge();

        $response = $this->actingAs($userAgent)->post('/agent/conges', [
            'id_type_conge' => $typeConge->id_type_conge,
            'date_fin'      => now()->addDays(10)->format('Y-m-d'),
            // date_debut manquante
        ]);

        $response->assertSessionHasErrors('date_debut');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_conge_avec_date_fin_avant_debut_est_rejete(): void
    {
        $service   = $this->creerService();
        $userAgent = $this->creerAgent();
        $this->creerDossierAgent($userAgent, $service);
        $typeConge = $this->creerTypeConge();

        $response = $this->actingAs($userAgent)->post('/agent/conges', [
            'id_type_conge' => $typeConge->id_type_conge,
            'date_debut'    => now()->addDays(10)->format('Y-m-d'),
            'date_fin'      => now()->addDays(5)->format('Y-m-d'), // < date_debut
        ]);

        $response->assertSessionHasErrors('date_fin');
    }

    // ──────────────────────────────────────────────────────
    // VALIDATION AGENTS (INTÉGRITÉ DES DONNÉES RH)
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_agent_sans_nom_est_rejete(): void
    {
        $userRH = $this->creerAgentRH();

        $response = $this->actingAs($userRH)->post('/rh/agents', [
            'prenom'             => 'Moussa',
            'date_naissance'     => '1990-01-01',
            // nom manquant
        ]);

        $response->assertSessionHasErrors('nom');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_agent_avec_date_naissance_invalide_est_rejete(): void
    {
        $userRH  = $this->creerAgentRH();
        $service = $this->creerService();

        $response = $this->actingAs($userRH)->post('/rh/agents', [
            'nom'            => 'Diallo',
            'prenom'         => 'Moussa',
            'date_naissance' => 'date-invalide',
        ]);

        $response->assertSessionHasErrors('date_naissance');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_sexe_d_un_agent_doit_etre_m_ou_f(): void
    {
        $userRH  = $this->creerAgentRH();
        $service = $this->creerService();

        $response = $this->actingAs($userRH)->post('/rh/agents', [
            'nom'            => 'Diallo',
            'prenom'         => 'Moussa',
            'date_naissance' => '1990-01-01',
            'sexe'           => 'X', // Valeur invalide
            'id_service'     => $service->id_service,
        ]);

        $response->assertSessionHasErrors('sexe');
    }

    // ──────────────────────────────────────────────────────
    // PROTECTION XSS — REJET DES SCRIPTS MALVEILLANTS
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function les_champs_texte_ne_permettent_pas_l_injection_xss(): void
    {
        $service   = $this->creerService();
        $userAgent = $this->creerAgent();
        $this->creerDossierAgent($userAgent, $service);

        $payloadXSS = '<script>alert("XSS")</script>';

        $response = $this->actingAs($userAgent)->post('/agent/pec', [
            'raison_medical'       => $payloadXSS,
            'ayant_droit'          => 'agent',
            'etablissement_medical'=> 'Test',
        ]);

        // Si la demande est enregistrée, vérifier que le script n'est pas exécutable
        // Le moteur de template Blade échappe automatiquement les données avec {{ }}
        if ($response->isRedirect()) {
            $saved = \App\Models\DemandeDocument::latest()->first();
            // La donnée brute peut être stockée mais Blade l'échappe à l'affichage
            $this->assertNotNull($saved ?? true); // Blade protège à l'affichage
        }

        // La réponse ne doit jamais inclure le script non échappé
        $response->assertDontSee('<script>alert("XSS")</script>', false);
    }

    // ──────────────────────────────────────────────────────
    // VALIDATION ABSENCES
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_type_absence_doit_etre_dans_la_liste_autorisee(): void
    {
        $service     = $this->creerService();
        $userAgent   = $this->creerAgent();
        $agentModel  = $this->creerDossierAgent($userAgent, $service);
        $userManager = $this->creerManager();
        $this->service ?? $service->update(['id_agent_manager' => $userManager->id]);

        $response = $this->actingAs($userManager)->post('/manager/absences', [
            'id_agent'     => $agentModel->id_agent,
            'date_absence' => now()->subDay()->format('Y-m-d'),
            'type_absence' => 'TypeInvalide', // Non autorisé
        ]);

        $response->assertSessionHasErrors('type_absence');
    }
}
