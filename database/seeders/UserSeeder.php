<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Agent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    private const DEFAULT_PASSWORD = 'Password123!';

    public function run(): void
    {
        // 1. ADMINISTRATEUR SYSTÈME
        $admin = User::create([
            'name'                => 'Amadou DIOP',
            'login'               => 'amadou.diop',
            'password'            => Hash::make(self::DEFAULT_PASSWORD),
            'statut_compte'       => 'Actif',
            'verouille'           => false,
            'tentatives_connexion' => 0,
        ]);
        $admin->assignRole('AdminSystème');

        Agent::create([
            'user_id'          => $admin->id,
            'matricule'        => 'CHNP-00001',
            'nom'              => 'DIOP',
            'prenom'           => 'Amadou',
            'date_naissance'   => '1980-05-15',
            'sexe'             => 'M',
            'telephone'        => null,
            'cni'              => null,
            'religion'         => null,
            'categorie_cp'     => 'Cadre_Superieur',
            'famille_d_emploi' => 'Corps_Administratif',
            'statut_agent'     => 'Actif',
            'id_service'       => 17,
        ]);

        // 2. AGENT RH
        $rh = User::create([
            'name'                => 'Fatou SARR',
            'login'               => 'fatou.sarr',
            'password'            => Hash::make(self::DEFAULT_PASSWORD),
            'statut_compte'       => 'Actif',
            'verouille'           => false,
            'tentatives_connexion' => 0,
        ]);
        $rh->assignRole('AgentRH');

        Agent::create([
            'user_id'          => $rh->id,
            'matricule'        => 'CHNP-00002',
            'nom'              => 'SARR',
            'prenom'           => 'Fatou',
            'date_naissance'   => '1985-03-20',
            'sexe'             => 'F',
            'telephone'        => null,
            'cni'              => null,
            'religion'         => null,
            'categorie_cp'     => 'Cadre_Superieur',
            'famille_d_emploi' => 'Corps_Administratif',
            'statut_agent'     => 'Actif',
            'id_service'       => 10,
        ]);

        // 3. MANAGER (Pédiatrie)
        $manager = User::create([
            'name'                => 'Moussa NDIAYE',
            'login'               => 'moussa.ndiaye',
            'password'            => Hash::make(self::DEFAULT_PASSWORD),
            'statut_compte'       => 'Actif',
            'verouille'           => false,
            'tentatives_connexion' => 0,
        ]);
        $manager->assignRole('Manager');

        Agent::create([
            'user_id'          => $manager->id,
            'matricule'        => 'CHNP-00003',
            'nom'              => 'NDIAYE',
            'prenom'           => 'Moussa',
            'date_naissance'   => '1978-11-10',
            'sexe'             => 'M',
            'telephone'        => null,
            'cni'              => null,
            'religion'         => null,
            'categorie_cp'     => 'Cadre_Superieur',
            'famille_d_emploi' => 'Corps_Médical',
            'statut_agent'     => 'Actif',
            'id_service'       => 1,
        ]);

        DB::table('services')->where('id_service', 1)->update([
            'id_agent_manager' => $manager->id,
        ]);

        // 4. MAJOR DE SERVICE (Urgences - service paramédical)
        $major = User::create([
            'name'                => 'Rokhaya MBAYE',
            'login'               => 'rokhaya.mbaye',
            'password'            => Hash::make(self::DEFAULT_PASSWORD),
            'statut_compte'       => 'Actif',
            'verouille'           => false,
            'tentatives_connexion' => 0,
        ]);
        $major->assignRole('Major');

        Agent::create([
            'user_id'          => $major->id,
            'matricule'        => 'CHNP-00004B',
            'nom'              => 'MBAYE',
            'prenom'           => 'Rokhaya',
            'date_naissance'   => '1983-06-18',
            'sexe'             => 'F',
            'telephone'        => null,
            'cni'              => null,
            'religion'         => null,
            'categorie_cp'     => 'Cadre_Moyen',
            'famille_d_emploi' => 'Corps_Paramédical',
            'statut_agent'     => 'Actif',
            'id_service'       => 5, // Urgences (SAU)
        ]);

        DB::table('services')->where('id_service', 5)->update([
            'id_agent_major' => $major->id,
        ]);

        // 5. AGENT (Personnel de base — Pédiatrie)
        $agent = User::create([
            'name'                => 'Aïssatou FALL',
            'login'               => 'aissatou.fall',
            'password'            => Hash::make(self::DEFAULT_PASSWORD),
            'statut_compte'       => 'Actif',
            'verouille'           => false,
            'tentatives_connexion' => 0,
        ]);
        $agent->assignRole('Agent');

        Agent::create([
            'user_id'          => $agent->id,
            'matricule'        => 'CHNP-00004',
            'nom'              => 'FALL',
            'prenom'           => 'Aïssatou',
            'date_naissance'   => '1992-07-25',
            'sexe'             => 'F',
            'telephone'        => null,
            'cni'              => null,
            'religion'         => null,
            'categorie_cp'     => 'Technicien_Superieur',
            'famille_d_emploi' => 'Corps_Paramédical',
            'statut_agent'     => 'Actif',
            'id_service'       => 1,
        ]);

        // 6. DRH (Directeur des Ressources Humaines)
        $drh = User::create([
            'name'                => 'Ibrahima DIALLO',
            'login'               => 'ibrahima.diallo',
            'password'            => Hash::make(self::DEFAULT_PASSWORD),
            'statut_compte'       => 'Actif',
            'verouille'           => false,
            'tentatives_connexion' => 0,
        ]);
        $drh->assignRole('DRH');

        Agent::create([
            'user_id'          => $drh->id,
            'matricule'        => 'CHNP-00005',
            'nom'              => 'DIALLO',
            'prenom'           => 'Ibrahima',
            'date_naissance'   => '1975-09-12',
            'sexe'             => 'M',
            'telephone'        => null,
            'cni'              => null,
            'religion'         => null,
            'categorie_cp'     => 'Cadre_Superieur',
            'famille_d_emploi' => 'Corps_Administratif',
            'statut_agent'     => 'Actif',
            'id_service'       => 10,
        ]);

        // 6-11. Agents supplémentaires pour tests
        $agentsData = [
            ['nom' => 'SY',     'prenom' => 'Ousmane', 'sexe' => 'M', 'service' => 1,  'famille' => 'Corps_Médical'],
            ['nom' => 'CISSE',  'prenom' => 'Mariama', 'sexe' => 'F', 'service' => 2,  'famille' => 'Corps_Paramédical'],
            ['nom' => 'BA',     'prenom' => 'Ibrahima','sexe' => 'M', 'service' => 5,  'famille' => 'Corps_Paramédical'],
            ['nom' => 'DIALLO', 'prenom' => 'Khady',   'sexe' => 'F', 'service' => 3,  'famille' => 'Corps_de_Soutien'],
            ['nom' => 'GUEYE',  'prenom' => 'Cheikh',  'sexe' => 'M', 'service' => 8,  'famille' => 'Corps_Technique'],
            ['nom' => 'SOW',    'prenom' => 'Aminata', 'sexe' => 'F', 'service' => 10, 'famille' => 'Corps_Administratif'],
        ];

        foreach ($agentsData as $index => $data) {
            $u = User::create([
                'name'                => $data['prenom'] . ' ' . $data['nom'],
                'login'               => strtolower($data['prenom']) . '.' . strtolower($data['nom']),
                'password'            => Hash::make(self::DEFAULT_PASSWORD),
                'statut_compte'       => 'Actif',
                'verouille'           => false,
                'tentatives_connexion' => 0,
            ]);
            $u->assignRole('Agent');

            Agent::create([
                'user_id'          => $u->id,
                'matricule'        => 'CHNP-' . str_pad(6 + $index, 5, '0', STR_PAD_LEFT),
                'nom'              => $data['nom'],
                'prenom'           => $data['prenom'],
                'date_naissance'   => '1990-01-01',
                'sexe'             => $data['sexe'],
                'telephone'        => null,
                'cni'              => null,
                'religion'         => null,
                'categorie_cp'     => 'Agent_Administratif',
                'famille_d_emploi' => $data['famille'],
                'statut_agent'     => 'Actif',
                'id_service'       => $data['service'],
            ]);
        }
    }
}
