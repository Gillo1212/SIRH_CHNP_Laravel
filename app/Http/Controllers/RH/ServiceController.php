<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Http\Requests\RH\StoreServiceRequest;
use App\Http\Requests\RH\UpdateServiceRequest;
use App\Models\Agent;
use App\Models\Division;
use App\Models\Service;
use App\Models\User;
use App\Services\ServiceStatisticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    public function __construct(private ServiceStatisticsService $statsService) {}

    /**
     * Liste de tous les services avec statistiques
     */
    public function index()
    {
        $this->authorize('viewAny', Service::class);

        $services = Service::with([
            'manager.agent',
            'major.agent',
            'agents' => fn($q) => $q->orderBy('nom')->select('id_agent', 'id_service', 'nom', 'prenom', 'matricule', 'fontion', 'statut_agent'),
        ])
            ->withCount(['agents', 'divisions'])
            ->orderBy('nom_service')
            ->get();

        $totaux = [
            'services'     => $services->count(),
            'agents'       => $services->sum('agents_count'),
            'avec_manager' => $services->whereNotNull('id_agent_manager')->count(),
            'sans_manager' => $services->whereNull('id_agent_manager')->count(),
        ];

        $managers  = User::role('Manager')->with('agent')->get();
        $majors    = User::role('Major')->with('agent')->get();
        $allAgents = Agent::orderBy('nom')
            ->select('id_agent', 'id_service', 'nom', 'prenom', 'matricule', 'fontion')
            ->get();

        return view('rh.services.index', compact('services', 'totaux', 'managers', 'majors', 'allAgents'));
    }

    /**
     * Création via modal dans index — redirection directe
     */
    public function create()
    {
        return redirect()->route('rh.services.index');
    }

    /**
     * Enregistrer un nouveau service (Intégrité CID : transaction + validation)
     */
    public function store(StoreServiceRequest $request)
    {
        $this->authorize('create', Service::class);

        DB::transaction(function () use ($request) {
            $service = Service::create($request->validated());

            activity()
                ->causedBy(auth()->user())
                ->performedOn($service)
                ->withProperties(['nom' => $service->nom_service])
                ->log('Service créé');
        });

        return redirect()->route('rh.services.index')
            ->with('success', "Le service \"{$request->nom_service}\" a été créé avec succès.");
    }

    /**
     * Détails d'un service avec statistiques et agents
     */
    public function show(int $id)
    {
        $service = Service::with(['divisions', 'manager.agent', 'agents' => function ($q) {
            $q->with('user')->orderBy('nom');
        }])->findOrFail($id);

        $this->authorize('view', $service);

        $stats           = $this->statsService->getServiceStats($service->id_service);
        $absencesByMonth = $this->statsService->getAbsencesByMonth($service->id_service);
        $managers        = User::role('Manager')->with('agent')->get();
        $majors          = User::role('Major')->with('agent')->get();

        return view('rh.services.show', compact('service', 'stats', 'absencesByMonth', 'managers', 'majors'));
    }

    /**
     * Édition via modal dans index — redirection directe
     */
    public function edit(int $id)
    {
        return redirect()->route('rh.services.index');
    }

    /**
     * Mettre à jour un service (Intégrité CID : transaction)
     */
    public function update(UpdateServiceRequest $request, int $id)
    {
        $service = Service::findOrFail($id);
        $this->authorize('update', $service);

        DB::transaction(function () use ($request, $service) {
            $service->update($request->validated());

            activity()
                ->causedBy(auth()->user())
                ->performedOn($service)
                ->withProperties($request->validated())
                ->log('Service modifié');
        });

        return redirect()->route('rh.services.index')
            ->with('success', "Le service \"{$service->nom_service}\" a été mis à jour.")
            ->with('edit_service_id', $id);
    }

    /**
     * Supprimer un service (seulement si aucun agent)
     */
    public function destroy(int $id)
    {
        $service = Service::withCount('agents')->findOrFail($id);
        $this->authorize('delete', $service);

        if ($service->agents_count > 0) {
            return back()->with('error', "Impossible de supprimer : {$service->agents_count} agent(s) sont affectés à ce service.");
        }

        DB::transaction(function () use ($service) {
            activity()->causedBy(auth()->user())->log("Service supprimé : {$service->nom_service}");
            $service->delete();
        });

        return redirect()->route('rh.services.index')
            ->with('success', "Le service a été supprimé.");
    }

    /**
     * Assigner un manager à un service
     */
    public function assignerManager(Request $request, int $id)
    {
        $service = Service::findOrFail($id);
        $this->authorize('assignerManager', $service);

        $request->validate([
            'id_agent_manager' => 'nullable|exists:users,id',
        ], [
            'id_agent_manager.exists' => 'L\'utilisateur sélectionné n\'existe pas.',
        ]);

        DB::transaction(function () use ($request, $service) {
            $ancienManager  = $service->id_agent_manager;
            $nouveauManager = $request->id_agent_manager ?: null;
            $service->update(['id_agent_manager' => $nouveauManager]);

            activity()
                ->causedBy(auth()->user())
                ->performedOn($service)
                ->withProperties([
                    'ancien_manager'  => $ancienManager,
                    'nouveau_manager' => $nouveauManager,
                ])
                ->log('Manager du service modifié');
        });

        $msg = $request->id_agent_manager
            ? "Manager assigné au service \"{$service->nom_service}\" avec succès."
            : "Manager retiré du service \"{$service->nom_service}\".";

        return back()->with('success', $msg);
    }

    /**
     * Assigner un major à un service
     */
    public function assignerMajor(Request $request, int $id)
    {
        $service = Service::findOrFail($id);
        $this->authorize('assignerManager', $service);

        $request->validate([
            'id_agent_major' => 'nullable|exists:users,id',
        ], [
            'id_agent_major.exists' => 'L\'utilisateur sélectionné n\'existe pas.',
        ]);

        DB::transaction(function () use ($request, $service) {
            $ancienMajor  = $service->id_agent_major;
            $nouveauMajor = $request->id_agent_major ?: null;
            $service->update(['id_agent_major' => $nouveauMajor]);

            activity()
                ->causedBy(auth()->user())
                ->performedOn($service)
                ->withProperties([
                    'ancien_major'  => $ancienMajor,
                    'nouveau_major' => $nouveauMajor,
                ])
                ->log('Major du service modifié');
        });

        $msg = $request->id_agent_major
            ? "Major assigné au service \"{$service->nom_service}\" avec succès."
            : "Major retiré du service \"{$service->nom_service}\".";

        return back()->with('success', $msg);
    }

    /**
     * Affecter un agent existant à ce service (Intégrité CID : transaction)
     */
    public function attachAgent(Request $request, int $id)
    {
        $service = Service::findOrFail($id);
        $this->authorize('update', $service);

        $request->validate([
            'agent_id' => 'required|exists:agents,id_agent',
        ], [
            'agent_id.required' => 'Veuillez sélectionner un agent.',
            'agent_id.exists'   => 'L\'agent sélectionné n\'existe pas.',
        ]);

        DB::transaction(function () use ($request, $service) {
            $agent = Agent::findOrFail($request->agent_id);
            $agent->update(['id_service' => $service->id_service]);

            activity()
                ->causedBy(auth()->user())
                ->performedOn($agent)
                ->withProperties(['id_service' => $service->id_service, 'service' => $service->nom_service])
                ->log("Agent affecté au service : {$service->nom_service}");
        });

        return back()->with('success', "Agent affecté au service \"{$service->nom_service}\".");
    }

    /**
     * Retirer un agent de ce service (remet id_service à null)
     */
    public function detachAgent(int $id, int $agentId)
    {
        $service = Service::findOrFail($id);
        $this->authorize('update', $service);

        DB::transaction(function () use ($agentId, $service) {
            $agent = Agent::findOrFail($agentId);
            $agent->update(['id_service' => null]);

            activity()
                ->causedBy(auth()->user())
                ->performedOn($agent)
                ->withProperties(['id_service' => null, 'service' => $service->nom_service])
                ->log("Agent retiré du service : {$service->nom_service}");
        });

        return back()->with('success', "Agent retiré du service.");
    }
}
