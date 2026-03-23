<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Contrat;
use App\Models\Demande;
use App\Models\Service;

class RHDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_agents'      => Agent::actif()->count(),
            'total_services'    => Service::count(),
            'contrats_actifs'   => Contrat::where('statut_contrat', 'Actif')->count(),
            'contrats_expirant' => Contrat::where('statut_contrat', 'Actif')
                ->where('date_fin', '<=', now()->addDays(60))
                ->where('date_fin', '>', now())
                ->count(),
            'demandes_pending'    => Demande::where('statut_demande', 'En_attente')->count(),
            'demandes_pending_rh' => Demande::where('statut_demande', 'Validé')->count(),

            'agents_par_service' => Service::withCount('agents')->get(),

            'recent_demandes' => Demande::with(['agent', 'conge', 'absence'])
                ->where('statut_demande', 'Validé')
                ->latest()
                ->take(5)
                ->get(),
        ];

        return view('rh.dashboard', compact('stats'));
    }
}
