<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\LignePlanning;
use Carbon\Carbon;

class PlanningAgentController extends Controller
{
    public function index()
    {
        $agent = auth()->user()->agent;

        if (!$agent) {
            return redirect()->route('agent.dashboard')
                ->with('error', 'Profil agent introuvable. Contactez la RH.');
        }

        // Lignes de planning validées pour cet agent
        $lignes = LignePlanning::with(['planning.service', 'typePoste'])
            ->where('id_agent', $agent->id_agent)
            ->whereHas('planning', fn($q) => $q->where('statut_planning', 'Validé'))
            ->orderBy('date_poste')
            ->get();

        // Couleurs par type de poste
        $colorMap = [
            'Jour'       => '#3B82F6',
            'Nuit'       => '#4F46E5',
            'Garde'      => '#F59E0B',
            'Repos'      => '#9CA3AF',
            'Astreinte'  => '#8B5CF6',
            'Permanence' => '#0D9488',
        ];

        // Événements FullCalendar
        $calendarEvents = [];
        foreach ($lignes as $ligne) {
            if (!$ligne->typePoste) {
                continue;
            }
            $libelle    = $ligne->typePoste->libelle;
            $color      = $colorMap[$libelle] ?? '#6B7280';
            $dateStr    = $ligne->date_poste->format('Y-m-d');
            $heureDebut = $ligne->heure_debut instanceof Carbon
                ? $ligne->heure_debut->format('H:i')
                : substr((string) $ligne->heure_debut, 0, 5);
            $heureFin   = $ligne->heure_fin instanceof Carbon
                ? $ligne->heure_fin->format('H:i')
                : substr((string) $ligne->heure_fin, 0, 5);
            $endDate    = ($heureFin < $heureDebut)
                ? $ligne->date_poste->copy()->addDay()->format('Y-m-d')
                : $dateStr;

            $calendarEvents[] = [
                'title'           => $libelle,
                'start'           => $dateStr . 'T' . $heureDebut,
                'end'             => $endDate . 'T' . $heureFin,
                'backgroundColor' => $color,
                'borderColor'     => $color,
                'extendedProps'   => [
                    'typePoste'  => $libelle,
                    'heureDebut' => $heureDebut,
                    'heureFin'   => $heureFin,
                    'service'    => $ligne->planning->service->nom_service ?? '',
                    'nbHeures'   => $ligne->nb_heures ?? 0,
                ],
            ];
        }

        // Statistiques
        $stats = [
            'ce_mois'       => $lignes->filter(fn($l) => $l->date_poste->isCurrentMonth())->count(),
            'cette_semaine' => $lignes->filter(fn($l) => $l->date_poste->isCurrentWeek())->count(),
            'total'         => $lignes->count(),
        ];

        // 5 prochains postes
        $prochains = $lignes->filter(fn($l) => $l->date_poste->gte(today()))->take(5);

        return view('agent.plannings.index', compact('calendarEvents', 'stats', 'prochains', 'agent'));
    }
}
