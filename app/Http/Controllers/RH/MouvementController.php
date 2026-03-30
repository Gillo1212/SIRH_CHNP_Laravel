<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Http\Requests\RH\StoreMouvementRequest;
use App\Http\Requests\RH\UpdateMouvementRequest;
use App\Models\Agent;
use App\Models\Mouvement;
use App\Models\Service;
use App\Models\User;
use App\Notifications\MouvementSoumisNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MouvementController extends Controller
{
    // ──────────────────────────────────────────────────
    // LISTE PRINCIPALE
    // ──────────────────────────────────────────────────
    public function index(Request $request)
    {
        $this->authorize('viewAny', Mouvement::class);

        $query = Mouvement::with(['agent.service', 'serviceDestination', 'serviceOrigine', 'createur'])
            ->orderByDesc('date_mouvement');
        $this->applyFilters($query, $request);

        $mouvements  = $query->paginate(15)->withQueryString();
        $stats       = $this->getStats();
        $services    = Service::orderBy('nom_service')->get();
        $agents      = Agent::whereIn('statut_agent', ['Actif', 'En_congé', 'Suspendu'])->orderBy('nom')->get();
        $filtreActif = null;

        return view('rh.mouvements.index', compact('mouvements', 'stats', 'services', 'agents', 'filtreActif'));
    }

    // ──────────────────────────────────────────────────
    // VUES FILTRÉES PAR TYPE
    // ──────────────────────────────────────────────────
    public function affectations(Request $request) { return $this->indexFiltre($request, 'Affectation initiale'); }
    public function mutations(Request $request)    { return $this->indexFiltre($request, 'Mutation'); }
    public function retours(Request $request)      { return $this->indexFiltre($request, 'Retour'); }
    public function departs(Request $request)      { return $this->indexFiltre($request, 'Départ'); }

    private function indexFiltre(Request $request, string $type)
    {
        $this->authorize('viewAny', Mouvement::class);

        $query = Mouvement::with(['agent.service', 'serviceDestination', 'serviceOrigine', 'createur'])
            ->parType($type)->orderByDesc('date_mouvement');
        $this->applyFilters($query, $request, skip: 'type_mouvement');

        $mouvements  = $query->paginate(15)->withQueryString();
        $stats       = $this->getStats();
        $services    = Service::orderBy('nom_service')->get();
        $agents      = Agent::whereIn('statut_agent', ['Actif', 'En_congé', 'Suspendu'])->orderBy('nom')->get();
        $filtreActif = $type;

        return view('rh.mouvements.index', compact('mouvements', 'stats', 'services', 'agents', 'filtreActif'));
    }

    // ──────────────────────────────────────────────────
    // CRÉATION
    // ──────────────────────────────────────────────────
    public function create()
    {
        $this->authorize('create', Mouvement::class);
        return redirect()->route('rh.mouvements.index');
    }

    public function store(StoreMouvementRequest $request)
    {
        $this->authorize('create', Mouvement::class);

        DB::transaction(function () use ($request) {
            $data             = $request->validated();
            $data['cree_par'] = auth()->id();
            $data['statut']   = 'en_attente';
            if ($data['type_mouvement'] === 'Départ') {
                $data['id_service'] = null;
            }

            $mouvement = Mouvement::create($data);

            activity()
                ->causedBy(auth()->user())
                ->on($mouvement)
                ->withProperties(['type' => $data['type_mouvement'], 'agent_id' => $data['id_agent']])
                ->log("Création mouvement {$data['type_mouvement']} pour agent #{$data['id_agent']}");
        });

        // Notifier le DRH (hors transaction)
        try {
            $mouvement = Mouvement::with('agent')->latest()->first();
            if ($mouvement) {
                User::role('DRH')->each(
                    fn(User $drh) => $drh->notify(new MouvementSoumisNotification($mouvement))
                );
            }
        } catch (\Throwable $e) {
            \Log::warning('Notification mouvement DRH échouée : ' . $e->getMessage());
        }

        return redirect()->route('rh.mouvements.index')
            ->with('success', 'Mouvement enregistré. Il est en attente de traitement.');
    }

    // ──────────────────────────────────────────────────
    // CONSULTATION JSON (modal AJAX)
    // ──────────────────────────────────────────────────
    public function show(Request $request, $id)
    {
        $m = Mouvement::with(['agent.service', 'serviceDestination', 'serviceOrigine', 'createur', 'validateur'])
            ->findOrFail($id);

        $this->authorize('view', $m);

        if ($request->expectsJson()) {
            $type   = $m->couleur_type;
            $statut = $m->couleur_statut;
            return response()->json([
                'id_mouvement'       => $m->id_mouvement,
                'id_agent'           => $m->id_agent,
                'type_mouvement'     => $m->type_mouvement,
                'type_label'         => $type['label'],
                'type_color'         => $type['color'],
                'type_bg'            => $type['bg'],
                'type_icon'          => $type['icon'],
                'statut'             => $m->statut,
                'statut_label'       => $statut['label'],
                'statut_color'       => $statut['color'],
                'statut_bg'          => $statut['bg'],
                'date_mouvement'     => $m->date_mouvement?->format('Y-m-d'),
                'date_mouvement_fr'  => $m->date_mouvement?->isoFormat('DD MMMM YYYY'),
                'motif'              => $m->motif,
                'date_validation_fr' => $m->date_validation?->isoFormat('DD/MM/YYYY HH:mm'),
                'est_modifiable'     => $m->est_modifiable,
                'est_annulable'      => $m->est_annulable,
                'est_effectuable'    => $m->est_effectuable,
                'service_destination'=> $m->serviceDestination?->nom_service,
                'service_origine'    => $m->serviceOrigine?->nom_service,
                'id_service'         => $m->id_service,
                'id_service_origine' => $m->id_service_origine,
                'agent' => [
                    'nom_complet' => $m->agent->nom_complet,
                    'matricule'   => $m->agent->matricule,
                    'famille_d_emploi' => $m->agent->famille_d_emploi ? str_replace('_', ' ', $m->agent->famille_d_emploi) : '—',
                    'service'          => $m->agent->service?->nom_service ?? '—',
                    'statut_contrat'   => $m->agent->statut_contrat,
                ],
                'cree_par_nom'   => $m->createur?->name ?? '—',
                'valide_par_nom' => $m->validateur?->name ?? '—',
            ]);
        }

        return redirect()->route('rh.mouvements.index');
    }

    // ──────────────────────────────────────────────────
    // MODIFICATION
    // ──────────────────────────────────────────────────
    public function edit($id)
    {
        $mouvement = Mouvement::findOrFail($id);
        $this->authorize('update', $mouvement);
        return redirect()->route('rh.mouvements.index');
    }

    public function update(UpdateMouvementRequest $request, $id)
    {
        $mouvement = Mouvement::findOrFail($id);
        $this->authorize('update', $mouvement);

        DB::transaction(function () use ($request, $mouvement) {
            $data = $request->validated();
            if ($data['type_mouvement'] === 'Départ') {
                $data['id_service'] = null;
            }
            $mouvement->update($data);

            activity()
                ->causedBy(auth()->user())
                ->on($mouvement)
                ->log("Modification mouvement #{$mouvement->id_mouvement}");
        });

        return redirect()->route('rh.mouvements.index')
            ->with('success', 'Mouvement mis à jour avec succès.');
    }

    // ──────────────────────────────────────────────────
    // MARQUER EFFECTUÉ
    // ──────────────────────────────────────────────────
    public function effectuer($id)
    {
        $mouvement = Mouvement::with('agent')->findOrFail($id);
        $this->authorize('effectuer', $mouvement);

        DB::transaction(function () use ($mouvement) {
            $mouvement->update(['statut' => 'effectue']);
            $agent = $mouvement->agent;

            match ($mouvement->type_mouvement) {
                'Affectation initiale', 'Mutation', 'Retour' => $mouvement->id_service
                    ? $agent->update(['id_service' => $mouvement->id_service, 'statut_agent' => 'Actif'])
                    : null,
                'Départ' => $agent->update(['statut_agent' => 'Retraité']),
                default  => null,
            };

            activity()
                ->causedBy(auth()->user())
                ->on($mouvement)
                ->log("Mouvement #{$mouvement->id_mouvement} effectué — agent {$agent->nom_complet}");
        });

        return redirect()->back()
            ->with('success', "Mouvement effectué. Le dossier de l'agent a été mis à jour.");
    }

    // ──────────────────────────────────────────────────
    // ANNULER
    // ──────────────────────────────────────────────────
    public function annuler(Request $request, $id)
    {
        $mouvement = Mouvement::findOrFail($id);
        $this->authorize('annuler', $mouvement);

        DB::transaction(function () use ($mouvement) {
            $mouvement->update(['statut' => 'annule']);
            activity()
                ->causedBy(auth()->user())
                ->on($mouvement)
                ->log("Annulation mouvement #{$mouvement->id_mouvement}");
        });

        return redirect()->back()->with('success', 'Mouvement annulé.');
    }

    // ──────────────────────────────────────────────────
    // EXPORT EXCEL
    // ──────────────────────────────────────────────────
    public function export(Request $request)
    {
        $this->authorize('viewAny', Mouvement::class);

        $query = Mouvement::with(['agent.service', 'serviceDestination', 'serviceOrigine'])
            ->orderByDesc('date_mouvement');
        $this->applyFilters($query, $request);

        $mouvements = $query->get();

        $export = new \App\Exports\ExcelExport('Mouvements CHNP');
        $export->setHeaders([
            'Matricule', 'Agent', 'Type', 'Statut',
            'Service origine', 'Service destination',
            'Date mouvement', 'Motif',
        ]);

        foreach ($mouvements as $m) {
            $export->addRow([
                $m->agent?->matricule ?? '—',
                $m->agent?->nom_complet ?? '—',
                $m->type_mouvement,
                $m->statut,
                $m->serviceOrigine?->nom_service ?? '—',
                $m->serviceDestination?->nom_service ?? '—',
                $m->date_mouvement?->format('d/m/Y') ?? '—',
                $m->motif ?? '',
            ]);
        }

        return $export->download('mouvements_chnp_' . now()->format('Y-m-d'));
    }

    // ──────────────────────────────────────────────────
    // HELPERS
    // ──────────────────────────────────────────────────
    private function applyFilters($query, Request $request, string $skip = ''): void
    {
        if ($s = $request->search) {
            $query->whereHas('agent', fn($q) => $q
                ->where('nom', 'like', "%{$s}%")
                ->orWhere('prenom', 'like', "%{$s}%")
                ->orWhere('matricule', 'like', "%{$s}%"));
        }
        if ($skip !== 'type_mouvement' && $t = $request->type_mouvement) {
            $query->where('type_mouvement', $t);
        }
        if ($st = $request->statut) {
            $query->where('statut', $st);
        }
        if ($sv = $request->service) {
            $query->where(fn($q) => $q->where('id_service', $sv)->orWhere('id_service_origine', $sv));
        }
        if ($from = $request->date_from) {
            $query->where('date_mouvement', '>=', $from);
        }
        if ($to = $request->date_to) {
            $query->where('date_mouvement', '<=', $to);
        }
    }

    private function getStats(): array
    {
        return [
            'total'        => Mouvement::count(),
            'affectations' => Mouvement::parType('Affectation initiale')->count(),
            'mutations'    => Mouvement::parType('Mutation')->count(),
            'retours'      => Mouvement::parType('Retour')->count(),
            'departs'      => Mouvement::parType('Départ')->count(),
            'en_attente'   => Mouvement::enAttente()->count(),
            'valide_drh'   => Mouvement::valideDRH()->count(),
            'effectue'     => Mouvement::effectue()->count(),
            'ce_mois'      => Mouvement::whereMonth('date_mouvement', now()->month)
                                       ->whereYear('date_mouvement', now()->year)->count(),
        ];
    }
}
