<?php

namespace App\Http\Controllers\DRH;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Agent;
use App\Models\Contrat;
use App\Models\Demande;
use App\Models\Mouvement;
use App\Models\Planning;
use App\Models\Service;

class DRHDashboardController extends Controller
{
    public function index()
    {
        // ── KPIs Stratégiques ─────────────────────────────────────────
        $effectifTotal    = Agent::actif()->count();
        $agentsEnConge    = Agent::where('statut_agent', 'En_congé')->count();
        $absencesMonth    = Absence::whereMonth('date_absence', now()->month)
            ->whereYear('date_absence', now()->year)->count();
        $tauxAbsenteisme  = $effectifTotal > 0
            ? round(($absencesMonth / ($effectifTotal * 22)) * 100, 1) : 0;

        // ── KPIs Opérationnels ────────────────────────────────────────
        $contratsExpiring  = Contrat::where('statut_contrat', 'Actif')
            ->where('date_fin', '<=', now()->addDays(60))
            ->where('date_fin', '>', now())->count();
        $demandesEnAttente = Demande::whereIn('statut_demande', ['En_attente', 'Validé'])->count();
        $mouvementsMois    = Mouvement::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->count();
        $mouvementsEnAttenteCount = Mouvement::enAttente()->count();
        $planningsPending  = Planning::where('statut_planning', 'Transmis')->count();

        // ── Graphique 1 : Évolution effectifs 12 mois ─────────────────
        // Simulated: count recrutements cumulés mois par mois (actifs recrutés jusqu'à ce mois)
        $effectifs12Mois = [];
        $labels12Mois    = [];
        for ($i = 11; $i >= 0; $i--) {
            $month           = now()->subMonths($i);
            $labels12Mois[]  = $month->isoFormat('MMM YY');
            $effectifs12Mois[] = Agent::actif()
                ->whereDate('created_at', '<=', $month->endOfMonth()->toDateString())
                ->count();
        }

        // ── Graphique 2 : Types de contrats actifs ────────────────────
        $contratsRaw = Contrat::where('statut_contrat', 'Actif')
            ->get()
            ->groupBy('type_contrat')
            ->map->count();
        $contratsTypesLabels = $contratsRaw->keys()->values()->toArray();
        $contratsTypesData   = $contratsRaw->values()->toArray();

        // ── Graphique 3 : Absentéisme par service (ce mois) ───────────
        $absParService = Service::withCount(['agents as absences_count' => function ($q) {
            $q->whereHas('demandes', function ($d) {
                $d->whereHas('absence', function ($a) {
                    $a->whereMonth('date_absence', now()->month)
                      ->whereYear('date_absence', now()->year);
                });
            });
        }])->get();
        $absServicesLabels = $absParService->pluck('nom_service')->toArray();
        $absServicesData   = $absParService->pluck('absences_count')->toArray();

        // ── Graphique 4 : Pyramide des âges ──────────────────────────
        $pyramideLabels = ['< 30 ans', '30-40 ans', '40-50 ans', '50-60 ans', '> 60 ans'];
        $pyramideData   = [
            Agent::actif()->whereDate('date_naissance', '>', now()->subYears(30))->count(),
            Agent::actif()->whereDate('date_naissance', '<=', now()->subYears(30))
                ->whereDate('date_naissance', '>', now()->subYears(40))->count(),
            Agent::actif()->whereDate('date_naissance', '<=', now()->subYears(40))
                ->whereDate('date_naissance', '>', now()->subYears(50))->count(),
            Agent::actif()->whereDate('date_naissance', '<=', now()->subYears(50))
                ->whereDate('date_naissance', '>', now()->subYears(60))->count(),
            Agent::actif()->whereDate('date_naissance', '<=', now()->subYears(60))->count(),
        ];

        // ── Mouvements en attente de validation DRH ───────────────────
        $mouvementsEnAttente = Mouvement::enAttente()
            ->with(['agent.service', 'createur'])
            ->latest()
            ->take(5)
            ->get();

        // ── Contrats à renouveler ─────────────────────────────────────
        $contratsExpirantList = Contrat::with('agent')
            ->where('statut_contrat', 'Actif')
            ->where('date_fin', '<=', now()->addDays(60))
            ->where('date_fin', '>', now())
            ->orderBy('date_fin')
            ->take(5)
            ->get();

        return view('drh.dashboard', compact(
            'effectifTotal', 'agentsEnConge', 'absencesMonth', 'tauxAbsenteisme',
            'contratsExpiring', 'demandesEnAttente', 'mouvementsMois', 'mouvementsEnAttenteCount',
            'planningsPending', 'effectifs12Mois', 'labels12Mois',
            'contratsTypesLabels', 'contratsTypesData',
            'absServicesLabels', 'absServicesData',
            'pyramideLabels', 'pyramideData',
            'mouvementsEnAttente', 'contratsExpirantList'
        ));
    }

    public function kpis()
    {
        return view('drh.kpis');
    }

    public function budget()
    {
        return view('drh.budget');
    }

}
