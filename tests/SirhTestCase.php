<?php

namespace Tests;

use App\Models\Agent;
use App\Models\Demande;
use App\Models\Service;
use App\Models\TypeConge;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * SirhTestCase — Classe de base commune à tous les tests SIRH CHNP
 *
 * Fournit les helpers pour créer des utilisateurs avec rôles,
 * des agents, des services et des données de référence.
 *
 * Aligné sur la Section 4.2.1 du mémoire :
 *   - 4.2.1.1 Tests fonctionnels et de non-régression
 *   - 4.2.1.2 Tests de sécurité fonctionnelle
 *   - 4.2.1.3 Tests de performance et de disponibilité
 */
abstract class SirhTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Vider le cache Spatie Permission avant chaque test
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Seeder rôles et permissions
        $this->seed(RoleAndPermissionSeeder::class);
    }

    // ══════════════════════════════════════════════════════
    // HELPERS — CRÉATION D'UTILISATEURS PAR RÔLE
    // ══════════════════════════════════════════════════════

    protected function creerUtilisateur(string $role, array $attrs = []): User
    {
        $user = User::create(array_merge([
            'login'              => 'user_' . strtolower($role) . '_' . uniqid(),
            'email'              => strtolower($role) . '_' . uniqid() . '@chnp.sn',
            'name'               => 'Test ' . $role,
            'password'           => bcrypt('Password1!'),
            'statut_compte'      => 'Actif',
            'verouille'          => false,
            'tentatives_connexion' => 0,
            'agent_completed'    => true,
        ], $attrs));

        $user->assignRole($role);

        return $user;
    }

    protected function creerAdmin(): User     { return $this->creerUtilisateur('AdminSystème'); }
    protected function creerDRH(): User       { return $this->creerUtilisateur('DRH'); }
    protected function creerAgentRH(): User   { return $this->creerUtilisateur('AgentRH'); }
    protected function creerManager(): User   { return $this->creerUtilisateur('Manager'); }
    protected function creerMajor(): User     { return $this->creerUtilisateur('Major'); }
    protected function creerAgent(): User     { return $this->creerUtilisateur('Agent'); }

    // ══════════════════════════════════════════════════════
    // HELPERS — DONNÉES MÉTIER
    // ══════════════════════════════════════════════════════

    protected function creerService(array $attrs = []): Service
    {
        return Service::create(array_merge([
            'nom_service'  => 'Service Test ' . uniqid(),
            'type_service' => 'Clinique',
        ], $attrs));
    }

    protected function creerDossierAgent(User $user, Service $service, array $attrs = []): Agent
    {
        return Agent::create(array_merge([
            'user_id'            => $user->id,
            'matricule'          => 'CHNP-' . str_pad(rand(1, 9999), 5, '0', STR_PAD_LEFT),
            'nom'                => 'Diallo',
            'prenom'             => 'Moussa',
            'date_naissance'     => '1990-05-15',
            'lieu_naissance'     => 'Dakar',
            'sexe'               => 'M',
            'situation_familiale'=> 'Célibataire',
            'nationalite'        => 'Sénégalaise',
            'adresse'            => '123 Rue Pikine, Dakar',
            'telephone'          => '77 000 00 00',
            'date_prise_service' => '2020-01-01',
            'fonction'            => 'Infirmier',
            'categorie_cp'       => 'Technicien',
            'famille_d_emploi'   => 'Corps_Paramédical',
            'statut_agent'       => 'actif',
            'id_service'         => $service->id_service,
        ], $attrs));
    }

    protected function creerTypeConge(array $attrs = []): TypeConge
    {
        return TypeConge::create(array_merge([
            'libelle'        => 'Congé Administratif',
            'duree'          => '30 jours par an',
            'nb_jours_droit' => 30,
            'deductible'     => true,
        ], $attrs));
    }

    /**
     * Crée une demande + congé prêt pour validation.
     */
    protected function creerDemande(Agent $agent, string $statut = 'En_attente'): Demande
    {
        return Demande::create([
            'id_agent'       => $agent->id_agent,
            'type_demande'   => 'Conge',
            'statut_demande' => $statut,
        ]);
    }
}
