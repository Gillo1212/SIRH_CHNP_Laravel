<?php

namespace Tests\Feature\Securite;

use App\Models\Agent;
use Illuminate\Support\Facades\DB;
use Tests\SirhTestCase;

/**
 * Tests de sécurité — Chiffrement AES-256 (Section 4.2.1.2)
 *
 * Valide le mécanisme de confidentialité CID :
 * les données sensibles (adresse, téléphone, CNI) sont chiffrées
 * en base de données et ne jamais stockées en clair.
 *
 * Aligné sur la Section 4.1.2.2 du mémoire :
 * "Mécanismes de confidentialité : chiffrement des données au repos"
 */
class ChiffrementDonneesTest extends SirhTestCase
{
    private $service;
    private $userAgent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service   = $this->creerService();
        $this->userAgent = $this->creerAgent();
    }

    // ──────────────────────────────────────────────────────
    // CHIFFREMENT EN BASE
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function l_adresse_est_chiffree_en_base_de_donnees(): void
    {
        $adresseClaire = '45 Rue Thiong, Médina, Dakar';

        $agent = $this->creerDossierAgent($this->userAgent, $this->service, [
            'adresse' => $adresseClaire,
        ]);

        // Lire directement en base (sans passer par Eloquent)
        $rawValue = DB::table('agents')
            ->where('id_agent', $agent->id_agent)
            ->value('adresse');

        // La valeur brute NE doit PAS être en clair
        $this->assertNotEquals(
            $adresseClaire,
            $rawValue,
            'L\'adresse ne doit pas être stockée en clair en base de données'
        );

        // Mais via Eloquent elle doit être lisible
        $this->assertEquals(
            $adresseClaire,
            $agent->fresh()->adresse,
            'L\'adresse doit être déchiffrée correctement via Eloquent'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_telephone_est_chiffre_en_base_de_donnees(): void
    {
        $telephoneClaire = '77 123 45 67';

        $agent = $this->creerDossierAgent($this->userAgent, $this->service, [
            'telephone' => $telephoneClaire,
        ]);

        $rawValue = DB::table('agents')
            ->where('id_agent', $agent->id_agent)
            ->value('telephone');

        $this->assertNotEquals(
            $telephoneClaire,
            $rawValue,
            'Le téléphone ne doit pas être stocké en clair en base de données'
        );

        $this->assertEquals(
            $telephoneClaire,
            $agent->fresh()->telephone,
            'Le téléphone doit être déchiffré correctement via Eloquent'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function le_numero_cni_est_chiffre_en_base_de_donnees(): void
    {
        $cniClaire = '1 234 567 890 12';

        $agent = $this->creerDossierAgent($this->userAgent, $this->service, [
            'cni' => $cniClaire,
        ]);

        $rawValue = DB::table('agents')
            ->where('id_agent', $agent->id_agent)
            ->value('cni');

        $this->assertNotEquals(
            $cniClaire,
            $rawValue,
            'Le numéro CNI ne doit pas être stocké en clair'
        );

        $this->assertEquals(
            $cniClaire,
            $agent->fresh()->cni,
            'Le CNI doit être déchiffré correctement via Eloquent'
        );
    }

    // ──────────────────────────────────────────────────────
    // MISE À JOUR — RECHIFFREMENT
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function la_mise_a_jour_rechiffre_les_donnees_sensibles(): void
    {
        $agent = $this->creerDossierAgent($this->userAgent, $this->service, [
            'adresse' => 'Ancienne adresse',
        ]);

        $nouvelleAdresse = '99 Avenue Bourguiba, Pikine';
        $agent->update(['adresse' => $nouvelleAdresse]);

        // Vérifier que la nouvelle valeur brute est chiffrée
        $rawValue = DB::table('agents')
            ->where('id_agent', $agent->id_agent)
            ->value('adresse');

        $this->assertNotEquals($nouvelleAdresse, $rawValue);
        $this->assertEquals($nouvelleAdresse, $agent->fresh()->adresse);
    }

    // ──────────────────────────────────────────────────────
    // CHAMPS NON SENSIBLES (NE DOIVENT PAS ÊTRE CHIFFRÉS)
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function les_champs_non_sensibles_ne_sont_pas_chiffres(): void
    {
        $agent = $this->creerDossierAgent($this->userAgent, $this->service, [
            'nom'    => 'Diallo',
            'prenom' => 'Amadou',
        ]);

        $rawNom    = DB::table('agents')->where('id_agent', $agent->id_agent)->value('nom');
        $rawPrenom = DB::table('agents')->where('id_agent', $agent->id_agent)->value('prenom');

        // Nom et prénom doivent rester lisibles directement (pas de chiffrement)
        $this->assertEquals('Diallo', $rawNom, 'Le nom ne doit pas être chiffré');
        $this->assertEquals('Amadou', $rawPrenom, 'Le prénom ne doit pas être chiffré');
    }

    // ──────────────────────────────────────────────────────
    // MOTS DE PASSE — HACHAGE BCRYPT
    // ──────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\Test]
    public function les_mots_de_passe_sont_haches_en_base(): void
    {
        $user = $this->creerAgent();

        $rawPassword = DB::table('users')
            ->where('id', $user->id)
            ->value('password');

        // Le hash ne doit pas être le mot de passe en clair
        $this->assertNotEquals('Password1!', $rawPassword);

        // Mais doit vérifier correctement
        $this->assertTrue(
            password_verify('Password1!', $rawPassword),
            'Le mot de passe haché doit correspondre à l\'original'
        );
    }
}
