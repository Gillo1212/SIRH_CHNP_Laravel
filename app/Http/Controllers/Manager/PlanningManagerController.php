<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\StorePlanningRequest;
use App\Models\Agent;
use App\Models\LignePlanning;
use App\Models\Planning;
use App\Models\Service;
use App\Models\TypePoste;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * PlanningManagerController
 * Gestion complète des plannings du service du manager.
 * Isolation stricte : seulement les agents du service assigné.
 */
class PlanningManagerController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────
    // HELPERS PRIVÉS
    // ─────────────────────────────────────────────────────────────────────

    private function getManagerService(): Service
    {
        $service = Service::where('id_agent_manager', auth()->id())->first();
        if (!$service) {
            abort(403, 'Aucun service assigné. Contactez l\'administration.');
        }
        return $service;
    }

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
                $color     = $this->posteColor($ligne->typePoste->libelle);
                $dateStr   = $ligne->date_poste->format('Y-m-d');
                $heureDebut = $ligne->heure_debut instanceof \Carbon\Carbon
                    ? $ligne->heure_debut->format('H:i')
                    : substr((string) $ligne->heure_debut, 0, 5);
                $heureFin   = $ligne->heure_fin instanceof \Carbon\Carbon
                    ? $ligne->heure_fin->format('H:i')
                    : substr((string) $ligne->heure_fin, 0, 5);

                // Gestion des postes de nuit (fin < début → lendemain)
                $endDate = $dateStr;
                if ($heureFin < $heureDebut) {
                    $endDate = $ligne->date_poste->addDay()->format('Y-m-d');
                }

                $events[] = [
                    'id'    => 'ligne-' . $ligne->id_ligne,
                    'title' => $ligne->agent->prenom . ' ' . substr($ligne->agent->nom, 0, 1) . '. — ' . $ligne->typePoste->libelle,
                    'start' => $dateStr . 'T' . $heureDebut,
                    'end'   => $endDate . 'T' . $heureFin,
                    'backgroundColor' => $color,
                    'borderColor'     => $color,
                    'extendedProps'   => [
                        'agent'      => $ligne->agent->nom_complet,
                        'typePoste'  => $ligne->typePoste->libelle,
                        'heureDebut' => $heureDebut,
                        'heureFin'   => $heureFin,
                        'statut'     => $planning->statut_planning,
                        'planningId' => $planning->id_planning,
                    ],
                ];
            }
        }
        return $events;
    }

    // ─────────────────────────────────────────────────────────────────────
    // CRUD
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Créer — redirige vers index (la création se fait via modal)
     */
    public function create()
    {
        return redirect()->route('manager.planning.index');
    }

    /**
     * Ajouter une ligne à un planning existant
     */
    public function addLigne(Request $request, int $id)
    {
        $service  = $this->getManagerService();
        $planning = Planning::where('id_service', $service->id_service)->findOrFail($id);

        if (!$planning->est_modifiable) {
            return back()->with('error', 'Ce planning ne peut pas être modifié (statut : ' . $planning->statut_planning . ').');
        }

        $request->validate([
            'id_agent'     => 'required|exists:agents,id_agent',
            'id_typeposte' => 'required|exists:type_postes,id_typeposte',
            'date_poste'   => [
                'required', 'date',
                'after_or_equal:'  . $planning->periode_debut->format('Y-m-d'),
                'before_or_equal:' . $planning->periode_fin->format('Y-m-d'),
            ],
            'heure_debut'  => 'required|date_format:H:i',
            'heure_fin'    => 'required|date_format:H:i',
        ], [
            'date_poste.after_or_equal'  => 'La date doit être dans la période du planning (' . $planning->periode_debut->format('d/m/Y') . ' → ' . $planning->periode_fin->format('d/m/Y') . ').',
            'date_poste.before_or_equal' => 'La date doit être dans la période du planning (' . $planning->periode_debut->format('d/m/Y') . ' → ' . $planning->periode_fin->format('d/m/Y') . ').',
        ]);

        // Sécurité : l'agent doit appartenir au service du manager
        $agent = Agent::where('id_agent', $request->id_agent)
            ->where('id_service', $service->id_service)
            ->first();

        if (!$agent) {
            return back()->with('error', 'Cet agent n\'appartient pas à votre service.');
        }

        LignePlanning::create([
            'id_planning'  => $planning->id_planning,
            'id_agent'     => $agent->id_agent,
            'id_typeposte' => $request->id_typeposte,
            'date_poste'   => $request->date_poste,
            'heure_debut'  => $request->heure_debut,
            'heure_fin'    => $request->heure_fin,
        ]);

        activity()->causedBy(auth()->user())->performedOn($planning)
            ->withProperties(['agent' => $agent->nom_complet, 'date' => $request->date_poste])
            ->log('Ligne ajoutée au planning');

        return back()->with('success', 'Ligne ajoutée avec succès.');
    }

    /**
     * Supprimer une ligne d'un planning
     */
    public function removeLigne(int $id, int $ligneId)
    {
        $service  = $this->getManagerService();
        $planning = Planning::where('id_service', $service->id_service)->findOrFail($id);

        if (!$planning->est_modifiable) {
            return back()->with('error', 'Ce planning ne peut plus être modifié.');
        }

        LignePlanning::where('id_planning', $planning->id_planning)
            ->where('id_ligne', $ligneId)
            ->delete();

        return back()->with('success', 'Ligne supprimée.');
    }

    /**
     * Liste + calendrier des plannings du service
     */
    public function index()
    {
        $service = $this->getManagerService();

        $plannings = Planning::with(['lignes' => fn($q) => $q->withCount([])])
            ->withCount('lignes')
            ->where('id_service', $service->id_service)
            ->orderByDesc('periode_debut')
            ->paginate(12);

        $stats = [
            'brouillons' => Planning::where('id_service', $service->id_service)->where('statut_planning', 'Brouillon')->count(),
            'transmis'   => Planning::where('id_service', $service->id_service)->where('statut_planning', 'Transmis')->count(),
            'valides'    => Planning::where('id_service', $service->id_service)->where('statut_planning', 'Validé')->count(),
            'rejetes'    => Planning::where('id_service', $service->id_service)->where('statut_planning', 'Rejeté')->count(),
        ];

        // Données calendrier (toutes les lignes du service)
        $allPlannings   = Planning::with(['lignes.agent', 'lignes.typePoste'])
            ->where('id_service', $service->id_service)
            ->get();
        $calendarEvents = $this->buildCalendarEvents($allPlannings);

        // Données pour le modal de création
        $agents     = Agent::where('id_service', $service->id_service)
            ->where('statut_agent', 'Actif')
            ->orderBy('nom')
            ->get(['id_agent', 'nom', 'prenom', 'famille_d_emploi']);
        $typesPoste = TypePoste::orderBy('libelle')
            ->get(['id_typeposte', 'libelle', 'description']);

        return view('manager.plannings.index', compact(
            'plannings', 'service', 'stats', 'calendarEvents', 'agents', 'typesPoste'
        ));
    }

    /**
     * Créer un planning — POST depuis le modal
     */
    public function store(StorePlanningRequest $request)
    {
        $service = $this->getManagerService();

        DB::transaction(function () use ($request, $service) {
            $planning = Planning::create([
                'id_service'      => $service->id_service,
                'periode_debut'   => $request->periode_debut,
                'periode_fin'     => $request->periode_fin,
                'statut_planning' => 'Brouillon',
                'date_creation'   => now(),
            ]);

            foreach ($request->lignes ?? [] as $ligne) {
                $agent = Agent::where('id_agent', $ligne['id_agent'])
                    ->where('id_service', $service->id_service)
                    ->first();
                if ($agent) {
                    LignePlanning::create([
                        'id_planning'  => $planning->id_planning,
                        'id_agent'     => $agent->id_agent,
                        'id_typeposte' => $ligne['id_typeposte'],
                        'date_poste'   => $ligne['date_poste'],
                        'heure_debut'  => $ligne['heure_debut'],
                        'heure_fin'    => $ligne['heure_fin'],
                    ]);
                }
            }

            activity()
                ->causedBy(auth()->user())
                ->performedOn($planning)
                ->withProperties([
                    'service' => $service->nom_service,
                    'periode' => $request->periode_debut . ' → ' . $request->periode_fin,
                    'lignes'  => count($request->lignes ?? []),
                ])
                ->log('Planning créé en brouillon');
        });

        return redirect()->route('manager.planning.index')
            ->with('success', 'Planning créé en brouillon avec succès. Ajoutez des lignes puis transmettez-le à la RH.');
    }

    /**
     * Détail d'un planning + calendrier
     */
    public function show(int $id)
    {
        $service  = $this->getManagerService();
        $planning = Planning::with(['service', 'lignes' => fn($q) => $q->with(['agent', 'typePoste'])->orderBy('date_poste')->orderBy('id_agent')])
            ->where('id_service', $service->id_service)
            ->findOrFail($id);

        $lignesParDate  = $planning->lignes->groupBy(fn($l) => $l->date_poste->format('Y-m-d'));
        $calendarEvents = $this->buildCalendarEvents(collect([$planning]));

        // Données pour le modal d'édition
        $agents     = Agent::where('id_service', $service->id_service)
            ->where('statut_agent', 'Actif')
            ->orderBy('nom')
            ->get(['id_agent', 'nom', 'prenom', 'famille_d_emploi']);
        $typesPoste = TypePoste::orderBy('libelle')
            ->get(['id_typeposte', 'libelle']);

        // Stats rapides du planning
        $statsPoste = $planning->lignes->groupBy(fn($l) => $l->typePoste->libelle ?? 'Autre')
            ->map->count();

        return view('manager.plannings.show', compact(
            'planning', 'service', 'lignesParDate', 'calendarEvents', 'agents', 'typesPoste', 'statsPoste'
        ));
    }

    /**
     * Mettre à jour un planning (modal édition)
     */
    public function update(Request $request, int $id)
    {
        $service  = $this->getManagerService();
        $planning = Planning::where('id_service', $service->id_service)->findOrFail($id);

        if (!$planning->est_modifiable) {
            return back()->with('error', 'Ce planning ne peut pas être modifié (statut : ' . $planning->statut_planning . ').');
        }

        $request->validate([
            'periode_debut'         => 'required|date',
            'periode_fin'           => 'required|date|after_or_equal:periode_debut',
            'lignes'                => 'nullable|array',
            'lignes.*.id_agent'     => 'required|exists:agents,id_agent',
            'lignes.*.id_typeposte' => 'required|exists:type_postes,id_typeposte',
            'lignes.*.date_poste'   => 'required|date',
            'lignes.*.heure_debut'  => 'required|date_format:H:i',
            'lignes.*.heure_fin'    => 'required|date_format:H:i',
        ]);

        DB::transaction(function () use ($request, $planning, $service) {
            $planning->update([
                'periode_debut' => $request->periode_debut,
                'periode_fin'   => $request->periode_fin,
            ]);

            // Recréer toutes les lignes
            $planning->lignes()->delete();

            foreach ($request->lignes ?? [] as $ligne) {
                $agent = Agent::where('id_agent', $ligne['id_agent'])
                    ->where('id_service', $service->id_service)
                    ->first();
                if ($agent) {
                    LignePlanning::create([
                        'id_planning'  => $planning->id_planning,
                        'id_agent'     => $agent->id_agent,
                        'id_typeposte' => $ligne['id_typeposte'],
                        'date_poste'   => $ligne['date_poste'],
                        'heure_debut'  => $ligne['heure_debut'],
                        'heure_fin'    => $ligne['heure_fin'],
                    ]);
                }
            }

            activity()
                ->causedBy(auth()->user())
                ->performedOn($planning)
                ->withProperties(['lignes' => count($request->lignes ?? [])])
                ->log('Planning mis à jour');
        });

        return back()->with('success', 'Planning mis à jour avec succès.');
    }

    /**
     * Supprimer un planning (brouillon uniquement)
     */
    public function destroy(int $id)
    {
        $service  = $this->getManagerService();
        $planning = Planning::where('id_service', $service->id_service)->findOrFail($id);

        if (!$planning->est_brouillon) {
            return back()->with('error', 'Seuls les plannings en brouillon peuvent être supprimés.');
        }

        DB::transaction(function () use ($planning) {
            $pid = $planning->id_planning;
            $planning->lignes()->delete();
            $planning->delete();
            activity()->causedBy(auth()->user())->log('Planning #' . $pid . ' supprimé');
        });

        return redirect()->route('manager.planning.index')
            ->with('success', 'Planning supprimé.');
    }

    /**
     * Transmettre le planning à la RH
     */
    public function transmettre(Request $request, int $id)
    {
        $service  = $this->getManagerService();
        $planning = Planning::where('id_service', $service->id_service)->findOrFail($id);

        if (!$planning->est_modifiable) {
            return back()->with('error', 'Ce planning ne peut pas être transmis (statut : ' . $planning->statut_planning . ').');
        }

        if ($planning->lignes()->count() === 0) {
            return back()->with('error', 'Impossible de transmettre un planning vide. Ajoutez des lignes d\'abord.');
        }

        DB::transaction(function () use ($planning) {
            $planning->transmettre();
            activity()->causedBy(auth()->user())->performedOn($planning)
                ->log('Planning transmis à la RH');
        });

        return back()->with('success', 'Planning transmis au service RH pour validation.');
    }
}
