<?php

namespace App\Http\Controllers\Major;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Demande;
use App\Models\LignePlanning;
use App\Models\Service;

class MajorDashboardController extends Controller
{
    public function index()
    {
        $user    = auth()->user();
        $service = Service::where('id_agent_major', $user->id)->first();

        if (!$service) {
            return view('major.dashboard', ['noService' => true]);
        }

        $serviceId = $service->id_service;

        // Effectifs
        $totalAgents    = $service->agents()->actif()->count();
        $agentsEnConge  = $service->agents()->where('statut_agent', 'En_congé')->count();

        $absencesToday = Absence::whereHas('demande', function ($q) use ($serviceId) {
            $q->whereHas('agent', fn($q2) => $q2->where('id_service', $serviceId));
        })->whereDate('date_absence', today())->count();

        $agentsPresents = max(0, $totalAgents - $absencesToday - $agentsEnConge);

        // Plannings du service (en cours ce mois)
        $planningsEnCours = \App\Models\Planning::where('id_service', $serviceId)
            ->whereIn('statut_planning', ['Brouillon', 'Transmis'])
            ->count();

        $planningsValides = \App\Models\Planning::where('id_service', $serviceId)
            ->where('statut_planning', 'Validé')
            ->whereMonth('periode_debut', now()->month)
            ->count();

        // Absences du mois courant
        $absencesMois = Absence::whereHas('demande', function ($q) use ($serviceId) {
            $q->whereHas('agent', fn($q2) => $q2->where('id_service', $serviceId));
        })->whereMonth('date_absence', now()->month)
          ->whereYear('date_absence', now()->year)
          ->count();

        // Dernières absences (5)
        $dernieresAbsences = Absence::with(['demande.agent'])
            ->whereHas('demande', function ($q) use ($serviceId) {
                $q->whereHas('agent', fn($q2) => $q2->where('id_service', $serviceId));
            })
            ->orderByDesc('date_absence')
            ->take(5)
            ->get();

        // Planning semaine (lignes validées)
        $lignesSemaine = LignePlanning::with(['agent', 'typePoste'])
            ->whereHas('planning', fn($q) => $q
                ->where('id_service', $serviceId)
                ->where('statut_planning', 'Validé'))
            ->whereBetween('date_poste', [
                now()->startOfWeek()->toDateString(),
                now()->endOfWeek()->toDateString(),
            ])
            ->orderBy('date_poste')
            ->orderBy('heure_debut')
            ->get()
            ->groupBy(fn($l) => $l->date_poste->dayOfWeekIso - 1);

        // Absentéisme 6 derniers mois (chart)
        $absenteisme6Mois = [];
        $labelsAbsences   = [];
        for ($i = 5; $i >= 0; $i--) {
            $month             = now()->subMonths($i);
            $labelsAbsences[]  = $month->isoFormat('MMM');
            $absenteisme6Mois[] = Absence::whereHas('demande', function ($q) use ($serviceId) {
                $q->whereHas('agent', fn($q2) => $q2->where('id_service', $serviceId));
            })->whereMonth('date_absence', $month->month)
              ->whereYear('date_absence', $month->year)
              ->count();
        }

        return view('major.dashboard', compact(
            'service', 'totalAgents', 'agentsEnConge', 'absencesToday', 'agentsPresents',
            'planningsEnCours', 'planningsValides', 'absencesMois',
            'dernieresAbsences', 'lignesSemaine',
            'absenteisme6Mois', 'labelsAbsences'
        ));
    }
}
