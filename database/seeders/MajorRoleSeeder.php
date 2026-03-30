<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Agent;

/**
 * MajorRoleSeeder — Patch idempotent pour l'ajout du rôle Major
 *
 * À exécuter sur une DB existante :
 *   php artisan db:seed --class=MajorRoleSeeder
 *
 * Opérations :
 *  1. Créer la permission voir_dashboard_major
 *  2. Créer le rôle Major avec ses permissions (convention underscore)
 *  3. Créer le compte de test rokhaya.mbaye / Password123! (Urgences SAU)
 */
class MajorRoleSeeder extends Seeder
{
    /**
     * Permissions accordées au rôle Major.
     * Convention underscore (même base que RoleAndPermissionSeeder existant).
     */
    private const PERMISSIONS_MAJOR = [
        // Héritage Agent
        'voir_propre_dossier',
        'demander_conge',
        'voir_mes_conges',
        'voir_mes_absences',
        'justifier_absence',
        'voir_mon_planning',
        'telecharger_document',
        'voir_dashboard_agent',
        // Gestion équipe (lecture)
        'voir_equipe',
        'voir_conges_equipe',
        // Absences équipe
        'enregistrer_absence',
        // Planning
        'voir_planning_service',
        'creer_planning',
        'modifier_planning',
        'transmettre_planning',
        // Dashboard Major exclusif
        'voir_dashboard_major',
    ];

    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── 1. Permission exclusive Major ───────────────────────
        Permission::firstOrCreate(['name' => 'voir_dashboard_major', 'guard_name' => 'web']);
        $this->command->info('  ✔ Permission voir_dashboard_major créée/vérifiée.');

        // ── 2. Rôle Major ───────────────────────────────────────
        $roleMajor = Role::firstOrCreate(['name' => 'Major', 'guard_name' => 'web']);

        $existingPerms = Permission::whereIn('name', self::PERMISSIONS_MAJOR)->pluck('name');
        $missing       = array_diff(self::PERMISSIONS_MAJOR, $existingPerms->toArray());

        if (!empty($missing)) {
            $this->command->warn('  ⚠ Permissions manquantes ignorées : ' . implode(', ', $missing));
        }

        $roleMajor->syncPermissions($existingPerms);
        $this->command->info("  ✔ Rôle Major : {$existingPerms->count()} permissions assignées.");

        // ── 3. Compte de test Major ─────────────────────────────
        $existingUser = User::where('login', 'rokhaya.mbaye')->first();

        if ($existingUser) {
            $this->command->warn('  ⚠ Utilisateur rokhaya.mbaye existe déjà — ignoré.');
            return;
        }

        $majorUser = User::create([
            'name'                 => 'Rokhaya MBAYE',
            'login'                => 'rokhaya.mbaye',
            'password'             => Hash::make('Password123!'),
            'statut_compte'        => 'Actif',
            'verouille'            => false,
            'tentatives_connexion' => 0,
        ]);
        $majorUser->assignRole('Major');

        // Déterminer le prochain numéro de matricule disponible
        $lastNum   = Agent::selectRaw("MAX(CAST(SUBSTRING(matricule, 6) AS UNSIGNED)) as max_num")->value('max_num') ?? 0;
        $matricule = 'CHNP-' . str_pad($lastNum + 1, 5, '0', STR_PAD_LEFT);

        // S'assurer que le matricule n'est pas déjà pris
        while (Agent::where('matricule', $matricule)->exists()) {
            $lastNum++;
            $matricule = 'CHNP-' . str_pad($lastNum + 1, 5, '0', STR_PAD_LEFT);
        }

        Agent::create([
            'user_id'           => $majorUser->id,
            'matricule'         => $matricule,
            'nom'               => 'MBAYE',
            'prenom'            => 'Rokhaya',
            'date_naissance'    => '1983-06-18',
            'lieu_naissance'    => 'Dakar',
            'sexe'              => 'F',
            'nationalite'       => 'Sénégalaise',
            'situation_familiale' => 'Célibataire',
            'date_prise_service'=> '2010-09-01',
            'categorie_cp'      => 'Cadre_Moyen',
            'famille_d_emploi'  => 'Corps_Paramédical',
            'statut_agent'      => 'Actif',
            'id_service'        => 5, // Urgences (SAU)
        ]);

        // Assigner ce major au service Urgences
        DB::table('services')->where('id_service', 5)->update([
            'id_agent_major' => $majorUser->id,
        ]);

        $this->command->info("  ✔ Compte Major créé : rokhaya.mbaye / Password123! (matricule {$matricule}, service Urgences SAU)");
        $this->command->newLine();
        $this->command->table(
            ['Champ', 'Valeur'],
            [
                ['Login',    'rokhaya.mbaye'],
                ['Password', 'Password123!'],
                ['Rôle',     'Major'],
                ['Service',  'Urgences (SAU) — id_service=5'],
                ['Matricule', $matricule],
            ]
        );
    }
}
