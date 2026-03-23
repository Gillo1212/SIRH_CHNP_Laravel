<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LogAudit;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Statistiques pour le dashboard admin
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::compteActif()->count(),
            'locked_users' => User::verouille()->count(),
            'suspended_users' => User::suspendu()->count(),
            'recent_logs' => LogAudit::with('utilisateur')->orderBy('date_evenement', 'desc')->take(10)->get(),
            'roles_count' => \Spatie\Permission\Models\Role::count(),
            'permissions_count' => \Spatie\Permission\Models\Permission::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}