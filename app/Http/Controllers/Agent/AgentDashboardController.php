<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Demande;
use App\Models\SoldeConge;

class AgentDashboardController extends Controller
{
    public function index()
    {
        $user  = auth()->user();
        $agent = $user->agent;

        $stats = [
            'agent'          => $agent,
            'service'        => $agent?->service,
            'contrat_actif'  => $agent?->contratActif,

            'soldes_conges' => SoldeConge::where('id_agent', $agent?->id_agent)
                ->where('annee', date('Y'))
                ->with('typeConge')
                ->get(),

            'mes_demandes' => Demande::where('id_agent', $agent?->id_agent)
                ->with(['conge.typeConge', 'absence'])
                ->latest()
                ->take(5)
                ->get(),

            'demandes_pending' => Demande::where('id_agent', $agent?->id_agent)
                ->where('statut_demande', 'En_attente')
                ->count(),

            'demandes_approved' => Demande::where('id_agent', $agent?->id_agent)
                ->where('statut_demande', 'Approuvé')
                ->count(),
        ];

        return view('agent.dashboard', compact('stats'));
    }
}
