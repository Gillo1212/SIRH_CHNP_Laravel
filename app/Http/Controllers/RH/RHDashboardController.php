<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Agent;
use App\Models\Contrat;
use App\Models\Demande;
use App\Models\Planning;
use App\Models\Service;

class RHDashboardController extends Controller
{
    public function index()
    {
        // ── KPIs ──────────────────────────────────────────────────────
        $totalAgents      = Agent::actif()->count();
        $totalServices    = Service::count();
        $contratsActifs   = Contrat::where('statut_contrat', 'Actif')->count();
        $contratsExpiring = Contrat::where('statut_contrat', 'Actif')
            ->where('date_fin', '<=', now()->addDays(60))
            ->where('date_fin', '>', now())
            ->count();
        $pendingLeaves    = Demande::where('statut_demande', 'En_attente')->count();
        $enConge          = Agent::where('statut_agent', 'En_congé')->count();
        $absencesToday    = Absence::whereDate('date_absence', today())->count();
        $planningsPending = Planning::where('statut_planning', 'Transmis')->count();

        // ── Graphique : Effectifs par service ──────────────────────────
        $servicesRaw   = Service::withCount(['agents' => fn ($q) => $q->actif()])->get();
        $servicesLabels = $servicesRaw->pluck('nom_service')->toArray();
        $servicesData  = $servicesRaw->pluck('agents_count')->toArray();

        // ── Graphique : Demandes de congés par type (année en cours) ───
        $congesRaw = Demande::where('type_demande', 'Conge')
            ->whereYear('created_at', date('Y'))
            ->with('conge.typeConge')
            ->get()
            ->groupBy(fn ($d) => $d->conge?->typeConge?->libelle ?? 'Autre')
            ->map->count();
        $congesLabels = $congesRaw->keys()->values()->toArray();
        $congesData   = $congesRaw->values()->toArray();

        // ── Table : Demandes de congés (En_attente + Validé Manager) ───
        $recentDemandes = Demande::with(['agent.service', 'conge.typeConge'])
            ->whereIn('statut_demande', ['En_attente', 'Validé'])
            ->where('type_demande', 'Conge')
            ->latest()
            ->take(5)
            ->get();

        // ── Table : Contrats à renouveler ──────────────────────────────
        $contratsExpirantList = Contrat::with('agent')
            ->where('statut_contrat', 'Actif')
            ->where('date_fin', '<=', now()->addDays(60))
            ->where('date_fin', '>', now())
            ->orderBy('date_fin')
            ->take(5)
            ->get();

        return view('rh.dashboard', compact(
            'totalAgents', 'totalServices', 'contratsActifs', 'contratsExpiring',
            'pendingLeaves', 'enConge', 'absencesToday', 'planningsPending',
            'servicesLabels', 'servicesData', 'congesLabels', 'congesData',
            'recentDemandes', 'contratsExpirantList'
        ));
    }
}
