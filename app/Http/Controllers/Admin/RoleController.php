<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;

class RoleController extends Controller
{
    /** Rôles système non supprimables */
    private const SYSTEM_ROLES = ['Agent', 'Manager', 'AgentRH', 'DRH', 'AdminSystème'];

    /** Noms affichables */
    private const DISPLAY_NAMES = [
        'Agent'        => 'Agent (Personnel)',
        'Manager'      => 'Manager de Service',
        'AgentRH'      => 'Agent RH',
        'DRH'          => 'Directeur RH',
        'AdminSystème' => 'Administrateur Système',
    ];

    /** Icônes par rôle */
    private const ROLE_ICONS = [
        'Agent'        => 'fa-user',
        'Manager'      => 'fa-user-tie',
        'AgentRH'      => 'fa-user-nurse',
        'DRH'          => 'fa-user-cog',
        'AdminSystème' => 'fa-crown',
    ];

    /** Couleurs badge par rôle */
    private const ROLE_COLORS = [
        'Agent'        => '#6B7280',
        'Manager'      => '#D97706',
        'AgentRH'      => '#0891B2',
        'DRH'          => '#7C3AED',
        'AdminSystème' => '#DC2626',
    ];

    public function index()
    {
        $roles = Role::withCount(['permissions', 'users'])->get();

        $rolesData = $roles->map(function ($role) {
            return [
                'id'                => $role->id,
                'name'              => $role->name,
                'display_name'      => self::DISPLAY_NAMES[$role->name] ?? ucfirst(str_replace('_', ' ', $role->name)),
                'icon'              => self::ROLE_ICONS[$role->name] ?? 'fa-shield-alt',
                'color'             => self::ROLE_COLORS[$role->name] ?? '#0A4D8C',
                'permissions_count' => $role->permissions_count,
                'users_count'       => $role->users_count,
                'is_system'         => in_array($role->name, self::SYSTEM_ROLES),
                'is_admin'          => $role->name === 'AdminSystème',
                'created_at'        => $role->created_at,
            ];
        });

        $totalPermissions = Permission::count();

        return view('admin.roles.index', compact('rolesData', 'totalPermissions'));
    }

    public function create()
    {
        $groupedPermissions = $this->groupPermissions(Permission::all());
        return view('admin.roles.create', compact('groupedPermissions'));
    }

    public function store(StoreRoleRequest $request)
    {
        try {
            $role = Role::create([
                'name'       => $request->name,
                'guard_name' => 'web',
            ]);

            if ($request->filled('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            activity()
                ->performedOn($role)
                ->causedBy(auth()->user())
                ->withProperties(['permissions' => $request->permissions ?? []])
                ->log('Rôle créé');

            return redirect()
                ->route('admin.roles.index')
                ->with('success', "Rôle « {$role->name} » créé avec succès.");

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        $users = User::role($role->name)->with('agent')->get();

        $groupedPermissions = $this->groupPermissions($role->permissions);

        $auditLogs = Activity::forSubject($role)->latest()->take(15)->get();

        $displayName = self::DISPLAY_NAMES[$role->name] ?? ucfirst(str_replace('_', ' ', $role->name));
        $icon        = self::ROLE_ICONS[$role->name] ?? 'fa-shield-alt';
        $color       = self::ROLE_COLORS[$role->name] ?? '#0A4D8C';
        $isSystem    = in_array($role->name, self::SYSTEM_ROLES);

        return view('admin.roles.show', compact(
            'role', 'users', 'groupedPermissions', 'auditLogs',
            'displayName', 'icon', 'color', 'isSystem'
        ));
    }

    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        if ($role->name === 'AdminSystème') {
            return redirect()
                ->route('admin.roles.index')
                ->with('warning', 'Le rôle Administrateur Système ne peut pas être modifié.');
        }

        $allPermissions      = Permission::all();
        $groupedPermissions  = $this->groupPermissions($allPermissions);
        $rolePermissionIds   = $role->permissions->pluck('id')->toArray();
        $displayName         = self::DISPLAY_NAMES[$role->name] ?? ucfirst(str_replace('_', ' ', $role->name));

        return view('admin.roles.edit', compact(
            'role', 'groupedPermissions', 'rolePermissionIds', 'displayName'
        ));
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        $role = Role::findOrFail($id);

        if ($role->name === 'AdminSystème') {
            return redirect()
                ->route('admin.roles.index')
                ->with('error', 'Le rôle Administrateur Système ne peut pas être modifié.');
        }

        try {
            $oldPermissions = $role->permissions->pluck('name')->toArray();
            $newPermissions = $request->permissions ?? [];

            $role->syncPermissions($newPermissions);

            activity()
                ->performedOn($role)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old_permissions' => $oldPermissions,
                    'new_permissions' => $newPermissions,
                ])
                ->log('Permissions du rôle modifiées');

            return redirect()
                ->route('admin.roles.show', $role->id)
                ->with('success', 'Permissions mises à jour avec succès.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        if (in_array($role->name, self::SYSTEM_ROLES)) {
            return back()->with('error', 'Les rôles système ne peuvent pas être supprimés.');
        }

        $usersCount = User::role($role->name)->count();
        if ($usersCount > 0) {
            return back()->with('error', "Ce rôle est assigné à {$usersCount} utilisateur(s). Retirez d'abord ce rôle aux utilisateurs.");
        }

        try {
            activity()
                ->performedOn($role)
                ->causedBy(auth()->user())
                ->log('Rôle supprimé');

            $role->delete();

            return redirect()
                ->route('admin.roles.index')
                ->with('success', 'Rôle supprimé avec succès.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Grouper les permissions par catégorie métier.
     */
    private function groupPermissions($permissions): \Illuminate\Support\Collection
    {
        $groups = [
            'Personnel'    => ['voir_agents', 'creer_agent', 'modifier_agent', 'supprimer_agent', 'voir_propre_dossier', 'voir_equipe'],
            'Contrats'     => ['voir_contrats', 'creer_contrat', 'modifier_contrat', 'supprimer_contrat', 'renouveler_contrat'],
            'Congés'       => ['demander_conge', 'voir_mes_conges', 'voir_conges_equipe', 'valider_conge', 'approuver_conge', 'rejeter_conge', 'gerer_soldes'],
            'Absences'     => ['voir_mes_absences', 'justifier_absence', 'enregistrer_absence', 'modifier_absence', 'supprimer_absence'],
            'Planning'     => ['voir_mon_planning', 'voir_planning_service', 'creer_planning', 'modifier_planning', 'transmettre_planning', 'valider_planning', 'rejeter_planning'],
            'GED'          => ['voir_mes_documents', 'uploader_document', 'modifier_document', 'supprimer_document', 'rechercher_documents', 'telecharger_document'],
            'Reporting'    => ['voir_dashboard_agent', 'voir_dashboard_manager', 'voir_dashboard_rh', 'generer_rapport', 'exporter_donnees'],
            'DRH'          => ['view_kpis_strategiques', 'view_suivi_budgetaire', 'validate_decisions_finales', 'generate_bilan_social', 'view_rapports_direction', 'manage_decisions_rh', 'view_masse_salariale', 'export_rapports_direction'],
            'Administration' => ['gerer_utilisateurs', 'gerer_roles', 'gerer_permissions', 'voir_audit', 'exporter_audit', 'configurer_systeme'],
        ];

        $result      = collect();
        $assignedIds = collect();

        foreach ($groups as $groupName => $permNames) {
            $groupPerms = collect($permissions)->whereIn('name', $permNames)->values();
            if ($groupPerms->isNotEmpty()) {
                $result[$groupName] = $groupPerms;
                $assignedIds        = $assignedIds->merge($groupPerms->pluck('id'));
            }
        }

        $ungrouped = collect($permissions)->whereNotIn('id', $assignedIds->toArray())->values();
        if ($ungrouped->isNotEmpty()) {
            $result['Autres'] = $ungrouped;
        }

        return $result;
    }
}
