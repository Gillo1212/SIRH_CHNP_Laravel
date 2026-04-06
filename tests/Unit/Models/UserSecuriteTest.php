<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Tests\SirhTestCase;

/**
 * Tests unitaires — Sécurité du modèle User (Section 4.2.1.2)
 *
 * Vérifie les mécanismes de sécurité embarqués dans le modèle User :
 * - Complexité du mot de passe
 * - Verrouillage de compte
 * - Gestion des tentatives de connexion
 *
 * Aligné sur la Section 4.1.2.2 du mémoire :
 * "Mécanismes de confidentialité : authentification forte"
 */
class UserSecuriteTest extends SirhTestCase
{
    // ──────────────────────────────────────────────────────
    // VALIDATION DE LA COMPLEXITÉ DU MOT DE PASSE
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_mot_de_passe_trop_court_est_invalide(): void
    {
        $erreurs = User::validatePasswordComplexity('Ab1!');
        $this->assertNotEmpty($erreurs);
        $this->assertStringContainsString('8 caractères', $erreurs[0]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_mot_de_passe_sans_majuscule_est_invalide(): void
    {
        $erreurs = User::validatePasswordComplexity('password1!');
        $this->assertNotEmpty($erreurs);
        $contientErreurMajuscule = collect($erreurs)->some(
            fn($e) => str_contains($e, 'majuscule')
        );
        $this->assertTrue($contientErreurMajuscule);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_mot_de_passe_sans_minuscule_est_invalide(): void
    {
        $erreurs = User::validatePasswordComplexity('PASSWORD1!');
        $this->assertNotEmpty($erreurs);
        $this->assertTrue(collect($erreurs)->some(fn($e) => str_contains($e, 'minuscule')));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_mot_de_passe_sans_chiffre_est_invalide(): void
    {
        $erreurs = User::validatePasswordComplexity('Password!');
        $this->assertNotEmpty($erreurs);
        $this->assertTrue(collect($erreurs)->some(fn($e) => str_contains($e, 'chiffre')));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_mot_de_passe_sans_caractere_special_est_invalide(): void
    {
        $erreurs = User::validatePasswordComplexity('Password1');
        $this->assertNotEmpty($erreurs);
        $this->assertTrue(collect($erreurs)->some(fn($e) => str_contains($e, 'spécial')));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_mot_de_passe_fort_est_valide(): void
    {
        $erreurs = User::validatePasswordComplexity('Pikine@CHNP2025!');
        $this->assertEmpty($erreurs, 'Un mot de passe fort ne doit générer aucune erreur');
    }

    // ──────────────────────────────────────────────────────
    // VERROUILLAGE DE COMPTE
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_nouveau_compte_n_est_pas_verrouille(): void
    {
        $user = $this->creerAgent();
        $this->assertFalse($user->estVerouille());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function increment_tentatives_verrouille_apres_5_echecs(): void
    {
        $user = $this->creerAgent();

        for ($i = 0; $i < 4; $i++) {
            $user->incrementLoginAttempts();
            $user->refresh();
            $this->assertFalse($user->estVerouille(), "Pas encore verrouillé après " . ($i + 1) . " tentatives");
        }

        $user->incrementLoginAttempts(); // 5ème
        $user->refresh();

        $this->assertTrue($user->estVerouille(), 'Le compte doit être verrouillé après 5 tentatives');
        $this->assertEquals('Suspendu', $user->statut_compte);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_deverrouillage_remet_les_tentatives_a_zero(): void
    {
        $admin = $this->creerAdmin();

        $user = $this->creerAgent();
        $user->update(['verouille' => true, 'tentatives_connexion' => 5]);

        $this->actingAs($admin);
        $user->deverouiller();
        $user->refresh();

        $this->assertFalse($user->estVerouille());
        $this->assertEquals(0, $user->tentatives_connexion);
        $this->assertEquals('Actif', $user->statut_compte);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function reset_tentatives_reinitialise_le_compteur(): void
    {
        $user = $this->creerAgent();
        $user->update(['tentatives_connexion' => 3]);

        $user->resetLoginAttempts();
        $user->refresh();

        $this->assertEquals(0, $user->tentatives_connexion);
        $this->assertNotNull($user->derniere_connexion);
    }

    // ──────────────────────────────────────────────────────
    // ÉTATS DU COMPTE
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_compte_actif_est_detecte_correctement(): void
    {
        $user = $this->creerAgent();
        $this->assertTrue($user->estActif());
        $this->assertFalse($user->estSuspendu());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function un_compte_suspendu_est_detecte_correctement(): void
    {
        $user = $this->creerAgent();
        $user->update(['statut_compte' => 'Suspendu']);

        $this->assertFalse($user->estActif());
        $this->assertTrue($user->estSuspendu());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function la_suspension_d_un_compte_fonctionne(): void
    {
        $admin = $this->creerAdmin();
        $this->actingAs($admin);

        $user = $this->creerAgent();
        $user->suspendre('Comportement inapproprié');
        $user->refresh();

        $this->assertEquals('Suspendu', $user->statut_compte);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function la_desactivation_d_un_compte_fonctionne(): void
    {
        $admin = $this->creerAdmin();
        $this->actingAs($admin);

        $user = $this->creerAgent();
        $user->desactiver();
        $user->refresh();

        $this->assertEquals('Inactif', $user->statut_compte);
    }
}
