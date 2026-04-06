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
        $query = Planning::with(['service'])->withCount('lignes')
            ->where('statut_planning', 'Diffusé');

        if ($request->filled('service_id')) {
            $query->where('id_service', $request->service_id);
        }

        $plannings = $query->orderByDesc('periode_debut')->paginate(15)->withQueryString();

        $stats = [
            'total'             => Planning::where('statut_planning', 'Diffusé')->count(),
            'ce_mois'           => Planning::where('statut_planning', 'Diffusé')
                                       ->whereMonth('periode_debut', now()->month)
                                       ->whereYear('periode_debut', now()->year)->count(),
            'services_couverts' => Planning::where('statut_planning', 'Diffusé')
                                       ->distinct('id_service')->count('id_service'),
            'valides'           => Planning::where('statut_planning', 'Diffusé')->count(),
        ];

        // Calendrier : uniquement les plannings diffusés au RH
        $allValidated   = Planning::with(['lignes.agent', 'lignes.typePoste', 'service'])
            ->where('statut_planning', 'Diffusé')
            ->get();
        $calendarEvents = $this->buildCalendarEvents($allValidated);

        $services = Service::orderBy('nom_service')->get(['id_service', 'nom_service']);

        return view('rh.plannings.index', compact('plannings', 'stats', 'calendarEvents', 'services'));
    }

    /**
     * Plannings diffusés au RH à titre informatif (transmis par les Managers après validation)
     */
    public function pending()
    {
        $plannings = Planning::with(['service'])
            ->withCount('lignes')
            ->where('statut_planning', 'Diffusé')
            ->orderByDesc('updated_at')
            ->paginate(20);

        $count = Planning::where('statut_planning', 'Diffusé')->count();

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

}
