<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Http\Requests\Admin\StorePermissionRequest;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    private const PERMISSION_GROUPS = [
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

    public function index()
    {
        $permissions = Permission::with('roles')->get();
        $groupedPermissions = $this->groupPermissions($permissions);

        $stats = [
            'total'   => $permissions->count(),
            'modules' => $groupedPermissions->count(),
        ];

        return view('admin.permissions.index', compact('groupedPermissions', 'stats'));
    }

    public function create()
    {
        $modules = array_keys(self::PERMISSION_GROUPS);
        return view('admin.permissions.create', compact('modules'));
    }

    public function store(StorePermissionRequest $request)
    {
        try {
            $permission = Permission::create([
                'name'       => $request->name,
                'guard_name' => 'web',
            ]);

            activity()
                ->performedOn($permission)
                ->causedBy(auth()->user())
                ->log('Permission créée');

            return redirect()
                ->route('admin.permissions.index')
                ->with('success', "Permission « {$permission->name} » créée avec succès.");

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    public function matrix()
    {
        $roles       = Role::with('permissions')->get();
        $permissions = Permission::all();
        $groupedPermissions = $this->groupPermissions($permissions);

        return view('admin.permissions.matrix', compact('roles', 'groupedPermissions'));
    }

    public function updateMatrix(Request $request)
    {
        $request->validate([
            'role_id'       => 'required|exists:roles,id',
            'permission_id' => 'required|exists:permissions,id',
            'action'        => 'required|in:assign,remove',
        ]);

        try {
            $role       = Role::findOrFail($request->role_id);
            $permission = Permission::findOrFail($request->permission_id);

            if ($role->name === 'AdminSystème' && $request->action === 'remove') {
                return response()->json([
                    'success' => false,
                    'message' => 'Le rôle Administrateur Système doit conserver toutes les permissions.',
                ], 403);
            }

            if ($request->action === 'assign') {
                $role->givePermissionTo($permission);
                $message = "Permission « {$permission->name} » assignée au rôle « {$role->name} »";
            } else {
                $role->revokePermissionTo($permission);
                $message = "Permission « {$permission->name} » retirée du rôle « {$role->name} »";
            }

            activity()
                ->performedOn($role)
                ->causedBy(auth()->user())
                ->withProperties([
                    'permission' => $permission->name,
                    'action'     => $request->action,
                ])
                ->log('Matrice permissions modifiée');

            return response()->json(['success' => true, 'message' => $message]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);

        $rolesCount = $permission->roles()->count();
        if ($rolesCount > 0) {
            return back()->with('error', "Cette permission est utilisée par {$rolesCount} rôle(s). Impossible de la supprimer.");
        }

        try {
            activity()
                ->performedOn($permission)
                ->causedBy(auth()->user())
                ->log('Permission supprimée');

            $permission->delete();

            return redirect()
                ->route('admin.permissions.index')
                ->with('success', 'Permission supprimée avec succès.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    private function groupPermissions($permissions): \Illuminate\Support\Collection
    {
        $result      = collect();
        $assignedIds = collect();

        foreach (self::PERMISSION_GROUPS as $groupName => $permNames) {
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
