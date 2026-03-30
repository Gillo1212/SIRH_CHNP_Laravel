<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\LogAudit;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // ── KPIs ──────────────────────────────────────────────────────
        $totalUsers         = User::count();
        $activeUsers        = User::compteActif()->count();
        $lockedUsers        = User::verouille()->count();
        $suspendedUsers     = User::suspendu()->count();
        $totalAgents        = Agent::actif()->count();
        $logsAujourdhui     = LogAudit::whereDate('date_evenement', today())->count();
        $tentativesEchouees = User::where('tentatives_connexion', '>', 0)->count();
        $rolesCount         = Role::count();

        // ── Graphique : Distribution des rôles ────────────────────────
        $rolesRaw = Role::withCount('users')->get();
        $rolesLabels = $rolesRaw->pluck('name')->toArray();
        $rolesData   = $rolesRaw->pluck('users_count')->toArray();

        // ── Graphique : Activité système 7 derniers jours ─────────────
        $activiteLabels = [];
        $activiteData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $day              = now()->subDays($i);
            $activiteLabels[] = $day->isoFormat('ddd D');
            $activiteData[]   = LogAudit::whereDate('date_evenement', $day->toDateString())->count();
        }

        // ── Logs récents ──────────────────────────────────────────────
        $recentLogs = LogAudit::with('utilisateur')
            ->orderByDesc('date_evenement')
            ->take(6)
            ->get();

        // ── Utilisateurs actifs récemment ─────────────────────────────
        $recentUsers = User::whereNotNull('derniere_connexion')
            ->orderByDesc('derniere_connexion')
            ->with('roles')
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'activeUsers', 'lockedUsers', 'suspendedUsers',
            'totalAgents', 'logsAujourdhui', 'tentativesEchouees', 'rolesCount',
            'rolesLabels', 'rolesData', 'activiteLabels', 'activiteData',
            'recentLogs', 'recentUsers'
        ));
    }
}
