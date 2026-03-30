<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

/**
 * PermissionSeeder — RBAC Complet SIRH CHNP
 * Crée toutes les permissions granulaires (163 au total)
 * Chaque permission correspond à une action précise → Confidentialité CID
 */
class PermissionSeeder extends Seeder
{
    /** Toutes les permissions organisées par module */
    public const ALL_PERMISSIONS = [

        // ══════════════════════════════════════════════════════
        // MODULE UTILISATEURS & AUTH
        // ══════════════════════════════════════════════════════
        'users.view',
        'users.view-own',
        'users.create',
        'users.update',
        'users.update-own',
        'users.delete',
        'users.activate',
        'users.unlock',
        'users.reset-password',
        'users.export',

        // ══════════════════════════════════════════════════════
        // MODULE RÔLES & PERMISSIONS
        // ══════════════════════════════════════════════════════
        'roles.view',
        'roles.create',
        'roles.update',
        'roles.delete',
        'roles.assign',
        'permissions.view',
        'permissions.assign',

        // ══════════════════════════════════════════════════════
        // MODULE AGENTS (DOSSIER PERSONNEL)
        // ══════════════════════════════════════════════════════
        'agents.view-all',
        'agents.view-service',
        'agents.view-own',
        'agents.create',
        'agents.update',
        'agents.update-own',
        'agents.delete',
        'agents.export',
        'agents.search',
        'agents.upload-photo',

        // ══════════════════════════════════════════════════════
        // MODULE STRUCTURE ORGANISATIONNELLE
        // ══════════════════════════════════════════════════════
        'divisions.view',
        'divisions.create',
        'divisions.update',
        'divisions.delete',
        'services.view',
        'services.create',
        'services.update',
        'services.delete',
        'mouvements.view',
        'mouvements.create',
        'mouvements.update',
        'mouvements.delete',

        // ══════════════════════════════════════════════════════
        // MODULE FAMILLE (CONJOINTS & ENFANTS)
        // ══════════════════════════════════════════════════════
        'conjoints.view',
        'conjoints.create',
        'conjoints.update',
        'conjoints.delete',
        'enfants.view',
        'enfants.create',
        'enfants.update',
        'enfants.delete',

        // ══════════════════════════════════════════════════════
        // MODULE CONTRATS
        // ══════════════════════════════════════════════════════
        'contrats.view-all',
        'contrats.view-service',
        'contrats.view-own',
        'contrats.create',
        'contrats.update',
        'contrats.delete',
        'contrats.renew',
        'contrats.close',
        'contrats.export',
        'contrats.alerts',

        // ══════════════════════════════════════════════════════
        // MODULE TYPES DE CONTRAT
        // ══════════════════════════════════════════════════════
        'type-contrats.view',
        'type-contrats.create',
        'type-contrats.update',
        'type-contrats.delete',

        // ══════════════════════════════════════════════════════
        // MODULE CONGÉS
        // ══════════════════════════════════════════════════════
        'conges.view-all',
        'conges.view-service',
        'conges.view-own',
        'conges.create',
        'conges.update-own',
        'conges.cancel-own',
        'conges.validate',
        'conges.reject',
        'conges.approve',
        'conges.manage-soldes',
        'conges.export',

        // ══════════════════════════════════════════════════════
        // MODULE TYPES DE CONGÉ & SOLDES
        // ══════════════════════════════════════════════════════
        'type-conges.view',
        'type-conges.create',
        'type-conges.update',
        'type-conges.delete',
        'soldes-conges.view',
        'soldes-conges.update',
        'soldes-conges.reset',

        // ══════════════════════════════════════════════════════
        // MODULE ABSENCES
        // ══════════════════════════════════════════════════════
        'absences.view-all',
        'absences.view-service',
        'absences.view-own',
        'absences.create',
        'absences.update',
        'absences.delete',
        'absences.justify',
        'absences.validate-justif',
        'absences.export',
        'absences.statistics',

        // ══════════════════════════════════════════════════════
        // MODULE TYPES D'ABSENCE
        // ══════════════════════════════════════════════════════
        'type-absences.view',
        'type-absences.create',
        'type-absences.update',
        'type-absences.delete',

        // ══════════════════════════════════════════════════════
        // MODULE PLANNINGS
        // ══════════════════════════════════════════════════════
        'plannings.view-all',
        'plannings.view-service',
        'plannings.view-own',
        'plannings.create',
        'plannings.update',
        'plannings.delete',
        'plannings.submit',
        'plannings.validate',
        'plannings.reject',
        'plannings.export',

        // ══════════════════════════════════════════════════════
        // MODULE TYPES DE POSTE (PLANNING)
        // ══════════════════════════════════════════════════════
        'type-postes.view',
        'type-postes.create',
        'type-postes.update',
        'type-postes.delete',

        // ══════════════════════════════════════════════════════
        // MODULE GED (DOCUMENTS)
        // ══════════════════════════════════════════════════════
        'documents.view-all',
        'documents.view-own',
        'documents.create',
        'documents.update',
        'documents.delete',
        'documents.download',
        'documents.archive',
        'documents.search',

        // ══════════════════════════════════════════════════════
        // MODULE DOSSIER AGENT (GED)
        // ══════════════════════════════════════════════════════
        'dossiers.view',
        'dossiers.create',
        'dossiers.update',
        'dossiers.close',

        // ══════════════════════════════════════════════════════
        // MODULE PRISES EN CHARGE
        // ══════════════════════════════════════════════════════
        'prises-en-charge.view-all',
        'prises-en-charge.view-own',
        'prises-en-charge.create',
        'prises-en-charge.update',
        'prises-en-charge.delete',
        'prises-en-charge.download',

        // ══════════════════════════════════════════════════════
        // MODULE DEMANDES ADMINISTRATIVES
        // ══════════════════════════════════════════════════════
        'demandes-admin.view-all',
        'demandes-admin.view-own',
        'demandes-admin.create',
        'demandes-admin.process',
        'demandes-admin.reject',

        // ══════════════════════════════════════════════════════
        // MODULE REPORTING & DASHBOARD
        // ══════════════════════════════════════════════════════
        'dashboard.view-global',
        'dashboard.view-service',
        'dashboard.view-personal',
        'dashboard.view-major',
        'reports.view',
        'reports.generate',
        'reports.export-pdf',
        'reports.export-excel',
        'reports.schedule',

        // ══════════════════════════════════════════════════════
        // MODULE HEURES SUPPLÉMENTAIRES
        // ══════════════════════════════════════════════════════
        'heures-sup.view-all',
        'heures-sup.view-service',
        'heures-sup.view-own',
        'heures-sup.create',
        'heures-sup.update',
        'heures-sup.delete',
        'heures-sup.validate',
        'heures-sup.export',

        // ══════════════════════════════════════════════════════
        // MODULE AUDIT TRAIL (ADMIN)
        // ══════════════════════════════════════════════════════
        'audit.view',
        'audit.export',
        'audit.filter',
        'audit.view-connexions',
        'audit.view-echecs',

        // ══════════════════════════════════════════════════════
        // MODULE PARAMÈTRES SYSTÈME (ADMIN)
        // ══════════════════════════════════════════════════════
        'settings.view',
        'settings.update',
        'settings.notifications',
        'settings.security',
        'settings.backup',

        // ══════════════════════════════════════════════════════
        // MODULE SAUVEGARDES (ADMIN)
        // ══════════════════════════════════════════════════════
        'backups.view',
        'backups.create',
        'backups.download',
        'backups.delete',
        'backups.restore',
        'backups.schedule',

        // ══════════════════════════════════════════════════════
        // MODULE NOTIFICATIONS
        // ══════════════════════════════════════════════════════
        'notifications.view-own',
        'notifications.manage',
        'notifications.send',
    ];

    public function run(): void
    {
        // Guard web pour toutes les permissions
        foreach (self::ALL_PERMISSIONS as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web']
            );
        }

        $this->command->info(' ' . count(self::ALL_PERMISSIONS) . ' permissions créées/vérifiées.');
    }
}
