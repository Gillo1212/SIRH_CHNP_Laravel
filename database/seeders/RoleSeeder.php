<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * RoleSeeder — Matrice RBAC complète SIRH CHNP
 * Implémente la Confidentialité CID via séparation stricte des droits.
 *
 * Hiérarchie : Agent < Manager < AgentRH < DRH < AdminSystème
 */
class RoleSeeder extends Seeder
{
    // ────────────────────────────────────────────────────────
    // PERMISSIONS PAR RÔLE
    // ────────────────────────────────────────────────────────

    /** Permissions de base partagées par TOUS les rôles */
    private const PERMISSIONS_COMMUN = [
        'users.view-own',
        'users.update-own',
        'agents.view-own',
        'agents.update-own',
        'agents.upload-photo',
        'contrats.view-own',
        'conges.view-own',
        'conges.create',
        'conges.update-own',
        'conges.cancel-own',
        'absences.view-own',
        'absences.justify',
        'plannings.view-own',
        'documents.view-own',
        'documents.download',
        'prises-en-charge.view-own',
        'prises-en-charge.create',
        'prises-en-charge.download',
        'demandes-admin.view-own',
        'demandes-admin.create',
        'dashboard.view-personal',
        'notifications.view-own',
        'conjoints.view',
        'enfants.view',
        'soldes-conges.view',
        'type-conges.view',
        'services.view',
        'divisions.view',
        'heures-sup.view-own',
    ];

    /** Permissions supplémentaires Major (s'ajoutent aux permissions communes) — responsable paramédical */
    private const PERMISSIONS_MAJOR = [
        'agents.view-service',
        'agents.search',
        'contrats.view-service',
        'contrats.alerts',
        'conges.view-service',
        'absences.view-service',
        'absences.create',
        'absences.validate-justif',
        'absences.statistics',
        'plannings.view-service',
        'plannings.create',
        'plannings.update',
        'plannings.delete',
        'plannings.submit',
        'dashboard.view-service',
        'dashboard.view-major',
        'mouvements.view',
        'heures-sup.view-service',
        'heures-sup.create',
        'documents.search',
        'reports.view',
    ];

    /** Permissions supplémentaires Manager (s'ajoutent aux permissions communes) */
    private const PERMISSIONS_MANAGER = [
        'agents.view-service',
        'agents.search',
        'contrats.view-service',
        'contrats.alerts',
        'conges.view-service',
        'conges.validate',
        'conges.reject',
        'absences.view-service',
        'absences.create',
        'absences.validate-justif',
        'absences.statistics',
        'plannings.view-service',
        'plannings.create',
        'plannings.update',
        'plannings.delete',
        'plannings.submit',
        'dashboard.view-service',
        'mouvements.view',
        'heures-sup.view-service',
        'heures-sup.create',
        'documents.search',
        'reports.view',
    ];

    /** Permissions supplémentaires AgentRH (s'ajoutent à Manager) */
    private const PERMISSIONS_AGENT_RH = [
        // Utilisateurs
        'users.view',
        'users.create',
        'users.update',
        'users.export',
        // Agents
        'agents.view-all',
        'agents.create',
        'agents.update',
        'agents.export',
        // Structure
        'services.create',
        'services.update',
        'services.delete',
        'divisions.create',
        'divisions.update',
        'divisions.delete',
        // Famille
        'conjoints.create',
        'conjoints.update',
        'conjoints.delete',
        'enfants.create',
        'enfants.update',
        'enfants.delete',
        // Contrats
        'contrats.view-all',
        'contrats.create',
        'contrats.update',
        'contrats.delete',
        'contrats.renew',
        'contrats.close',
        'contrats.export',
        'type-contrats.view',
        'type-contrats.create',
        'type-contrats.update',
        // Congés
        'conges.view-all',
        'conges.approve',
        'conges.manage-soldes',
        'conges.export',
        'soldes-conges.update',
        'soldes-conges.reset',
        'type-conges.create',
        'type-conges.update',
        'type-conges.delete',
        // Absences
        'absences.view-all',
        'absences.update',
        'absences.delete',
        'absences.export',
        'type-absences.view',
        'type-absences.create',
        'type-absences.update',
        // Plannings
        'plannings.view-all',
        'plannings.validate',
        'plannings.reject',
        'plannings.export',
        'type-postes.view',
        'type-postes.create',
        'type-postes.update',
        // Mouvements
        'mouvements.create',
        'mouvements.update',
        'mouvements.delete',
        // GED
        'documents.view-all',
        'documents.create',
        'documents.update',
        'documents.delete',
        'documents.archive',
        'dossiers.view',
        'dossiers.create',
        'dossiers.update',
        'dossiers.close',
        // PEC
        'prises-en-charge.view-all',
        'prises-en-charge.update',
        'prises-en-charge.delete',
        // Demandes admin
        'demandes-admin.view-all',
        'demandes-admin.process',
        'demandes-admin.reject',
        // Reporting
        'dashboard.view-global',
        'reports.generate',
        'reports.export-pdf',
        'reports.export-excel',
        // Heures sup
        'heures-sup.view-all',
        'heures-sup.update',
        'heures-sup.delete',
        'heures-sup.validate',
        'heures-sup.export',
        // Notifications
        'notifications.manage',
    ];

    /** Permissions supplémentaires DRH (s'ajoutent à AgentRH) */
    private const PERMISSIONS_DRH = [
        // Décisions & validations stratégiques
        'mouvements.delete',           // Annuler mouvements
        'contrats.delete',             // Supprimer contrats
        'agents.delete',               // Archiver agents
        'type-contrats.delete',
        'type-absences.delete',
        'type-postes.delete',
        // Rapports avancés
        'reports.schedule',
        // Utilisateurs (lecture étendue)
        'users.view',
        // Notifications envoi
        'notifications.send',
    ];

    /** Permissions exclusives AdminSystème (sur les permissions communes) */
    private const PERMISSIONS_ADMIN = [
        // Tout ce que DRH n'a pas
        'users.delete',
        'users.activate',
        'users.unlock',
        'users.reset-password',
        // RBAC
        'roles.view',
        'roles.create',
        'roles.update',
        'roles.delete',
        'roles.assign',
        'permissions.view',
        'permissions.assign',
        // Audit
        'audit.view',
        'audit.export',
        'audit.filter',
        'audit.view-connexions',
        'audit.view-echecs',
        // Paramètres
        'settings.view',
        'settings.update',
        'settings.notifications',
        'settings.security',
        'settings.backup',
        // Sauvegardes
        'backups.view',
        'backups.create',
        'backups.download',
        'backups.delete',
        'backups.restore',
        'backups.schedule',
        // Notifications admin
        'notifications.send',
        'notifications.manage',
        // Types de référentiel
        'type-contrats.delete',
        'type-absences.delete',
        'type-postes.delete',
    ];

    // ────────────────────────────────────────────────────────

    public function run(): void
    {
        // Flush le cache des permissions (Spatie)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Créer les 6 rôles ────────────────────────────────
        $roleAgent  = Role::firstOrCreate(['name' => 'Agent',        'guard_name' => 'web']);
        $roleMajor  = Role::firstOrCreate(['name' => 'Major',        'guard_name' => 'web']);
        $roleManager= Role::firstOrCreate(['name' => 'Manager',      'guard_name' => 'web']);
        $roleRH     = Role::firstOrCreate(['name' => 'AgentRH',      'guard_name' => 'web']);
        $roleDRH    = Role::firstOrCreate(['name' => 'DRH',          'guard_name' => 'web']);
        $roleAdmin  = Role::firstOrCreate(['name' => 'AdminSystème', 'guard_name' => 'web']);

        // ── Construire les jeux de permissions cumulatifs ────
        $permsAgent   = self::PERMISSIONS_COMMUN;
        $permsMajor   = array_unique(array_merge($permsAgent,   self::PERMISSIONS_MAJOR));
        $permsManager = array_unique(array_merge($permsAgent,   self::PERMISSIONS_MANAGER));
        $permsRH      = array_unique(array_merge($permsManager, self::PERMISSIONS_AGENT_RH));
        $permsDRH     = array_unique(array_merge($permsRH,      self::PERMISSIONS_DRH));
        // Admin = toutes les permissions existantes
        $allPerms     = Permission::pluck('name')->toArray();

        // ── Assigner les permissions ─────────────────────────
        $this->syncPermissions($roleAgent,   $permsAgent,   'Agent');
        $this->syncPermissions($roleMajor,   $permsMajor,   'Major');
        $this->syncPermissions($roleManager, $permsManager, 'Manager');
        $this->syncPermissions($roleRH,      $permsRH,      'AgentRH');
        $this->syncPermissions($roleDRH,     $permsDRH,     'DRH');
        $this->syncPermissions($roleAdmin,   $allPerms,     'AdminSystème');

        $this->command->info(' Matrice RBAC appliquée avec succès.');
        $this->command->table(
            ['Rôle', 'Nb permissions'],
            [
                ['Agent',        count($permsAgent)],
                ['Major',        count($permsMajor)],
                ['Manager',      count($permsManager)],
                ['AgentRH',      count($permsRH)],
                ['DRH',          count($permsDRH)],
                ['AdminSystème', count($allPerms)],
            ]
        );
    }

    /**
     * Synchronise les permissions d'un rôle en ignorant les manquantes
     * (évite les erreurs si une permission n'a pas été créée)
     */
    private function syncPermissions(Role $role, array $permissionNames, string $roleName): void
    {
        $existing = Permission::whereIn('name', $permissionNames)->pluck('name');
        $missing  = array_diff($permissionNames, $existing->toArray());

        if (!empty($missing)) {
            $this->command->warn("  ⚠ {$roleName} : permissions manquantes ignorées : " . implode(', ', $missing));
        }

        $role->syncPermissions($existing);
        $this->command->info("  ✔ {$roleName} : {$existing->count()} permissions assignées.");
    }
}
