<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Models\Planning;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanningRHController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────────

    private function posteColor(string $libelle): string
    {
        return match ($libelle) {
            'Jour'       => '#3B82F6',
            'Nuit'       => '#4F46E5',
            'Garde'      => '#F59E0B',
            'Repos'      => '#9CA3AF',
            'Astreinte'  => '#8B5CF6',
            'Permanence' => '#0D9488',
            default      => '#6B7280',
        };
    }

    private function buildCalendarEvents($plannings): array
    {
        $events = [];
        foreach ($plannings as $planning) {
            foreach ($planning->lignes as $ligne) {
                if (!$ligne->agent || !$ligne->typePoste) {
                    continue;
                }
                $color      = $this->posteColor($ligne->typePoste->libelle);
                $dateStr    = $ligne->date_poste->format('Y-m-d');
                $heureDebut = $ligne->heure_debut instanceof \Carbon\Carbon
                    ? $ligne->heure_debut->format('H:i')
                    : substr((string) $ligne->heure_debut, 0, 5);
                $heureFin   = $ligne->heure_fin instanceof \Carbon\Carbon
                    ? $ligne->heure_fin->format('H:i')
                    : substr((string) $ligne->heure_fin, 0, 5);
                $endDate    = ($heureFin < $heureDebut)
                    ? $ligne->date_poste->copy()->addDay()->format('Y-m-d')
                    : $dateStr;

                $events[] = [
                    'id'              => 'rh-' . $ligne->id_ligne,
                    'title'           => $ligne->agent->prenom . ' ' . substr($ligne->agent->nom, 0, 1) . '. — ' . $ligne->typePoste->libelle,
                    'start'           => $dateStr . 'T' . $heureDebut,
                    'end'             => $endDate . 'T' . $heureFin,
                    'backgroundColor' => $color,
                    'borderColor'     => $color,
                    'extendedProps'   => [
                        'agent'      => $ligne->agent->nom_complet,
                        'typePoste'  => $ligne->typePoste->libelle,
                        'heureDebut' => $heureDebut,
                        'heureFin'   => $heureFin,
                        'service'    => $planning->service->nom_service ?? '',
                        'statut'     => $planning->statut_planning,
                        'planningId' => $planning->id_planning,
                    ],
                ];
            }
        }
        return $events;
    }

    // ─────────────────────────────────────────────────────────────────────
    // ACTIONS
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Tous les plannings, avec filtres et calendrier
     */
    public function index(Request $request)
    {
        $query = Planning::with(['service'])->withCount('lignes');

        if ($request->filled('service_id')) {
            $query->where('id_service', $request->service_id);
        }
        if ($request->filled('statut')) {
            $query->where('statut_planning', $request->statut);
        }

        $plannings = $query->orderByDesc('periode_debut')->paginate(15)->withQueryString();

        $stats = [
            'total'      => Planning::count(),
            'brouillons' => Planning::where('statut_planning', 'Brouillon')->count(),
            'transmis'   => Planning::where('statut_planning', 'Transmis')->count(),
            'valides'    => Planning::where('statut_planning', 'Validé')->count(),
            'rejetes'    => Planning::where('statut_planning', 'Rejeté')->count(),
        ];

        // Calendrier : plannings validés et transmis
        $allValidated   = Planning::with(['lignes.agent', 'lignes.typePoste', 'service'])
            ->whereIn('statut_planning', ['Validé', 'Transmis'])
            ->get();
        $calendarEvents = $this->buildCalendarEvents($allValidated);

        $services = Service::orderBy('nom_service')->get(['id_service', 'nom_service']);

        return view('rh.plannings.index', compact('plannings', 'stats', 'calendarEvents', 'services'));
    }

    /**
     * Plannings en attente de validation (statut = Transmis)
     */
    public function pending()
    {
        $plannings = Planning::with(['service'])
            ->withCount('lignes')
            ->where('statut_planning', 'Transmis')
            ->orderBy('periode_debut')
            ->paginate(20);

        $count = Planning::where('statut_planning', 'Transmis')->count();

        return view('rh.plannings.pending', compact('plannings', 'count'));
    }

    /**
     * Détail d'un planning
     */
    public function show(int $id)
    {
        $planning = Planning::with([
            'service',
            'lignes' => fn($q) => $q->with(['agent', 'typePoste'])->orderBy('date_poste')->orderBy('id_agent'),
        ])->findOrFail($id);

        $lignesParDate  = $planning->lignes->groupBy(fn($l) => $l->date_poste->format('Y-m-d'));
        $calendarEvents = $this->buildCalendarEvents(collect([$planning]));
        $statsPoste     = $planning->lignes->groupBy(fn($l) => $l->typePoste->libelle ?? 'Autre')->map->count();
        $agentsUniques  = $planning->lignes->pluck('id_agent')->unique()->count();

        return view('rh.plannings.show', compact(
            'planning', 'lignesParDate', 'calendarEvents', 'statsPoste', 'agentsUniques'
        ));
    }

    /**
     * Valider un planning (POST)
     */
    public function valider(int $id)
    {
        $planning = Planning::where('statut_planning', 'Transmis')->findOrFail($id);

        DB::transaction(function () use ($planning) {
            $planning->valider();
            activity()->causedBy(auth()->user())
                ->performedOn($planning)
                ->withProperties(['service' => $planning->service->nom_service ?? ''])
                ->log('Planning validé par RH');
        });

        return back()->with('success', 'Planning validé avec succès. Le manager a été notifié.');
    }

    /**
     * Rejeter un planning avec motif (POST)
     */
    public function rejeter(Request $request, int $id)
    {
        $planning = Planning::where('statut_planning', 'Transmis')->findOrFail($id);

        $request->validate([
            'motif_rejet' => 'required|string|min:10|max:500',
        ], [
            'motif_rejet.required' => 'Le motif de rejet est obligatoire.',
            'motif_rejet.min'      => 'Le motif doit contenir au moins 10 caractères.',
            'motif_rejet.max'      => 'Le motif ne peut pas dépasser 500 caractères.',
        ]);

        DB::transaction(function () use ($planning, $request) {
            $planning->rejeter($request->motif_rejet);
            activity()->causedBy(auth()->user())
                ->performedOn($planning)
                ->withProperties(['motif' => $request->motif_rejet])
                ->log('Planning rejeté par RH');
        });

        return back()->with('success', 'Planning rejeté. Le manager peut maintenant le corriger et le soumettre à nouveau.');
    }
}
