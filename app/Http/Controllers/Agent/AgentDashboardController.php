<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Demande;
use App\Models\LignePlanning;
use App\Models\SoldeConge;

class AgentDashboardController extends Controller
{
    public function index()
    {
        $user  = auth()->user();
        $agent = $user->agent;

        if (!$agent) {
            return view('agent.dashboard', ['stats' => [], 'noAgent' => true]);
        }

        // ── Soldes congés année courante ──────────────────────────────
        $soldesConges = SoldeConge::where('id_agent', $agent->id_agent)
            ->where('annee', date('Y'))
            ->with('typeConge')
            ->get();

        // ── Demandes récentes (congés + absences) ─────────────────────
        $mesDemandes = Demande::where('id_agent', $agent->id_agent)
            ->with(['conge.typeConge', 'absence'])
            ->latest()
            ->take(6)
            ->get();

        // ── Compteurs demandes ─────────────────────────────────────────
        $demandesEnAttente = Demande::where('id_agent', $agent->id_agent)
            ->where('statut_demande', 'En_attente')
            ->count();

        $demandesApprouvees = Demande::where('id_agent', $agent->id_agent)
            ->where('statut_demande', 'Approuvé')
            ->count();

        // ── Planning cette semaine (plannings validés) ─────────────────
        $planningCetteSemaine = LignePlanning::with(['typePoste', 'planning'])
            ->where('id_agent', $agent->id_agent)
            ->whereHas('planning', fn($q) => $q->where('statut_planning', 'Validé'))
            ->whereBetween('date_poste', [
                now()->startOfWeek()->toDateString(),
                now()->endOfWeek()->toDateString(),
            ])
            ->orderBy('date_poste')
            ->get()
            ->keyBy(fn($l) => $l->date_poste->dayOfWeekIso - 1); // 0=Lun…6=Dim

        // ── Prochain poste ─────────────────────────────────────────────
        $prochainPoste = LignePlanning::with(['typePoste'])
            ->where('id_agent', $agent->id_agent)
            ->whereHas('planning', fn($q) => $q->where('statut_planning', 'Validé'))
            ->where('date_poste', '>', today())
            ->orderBy('date_poste')
            ->first();

        // ── Absences ce mois ───────────────────────────────────────────
        $absencesCeMois = Absence::whereHas('demande', fn($q) => $q->where('id_agent', $agent->id_agent))
            ->whereMonth('date_absence', now()->month)
            ->whereYear('date_absence', now()->year)
            ->count();

        // ── Contrat actif ──────────────────────────────────────────────
        $contratActif = $agent->contratActif;

        return view('agent.dashboard', [
            'agent'               => $agent,
            'soldesConges'        => $soldesConges,
            'mesDemandes'         => $mesDemandes,
            'demandesEnAttente'   => $demandesEnAttente,
            'demandesApprouvees'  => $demandesApprouvees,
            'planningCetteSemaine'=> $planningCetteSemaine,
            'prochainPoste'       => $prochainPoste,
            'absencesCeMois'      => $absencesCeMois,
            'contratActif'        => $contratActif,
        ]);
    }
}
