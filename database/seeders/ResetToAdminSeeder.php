<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Vide la base (sauf l'admin) et crée 5 comptes de démonstration.
 * Mot de passe par défaut : Password123!
 */
class ResetToAdminSeeder extends Seeder
{
    private const PASSWORD = 'Password123!';
    private const NATIONALITE = 'Sénégalaise';

    public function run(): void
    {
        // ── 1. Trouver l'admin ────────────────────────────────────────────
        $admin = User::whereHas('roles', fn($q) => $q->where('name', 'AdminSystème'))->first();

        if (! $admin) {
            $this->command->error('Aucun AdminSystème trouvé. Abort.');
            return;
        }

        $adminAgentId = $admin->agent?->id_agent;

        $this->command->info("Admin conservé : {$admin->login} (ID {$admin->id})");

        // ── 2. Vider les tables liées (FK désactivées) ───────────────────
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Toutes les tables métier (pas besoin de filter, on vide tout)
        DB::table('conges')->truncate();
        DB::table('absences')->truncate();
        DB::table('prises_en_charge')->truncate();
        DB::table('demandes')->truncate();
        DB::table('contrats')->truncate();
        DB::table('mouvements')->truncate();
        DB::table('solde_conges')->truncate();
        DB::table('pieces_justificatives')->truncate();
        DB::table('dossier_agents')->truncate();
        DB::table('ligne_plannings')->truncate();
        DB::table('plannings')->truncate();
        DB::table('heures_sup')->truncate();
        DB::table('etageres')->truncate();
        DB::table('documents')->truncate();
        DB::table('notifications')->truncate();
        DB::table('activity_log')->truncate();
        DB::table('tickets_support')->truncate();

        // Enfants et conjoints (sauf ceux de l'admin)
        if ($adminAgentId) {
            DB::table('enfants') ->where('id_agent', '!=', $adminAgentId)->delete();
            DB::table('conjoints')->where('id_agent', '!=', $adminAgentId)->delete();
        } else {
            DB::table('enfants')->truncate();
            DB::table('conjoints')->truncate();
        }

        // Agents (sauf admin) — forceDelete pour ignorer soft deletes
        Agent::withTrashed()->where('id_agent', '!=', $adminAgentId)->forceDelete();

        // Users (sauf admin)
        $usersToDelete = User::where('id', '!=', $admin->id)->pluck('id');
        DB::table('model_has_roles')       ->whereIn('model_id', $usersToDelete)->delete();
        DB::table('model_has_permissions') ->whereIn('model_id', $usersToDelete)->delete();
        DB::table('user_preferences')      ->whereIn('user_id', $usersToDelete)->delete();
        User::whereIn('id', $usersToDelete)->delete();

        // Réinitialiser les managers et majors de services
        DB::table('services')->update(['id_agent_manager' => null, 'id_agent_major' => null]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info('Base vidée (hors admin).');

        // ── 3. Créer 6 nouveaux comptes ───────────────────────────────────
        $this->creerDRH();
        $this->creerAgentRH();
        $this->creerManager();
        $this->creerMajor();
        $this->creerAgent('OUMAR',   'Sow',     'M', 3,  'CHNP-00007');
        $this->creerAgent('MARIAMA', 'Cissé',   'F', 5,  'CHNP-00008');

        $this->command->table(
            ['Rôle', 'Login / Email', 'Mot de passe'],
            [
                ['DRH',         'ibrahima.diallo / drh@chnp.sn',     self::PASSWORD],
                ['AgentRH',     'fatou.sarr / rh@chnp.sn',           self::PASSWORD],
                ['Manager',     'moussa.ndiaye / manager@chnp.sn',   self::PASSWORD],
                ['Major',       'aminata.diop / major@chnp.sn',      self::PASSWORD],
                ['Agent',       'oumar.sow',                         self::PASSWORD],
                ['Agent',       'mariama.cisse',                     self::PASSWORD],
            ]
        );

        $this->command->info(' 6 comptes créés avec succès.');
    }

    // ─────────────────────────────────────────────────────────────────────

    private function creerDRH(): void
    {
        $user = User::create([
            'name'                => 'Ibrahima DIALLO',
            'login'               => 'ibrahima.diallo',
            'email'               => 'drh@chnp.sn',
            'password'            => Hash::make(self::PASSWORD),
            'statut_compte'       => 'Actif',
            'verouille'           => false,
            'tentatives_connexion'=> 0,
            'agent_completed'     => true,
        ]);
        $user->assignRole('DRH');

        Agent::create([
            'user_id'          => $user->id,
            'matricule'        => 'CHNP-00002',
            'nom'              => 'DIALLO',
            'prenom'           => 'Ibrahima',
            'date_naissance'   => '1975-09-12',
            'lieu_naissance'   => 'Ziguinchor',
            'sexe'             => 'M',
            'nationalite'      => self::NATIONALITE,
            'email'            => 'drh@chnp.sn',
            'date_prise_service' => '2005-04-01',
            'fontion'          => 'Directeur des Ressources Humaines',
            'grade'            => 'A1',
            'categorie_cp'     => 'Cadre_Superieur',
            'statut_agent'     => 'Actif',
            'account_pending'  => false,
            'id_service'       => 10,
        ]);
    }

    private function creerAgentRH(): void
    {
        $user = User::create([
            'name'                => 'Fatou SARR',
            'login'               => 'fatou.sarr',
            'email'               => 'rh@chnp.sn',
            'password'            => Hash::make(self::PASSWORD),
            'statut_compte'       => 'Actif',
            'verouille'           => false,
            'tentatives_connexion'=> 0,
            'agent_completed'     => true,
        ]);
        $user->assignRole('AgentRH');

        Agent::create([
            'user_id'          => $user->id,
            'matricule'        => 'CHNP-00003',
            'nom'              => 'SARR',
            'prenom'           => 'Fatou',
            'date_naissance'   => '1985-03-20',
            'lieu_naissance'   => 'Thiès',
            'sexe'             => 'F',
            'nationalite'      => self::NATIONALITE,
            'email'            => 'rh@chnp.sn',
            'date_prise_service' => '2012-06-01',
            'fontion'          => 'Responsable RH',
            'grade'            => 'A2',
            'categorie_cp'     => 'Cadre_Superieur',
            'statut_agent'     => 'Actif',
            'account_pending'  => false,
            'id_service'       => 10,
        ]);
    }

    private function creerManager(): void
    {
        $user = User::create([
            'name'                => 'Moussa NDIAYE',
            'login'               => 'moussa.ndiaye',
            'email'               => 'manager@chnp.sn',
            'password'            => Hash::make(self::PASSWORD),
            'statut_compte'       => 'Actif',
            'verouille'           => false,
            'tentatives_connexion'=> 0,
            'agent_completed'     => true,
        ]);
        $user->assignRole('Manager');

        Agent::create([
            'user_id'          => $user->id,
            'matricule'        => 'CHNP-00004',
            'nom'              => 'NDIAYE',
            'prenom'           => 'Moussa',
            'date_naissance'   => '1978-11-10',
            'lieu_naissance'   => 'Saint-Louis',
            'sexe'             => 'M',
            'nationalite'      => self::NATIONALITE,
            'email'            => 'manager@chnp.sn',
            'date_prise_service' => '2008-09-15',
            'fontion'          => 'Chef de Service Pédiatrie',
            'grade'            => 'P1',
            'categorie_cp'     => 'Cadre_Superieur',
            'statut_agent'     => 'Actif',
            'account_pending'  => false,
            'id_service'       => 1,
        ]);

        // Assigner comme manager du service Pédiatrie
        DB::table('services')->where('id_service', 1)->update(['id_agent_manager' => $user->id]);
    }

    private function creerMajor(): void
    {
        $user = User::create([
            'name'                => 'Aminata DIOP',
            'login'               => 'aminata.diop',
            'email'               => 'major@chnp.sn',
            'password'            => Hash::make(self::PASSWORD),
            'statut_compte'       => 'Actif',
            'verouille'           => false,
            'tentatives_connexion'=> 0,
            'agent_completed'     => true,
        ]);
        $user->assignRole('Major');

        Agent::create([
            'user_id'            => $user->id,
            'matricule'          => 'CHNP-00009',
            'nom'                => 'DIOP',
            'prenom'             => 'Aminata',
            'date_naissance'     => '1985-04-22',
            'lieu_naissance'     => 'Thiès',
            'sexe'               => 'F',
            'nationalite'        => self::NATIONALITE,
            'email'              => 'major@chnp.sn',
            'date_prise_service' => '2012-06-01',
            'fontion'            => 'Major Bloc Opératoire',
            'grade'              => 'TS1',
            'categorie_cp'       => 'Technicien_Superieur',
            'statut_agent'       => 'Actif',
            'account_pending'    => false,
            'id_service'         => 2,
        ]);

        // Assigner comme major du service (id 2)
        DB::table('services')->where('id_service', 2)->update(['id_agent_major' => $user->id]);
    }

    private function creerAgent(string $nom, string $prenom, string $sexe, int $serviceId, string $matricule): void
    {
        $login = strtolower(\Illuminate\Support\Str::ascii($prenom)) . '.' . strtolower(\Illuminate\Support\Str::ascii($nom));
        $login = preg_replace('/[^a-z0-9._-]/', '', $login);

        $user = User::create([
            'name'                => $prenom . ' ' . $nom,
            'login'               => $login,
            'email'               => $login . '@chnp.sn',
            'password'            => Hash::make(self::PASSWORD),
            'statut_compte'       => 'Actif',
            'verouille'           => false,
            'tentatives_connexion'=> 0,
            'agent_completed'     => true,
        ]);
        $user->assignRole('Agent');

        Agent::create([
            'user_id'          => $user->id,
            'matricule'        => $matricule,
            'nom'              => strtoupper($nom),
            'prenom'           => ucfirst(strtolower($prenom)),
            'date_naissance'   => '1993-06-15',
            'lieu_naissance'   => 'Dakar',
            'sexe'             => $sexe,
            'nationalite'      => self::NATIONALITE,
            'email'            => $login . '@chnp.sn',
            'date_prise_service' => '2019-01-15',
            'fontion'          => 'Agent de Santé',
            'grade'            => 'T1',
            'categorie_cp'     => 'Technicien',
            'statut_agent'     => 'Actif',
            'account_pending'  => false,
            'id_service'       => $serviceId,
        ]);
    }
}
