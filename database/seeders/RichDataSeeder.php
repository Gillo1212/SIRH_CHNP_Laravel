<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * RichDataSeeder
 * ─────────────────────────────────────────────────────────────────────────────
 * 1. Crée un Manager par service (services sans manager)
 * 2. Assigne chaque manager à son service
 * 3. Crée des agents supplémentaires pour atteindre 10+ par service
 * ─────────────────────────────────────────────────────────────────────────────
 */
class RichDataSeeder extends Seeder
{
    private const PASSWORD = 'Password123!';

    /* ── Noms sénégalais pour générer des identités réalistes ─────────────── */
    private const NOMS = [
        'DIOP', 'NDIAYE', 'FALL', 'GUEYE', 'SARR', 'DIALLO', 'SECK', 'BARRY',
        'MBAYE', 'THIAM', 'DIOUF', 'CISSE', 'SOW', 'LY', 'WADE', 'KA',
        'CAMARA', 'TOURE', 'NIANG', 'BADJI', 'FAYE', 'MENDY', 'DIEME', 'BALDE',
        'NDOYE', 'THIAW', 'MBOUP', 'SAMB', 'NGOM', 'DIONE', 'COLY', 'BASSENE',
        'GOMIS', 'NDOUR', 'SANE', 'THIONGANE', 'DIAGNE', 'TRAORE', 'KEITA', 'COULIBALY',
    ];

    private const PRENOMS_M = [
        'Amadou', 'Ibrahima', 'Moussa', 'Omar', 'Cheikh', 'Abdoulaye', 'Mamadou',
        'Ousmane', 'Modou', 'Pape', 'Alioune', 'Seydou', 'Daouda', 'Malick',
        'Babacar', 'Serigne', 'Lamine', 'Youssou', 'Boubacar', 'Thierno',
    ];

    private const PRENOMS_F = [
        'Fatou', 'Aïssatou', 'Mariama', 'Khady', 'Aminata', 'Rokhaya', 'Ndéye',
        'Coumba', 'Binta', 'Seynabou', 'Adja', 'Fatoumata', 'Mame', 'Rama',
        'Sokhna', 'Yacine', 'Penda', 'Astou', 'Dieynaba', 'Marième',
    ];

    /* ── Famille d'emploi par type de service ─────────────────────────────── */
    private const SERVICE_FAMILLES = [
        'Pédiatrie'                   => 'Corps_Médical',
        'Maternité'                   => 'Corps_Médical',
        'Chirurgie'                   => 'Corps_Médical',
        'Médecine Interne'            => 'Corps_Médical',
        'Urgences (SAU)'              => 'Corps_Médical',
        'Réanimation'                 => 'Corps_Médical',
        'Radiologie'                  => 'Corps_Paramédical',
        'Laboratoire'                 => 'Corps_Paramédical',
        'Pharmacie'                   => 'Corps_Paramédical',
        'Service Ressources Humaines' => 'Corps_Administratif',
        'Comptabilité'                => 'Corps_Administratif',
        'Finances'                    => 'Corps_Administratif',
        'Accueil et Orientation'      => 'Corps_Administratif',
        'Hygiène et Salubrité'        => 'Corps_de_Soutien',
        'Maintenance'                 => 'Corps_Technique',
        'Sécurité'                    => 'Corps_de_Soutien',
        'Informatique'                => 'Corps_Technique',
    ];

    /* ── Catégories par type de service ────────────────────────────────────── */
    private const SERVICE_CATEGORIES = [
        'Pédiatrie'                   => ['Cadre_Superieur', 'Technicien_Superieur', 'Agent_de_Service'],
        'Maternité'                   => ['Cadre_Superieur', 'Technicien_Superieur', 'Agent_de_Service'],
        'Chirurgie'                   => ['Cadre_Superieur', 'Technicien_Superieur', 'Agent_de_Service'],
        'Médecine Interne'            => ['Cadre_Superieur', 'Technicien_Superieur', 'Agent_de_Service'],
        'Urgences (SAU)'              => ['Cadre_Superieur', 'Technicien_Superieur', 'Agent_de_Service'],
        'Réanimation'                 => ['Cadre_Superieur', 'Technicien_Superieur', 'Technicien'],
        'Radiologie'                  => ['Technicien_Superieur', 'Technicien', 'Agent_de_Service'],
        'Laboratoire'                 => ['Technicien_Superieur', 'Technicien', 'Agent_de_Service'],
        'Pharmacie'                   => ['Cadre_Superieur', 'Technicien_Superieur', 'Agent_de_Service'],
        'Service Ressources Humaines' => ['Cadre_Superieur', 'Cadre_Moyen', 'Agent_Administratif'],
        'Comptabilité'                => ['Cadre_Moyen', 'Agent_Administratif'],
        'Finances'                    => ['Cadre_Superieur', 'Cadre_Moyen', 'Agent_Administratif'],
        'Accueil et Orientation'      => ['Agent_Administratif', 'Agent_de_Service'],
        'Hygiène et Salubrité'        => ['Technicien', 'Agent_de_Service', 'Ouvrier'],
        'Maintenance'                 => ['Technicien', 'Ouvrier'],
        'Sécurité'                    => ['Agent_de_Service', 'Sans_Diplome'],
        'Informatique'                => ['Cadre_Moyen', 'Technicien_Superieur', 'Technicien'],
    ];

    private int $matriculeCounter = 12; // reprend après CHNP-00011
    private int $nomIndex         = 0;
    private int $prenomMIndex     = 0;
    private int $prenomFIndex     = 0;

    // ─────────────────────────────────────────────────────────────────────────
    public function run(): void
    {
        /* ── 1. Managers pour les services sans manager ────────────────── */
        $servicesWithoutManager = DB::table('services')
            ->whereNull('id_agent_manager')
            ->orderBy('id_service')
            ->get();

        foreach ($servicesWithoutManager as $svc) {
            $this->createManagerForService($svc);
        }

        /* ── 2. Compléter chaque service à 10 agents minimum ──────────────── */
        $allServices = DB::table('services')->orderBy('id_service')->get();

        foreach ($allServices as $svc) {
            $existing = DB::table('agents')
                ->where('id_service', $svc->id_service)
                ->count();

            $needed = max(0, 10 - $existing);

            for ($i = 0; $i < $needed; $i++) {
                $this->createAgentForService($svc);
            }
        }

        $this->command->info('RichDataSeeder terminé — ' . ($this->matriculeCounter - 12) . ' nouvelles entrées créées.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CRÉER UN MANAGER POUR UN SERVICE
    // ─────────────────────────────────────────────────────────────────────────
    private function createManagerForService(object $svc): void
    {
        $nom    = $this->nextNom();
        $prenom = $this->nextPrenom('M');
        $login  = $this->uniqueLogin(strtolower($prenom) . '.' . strtolower($nom));

        $user = User::create([
            'name'                => $prenom . ' ' . $nom,
            'login'               => $login,
            'password'            => Hash::make(self::PASSWORD),
            'statut_compte'       => 'Actif',
            'verouille'           => false,
            'tentatives_connexion'=> 0,
        ]);
        $user->assignRole('Manager');

        $famille = self::SERVICE_FAMILLES[$svc->nom_service] ?? 'Corps_Administratif';

        Agent::create([
            'user_id'           => $user->id,
            'matricule'         => $this->nextMatricule(),
            'nom'               => strtoupper($nom),
            'prenom'            => $prenom,
            'date_naissance'    => $this->randomDate('1970-01-01', '1980-12-31'),
            'sexe'              => 'M',
            'telephone'         => null,
            'cni'               => null,
            'religion'          => null,
            'categorie_cp'      => 'Cadre_Superieur',
            'famille_d_emploi'  => $famille,
            'statut_agent'    => 'Actif',
            'id_service'        => $svc->id_service,
        ]);

        // Assigner comme manager du service
        DB::table('services')
            ->where('id_service', $svc->id_service)
            ->update(['id_agent_manager' => $user->id]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CRÉER UN AGENT ORDINAIRE POUR UN SERVICE
    // ─────────────────────────────────────────────────────────────────────────
    private function createAgentForService(object $svc): void
    {
        $sexe   = (rand(0, 1) === 0) ? 'M' : 'F';
        $nom    = $this->nextNom();
        $prenom = $this->nextPrenom($sexe);
        $login  = $this->uniqueLogin(strtolower($prenom) . '.' . strtolower($nom));

        $famille    = self::SERVICE_FAMILLES[$svc->nom_service] ?? 'Corps_Administratif';
        $categories = self::SERVICE_CATEGORIES[$svc->nom_service] ?? ['Agent_Administratif'];
        $categorie  = $categories[array_rand($categories)];

        // 75% ont un compte
        $hasAccount = rand(0, 3) > 0;
        $userId     = null;

        if ($hasAccount) {
            $u = User::create([
                'name'                => $prenom . ' ' . $nom,
                'login'               => $login,
                'password'            => Hash::make(self::PASSWORD),
                'statut_compte'       => 'Actif',
                'verouille'           => false,
                'tentatives_connexion'=> 0,
            ]);
            $u->assignRole('Agent');
            $userId = $u->id;
        }

        Agent::create([
            'user_id'           => $userId,
            'matricule'         => $this->nextMatricule(),
            'nom'               => strtoupper($nom),
            'prenom'            => $prenom,
            'date_naissance'    => $this->randomDate('1975-01-01', '1998-12-31'),
            'sexe'              => $sexe,
            'telephone'         => null,
            'cni'               => null,
            'religion'          => null,
            'categorie_cp'      => $categorie,
            'famille_d_emploi'  => $famille,
            'statut_agent'    => $this->randomStatut(),
            'id_service'        => $svc->id_service,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    private function nextMatricule(): string
    {
        return 'CHNP-' . str_pad($this->matriculeCounter++, 5, '0', STR_PAD_LEFT);
    }

    private function nextNom(): string
    {
        $nom = self::NOMS[$this->nomIndex % count(self::NOMS)];
        $this->nomIndex++;
        return $nom;
    }

    private function nextPrenom(string $sexe): string
    {
        if ($sexe === 'M') {
            $p = self::PRENOMS_M[$this->prenomMIndex % count(self::PRENOMS_M)];
            $this->prenomMIndex++;
        } else {
            $p = self::PRENOMS_F[$this->prenomFIndex % count(self::PRENOMS_F)];
            $this->prenomFIndex++;
        }
        return $p;
    }

    private function randomDate(string $from, string $to): string
    {
        $start = strtotime($from);
        $end   = strtotime($to);
        return date('Y-m-d', rand($start, $end));
    }

    private function randomStatut(): string
    {
        $r = rand(1, 10);
        if ($r <= 7) return 'Actif';
        if ($r <= 8) return 'En_congé';
        if ($r <= 9) return 'Suspendu';
        return 'Actif';
    }

    private function uniqueLogin(string $base): string
    {
        $login = $base;
        $i = 1;
        while (DB::table('users')->where('login', $login)->exists()) {
            $login = $base . $i;
            $i++;
        }
        return $login;
    }
}
