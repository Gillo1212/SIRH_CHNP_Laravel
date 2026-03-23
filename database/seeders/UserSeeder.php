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
    private const DEFAULT_NATIONALITY = 'Sénégalaise';

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
            'lieu_naissance'   => 'Dakar',
            'sexe'             => 'M',
            'nationalite'      => self::DEFAULT_NATIONALITY,
            'email'            => 'amadou.diop@chnp.sn',
            'date_recrutement' => '2010-01-15',
            'fonction'         => 'Administrateur Système',
            'grade'            => 'A1',
            'categorie_cp'     => 'Cadre_Superieur',
            'statut'           => 'Actif',
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
            'lieu_naissance'   => 'Thiès',
            'sexe'             => 'F',
            'nationalite'      => self::DEFAULT_NATIONALITY,
            'email'            => 'fatou.sarr@chnp.sn',
            'date_recrutement' => '2012-06-01',
            'fonction'         => 'Responsable RH',
            'grade'            => 'A2',
            'categorie_cp'     => 'Cadre_Superieur',
            'statut'           => 'Actif',
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

        $managerAgent = Agent::create([
            'user_id'          => $manager->id,
            'matricule'        => 'CHNP-00003',
            'nom'              => 'NDIAYE',
            'prenom'           => 'Moussa',
            'date_naissance'   => '1978-11-10',
            'lieu_naissance'   => 'Saint-Louis',
            'sexe'             => 'M',
            'nationalite'      => self::DEFAULT_NATIONALITY,
            'email'            => 'moussa.ndiaye@chnp.sn',
            'date_recrutement' => '2008-09-15',
            'fonction'         => 'Chef de Service Pédiatrie',
            'grade'            => 'P1',
            'categorie_cp'     => 'Cadre_Superieur',
            'statut'           => 'Actif',
            'id_service'       => 1,
        ]);

        DB::table('services')->where('id_service', 1)->update([
            'id_agent_manager' => $manager->id,
        ]);

        // 4. AGENT (Personnel de base)
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
            'lieu_naissance'   => 'Kaolack',
            'sexe'             => 'F',
            'nationalite'      => self::DEFAULT_NATIONALITY,
            'email'            => 'aissatou.fall@chnp.sn',
            'date_recrutement' => '2018-03-01',
            'fonction'         => 'Infirmière',
            'grade'            => 'IDE',
            'categorie_cp'     => 'Technicien_Superieur',
            'statut'           => 'Actif',
            'id_service'       => 1,
        ]);

        // 5. DRH (Directeur des Ressources Humaines)
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
            'lieu_naissance'   => 'Ziguinchor',
            'sexe'             => 'M',
            'nationalite'      => self::DEFAULT_NATIONALITY,
            'email'            => 'drh@chnp.sn',
            'date_recrutement' => '2005-04-01',
            'fonction'         => 'Directeur des Ressources Humaines',
            'grade'            => 'A1',
            'categorie_cp'     => 'Cadre_Superieur',
            'statut'           => 'Actif',
            'id_service'       => 10,
        ]);

        // 6-11. Agents supplémentaires pour tests
        $agentsData = [
            ['nom' => 'SY',     'prenom' => 'Ousmane', 'sexe' => 'M', 'service' => 1],
            ['nom' => 'CISSE',  'prenom' => 'Mariama', 'sexe' => 'F', 'service' => 2],
            ['nom' => 'BA',     'prenom' => 'Ibrahima','sexe' => 'M', 'service' => 5],
            ['nom' => 'DIALLO', 'prenom' => 'Khady',   'sexe' => 'F', 'service' => 3],
            ['nom' => 'GUEYE',  'prenom' => 'Cheikh',  'sexe' => 'M', 'service' => 8],
            ['nom' => 'SOW',    'prenom' => 'Aminata', 'sexe' => 'F', 'service' => 10],
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
                'lieu_naissance'   => 'Dakar',
                'sexe'             => $data['sexe'],
                'nationalite'      => self::DEFAULT_NATIONALITY,
                'email'            => strtolower($data['prenom']) . '.' . strtolower($data['nom']) . '@chnp.sn',
                'date_recrutement' => '2020-01-01',
                'fonction'         => 'Agent',
                'statut'           => 'Actif',
                'id_service'       => $data['service'],
            ]);
        }
    }
}
