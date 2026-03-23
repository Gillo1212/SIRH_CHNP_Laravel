<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // =====================================================
        // CRÉATION DES PERMISSIONS
        // =====================================================
        
        // Permissions Gestion Personnel
        Permission::create(['name' => 'voir_agents']);
        Permission::create(['name' => 'creer_agent']);
        Permission::create(['name' => 'modifier_agent']);
        Permission::create(['name' => 'supprimer_agent']);
        Permission::create(['name' => 'voir_propre_dossier']);
        Permission::create(['name' => 'voir_equipe']);
        
        // Permissions Contrats
        Permission::create(['name' => 'voir_contrats']);
        Permission::create(['name' => 'creer_contrat']);
        Permission::create(['name' => 'modifier_contrat']);
        Permission::create(['name' => 'supprimer_contrat']);
        Permission::create(['name' => 'renouveler_contrat']);
        
        // Permissions Congés
        Permission::create(['name' => 'demander_conge']);
        Permission::create(['name' => 'voir_mes_conges']);
        Permission::create(['name' => 'voir_conges_equipe']);
        Permission::create(['name' => 'valider_conge']); // Manager
        Permission::create(['name' => 'approuver_conge']); // RH
        Permission::create(['name' => 'rejeter_conge']);
        Permission::create(['name' => 'gerer_soldes']);
        
        // Permissions Absences
        Permission::create(['name' => 'voir_mes_absences']);
        Permission::create(['name' => 'justifier_absence']);
        Permission::create(['name' => 'enregistrer_absence']); // Manager/RH
        Permission::create(['name' => 'modifier_absence']);
        Permission::create(['name' => 'supprimer_absence']);
        
        // Permissions Planning
        Permission::create(['name' => 'voir_mon_planning']);
        Permission::create(['name' => 'voir_planning_service']);
        Permission::create(['name' => 'creer_planning']); // Manager
        Permission::create(['name' => 'modifier_planning']); // Manager
        Permission::create(['name' => 'transmettre_planning']); // Manager
        Permission::create(['name' => 'valider_planning']); // RH
        Permission::create(['name' => 'rejeter_planning']); // RH
        
        // Permissions GED
        Permission::create(['name' => 'voir_mes_documents']);
        Permission::create(['name' => 'uploader_document']); // RH
        Permission::create(['name' => 'modifier_document']); // RH
        Permission::create(['name' => 'supprimer_document']); // RH
        Permission::create(['name' => 'rechercher_documents']);
        Permission::create(['name' => 'telecharger_document']);
        
        // Permissions Reporting
        Permission::create(['name' => 'voir_dashboard_agent']);
        Permission::create(['name' => 'voir_dashboard_manager']);
        Permission::create(['name' => 'voir_dashboard_rh']);
        Permission::create(['name' => 'generer_rapport']);
        Permission::create(['name' => 'exporter_donnees']);
        
        // Permissions Administration
        Permission::create(['name' => 'gerer_utilisateurs']);
        Permission::create(['name' => 'gerer_roles']);
        Permission::create(['name' => 'gerer_permissions']);
        Permission::create(['name' => 'voir_audit']);
        Permission::create(['name' => 'exporter_audit']);
        Permission::create(['name' => 'configurer_systeme']);
        
        // =====================================================
        // CRÉATION DES RÔLES
        // =====================================================
        
        // 1. AGENT (Personnel)
        $roleAgent = Role::create(['name' => 'Agent']);
        $roleAgent->givePermissionTo([
            'voir_propre_dossier',
            'demander_conge',
            'voir_mes_conges',
            'voir_mes_absences',
            'justifier_absence',
            'voir_mon_planning',
            'voir_mes_documents',
            'telecharger_document',
            'voir_dashboard_agent',
        ]);
        
        // 2. MANAGER DE SERVICE
        $roleManager = Role::create(['name' => 'Manager']);
        $roleManager->givePermissionTo([
            // Tout ce que l'Agent peut faire
            'voir_propre_dossier',
            'demander_conge',
            'voir_mes_conges',
            'voir_mes_absences',
            'justifier_absence',
            'voir_mon_planning',
            'voir_mes_documents',
            'telecharger_document',
            'voir_dashboard_agent',
            
            // Gestion équipe
            'voir_equipe',
            'voir_conges_equipe',
            'valider_conge',
            'rejeter_conge',
            'enregistrer_absence',
            'modifier_absence',
            'supprimer_absence',
            
            // Planning
            'voir_planning_service',
            'creer_planning',
            'modifier_planning',
            'transmettre_planning',
            
            // Dashboard
            'voir_dashboard_manager',
        ]);
        
        // 3. AGENT RH
        $roleRH = Role::create(['name' => 'AgentRH']);
        $roleRH->givePermissionTo([
            // Gestion Personnel complète
            'voir_agents',
            'creer_agent',
            'modifier_agent',
            'supprimer_agent',
            
            // Gestion Contrats
            'voir_contrats',
            'creer_contrat',
            'modifier_contrat',
            'supprimer_contrat',
            'renouveler_contrat',
            
            // Gestion Congés complète
            'voir_mes_conges',
            'demander_conge',
            'voir_conges_equipe',
            'approuver_conge',
            'rejeter_conge',
            'gerer_soldes',
            
            // Gestion Absences
            'voir_mes_absences',
            'enregistrer_absence',
            'modifier_absence',
            'supprimer_absence',
            
            // Planning
            'voir_mon_planning',
            'voir_planning_service',
            'valider_planning',
            'rejeter_planning',
            
            // GED complète
            'voir_mes_documents',
            'uploader_document',
            'modifier_document',
            'supprimer_document',
            'rechercher_documents',
            'telecharger_document',
            
            // Reporting
            'voir_dashboard_rh',
            'generer_rapport',
            'exporter_donnees',
        ]);
        
        // 4. DRH (Directeur des Ressources Humaines)
        // Permissions exclusives DRH
        $permissionsDRHExclusives = [
            'view_kpis_strategiques',
            'view_suivi_budgetaire',
            'validate_decisions_finales',
            'generate_bilan_social',
            'view_rapports_direction',
            'manage_decisions_rh',
            'view_masse_salariale',
            'export_rapports_direction',
        ];

        foreach ($permissionsDRHExclusives as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $roleDRH = Role::create(['name' => 'DRH']);

        // Hérite de toutes les permissions AgentRH
        $roleDRH->givePermissionTo($roleRH->permissions);

        // + Permissions exclusives DRH
        $roleDRH->givePermissionTo($permissionsDRHExclusives);

        // 5. ADMINISTRATEUR SYSTÈME
        $roleAdmin = Role::create(['name' => 'AdminSystème']);
        $roleAdmin->givePermissionTo(Permission::all()); // Toutes les permissions
    }
}