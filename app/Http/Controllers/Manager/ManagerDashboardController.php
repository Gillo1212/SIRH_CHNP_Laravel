<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Demande;
use App\Models\LignePlanning;
use App\Models\Service;

class ManagerDashboardController extends Controller
{
    public function index()
    {
        $user    = auth()->user();
        $service = Service::where('id_agent_manager', $user->id)->first();

        if (!$service) {
            return view('manager.dashboard', ['noService' => true]);
        }

        $serviceId = $service->id_service;

        // ── Effectifs ─────────────────────────────────────────────────
        $totalAgents   = $service->agents()->actif()->count();
        $agentsEnConge = $service->agents()->where('statut_agent', 'En_congé')->count();

        $absencesToday = Absence::whereHas('demande', function ($q) use ($serviceId) {
            $q->whereHas('agent', fn ($q2) => $q2->where('id_service', $serviceId));
        })->whereDate('date_absence', today())->count();

        $agentsPresents = max(0, $totalAgents - $absencesToday - $agentsEnConge);

        // ── Congés à valider (Manager 1ère étape) ─────────────────────
        $congesEnAttenteCount = Demande::whereHas('agent', fn ($q) => $q->where('id_service', $serviceId))
            ->where('type_demande', 'Conge')
            ->where('statut_demande', 'En_attente')
            ->count();

        $congesEnAttente = Demande::whereHas('agent', fn ($q) => $q->where('id_service', $serviceId))
            ->where('type_demande', 'Conge')
            ->where('statut_demande', 'En_attente')
            ->with(['agent', 'conge.typeConge'])
            ->latest()
            ->take(5)
            ->get();

        // ── Planning semaine (lignes validées du service) ──────────────
        $lignesSemaine = LignePlanning::with(['agent', 'typePoste'])
            ->whereHas('planning', fn ($q) => $q
                ->where('id_service', $serviceId)
                ->where('statut_planning', 'Validé'))
            ->whereBetween('date_poste', [
                now()->startOfWeek()->toDateString(),
                now()->endOfWeek()->toDateString(),
            ])
            ->orderBy('date_poste')
            ->orderBy('heure_debut')
            ->get()
            ->groupBy(fn ($l) => $l->date_poste->dayOfWeekIso - 1); // 0=Lun … 6=Dim

        // ── Absentéisme 6 derniers mois (chart) ───────────────────────
        $absenteisme6Mois = [];
        $labelsAbsences   = [];
        for ($i = 5; $i >= 0; $i--) {
            $month             = now()->subMonths($i);
            $labelsAbsences[]  = $month->isoFormat('MMM');
            $absenteisme6Mois[] = Absence::whereHas('demande', function ($q) use ($serviceId) {
                $q->whereHas('agent', fn ($q2) => $q2->where('id_service', $serviceId));
            })->whereMonth('date_absence', $month->month)
              ->whereYear('date_absence', $month->year)
              ->count();
        }

        // ── Répartition types postes ce mois (chart) ──────────────────
        $repartitionRaw = LignePlanning::whereHas('planning', fn ($q) => $q
                ->where('id_service', $serviceId)->where('statut_planning', 'Validé'))
            ->whereMonth('date_poste', now()->month)
            ->whereYear('date_poste', now()->year)
            ->with('typePoste')
            ->get()
            ->groupBy(fn ($l) => $l->typePoste->libelle ?? 'Autre')
            ->map->count();

        $postesLabels = $repartitionRaw->keys()->values()->toArray();
        $postesData   = $repartitionRaw->values()->toArray();

        return view('manager.dashboard', compact(
            'service', 'totalAgents', 'agentsEnConge', 'absencesToday', 'agentsPresents',
            'congesEnAttenteCount', 'congesEnAttente', 'lignesSemaine',
            'absenteisme6Mois', 'labelsAbsences', 'postesLabels', 'postesData'
        ));
    }
}
