<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Http\Requests\RH\CreateAgentRequest;
use App\Http\Requests\RH\UpdateAgentRequest;
use App\Models\Agent;
use App\Models\Division;
use App\Models\Service;
use App\Exports\AgentsExport;
use App\Services\AgentService;
use App\Repositories\Contracts\AgentRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AgentRHController extends Controller
{
    public function __construct(
        private AgentService $service,
        private AgentRepositoryInterface $repo
    ) {}

    /**
     * Liste des agents avec recherche multicritère
     * Confidentialité CID : Gate::authorize avant affichage
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Agent::class);

        $filters = $request->only(['recherche', 'service', 'statut', 'categorie', 'sexe']);
        $agents  = $this->repo->paginate(15, $filters);

        $services  = Service::orderBy('nom_service')->get(['id_service', 'nom_service']);
        $divisions = Division::orderBy('nom_division')->get(['id_division', 'nom_division']);

        // Statistiques rapides pour le bandeau
        $stats = [
            'total'    => Agent::count(),
            'actifs'   => Agent::where('statut', 'Actif')->count(),
            'en_conge' => Agent::where('statut', 'En_congé')->count(),
            'suspendus'=> Agent::where('statut', 'Suspendu')->count(),
        ];

        $prochainMatricule = $this->repo->nextMatricule();

        return view('rh.agents.index', compact('agents', 'filters', 'services', 'divisions', 'stats', 'prochainMatricule'));
    }

    /**
     * Liste des comptes Admin en attente de dossier RH (agent_completed = false)
     */
    public function comptesACompleter()
    {
        Gate::authorize('viewAny', Agent::class);

        // Les AdminSystème n'ont pas de dossier RH
        $comptes = \App\Models\User::with('roles')
            ->where('agent_completed', false)
            ->whereDoesntHave('roles', fn($q) => $q->where('name', 'AdminSystème'))
            ->latest()
            ->get();

        $services          = Service::orderBy('nom_service')->get(['id_service', 'nom_service']);
        $divisions         = Division::orderBy('nom_division')->get(['id_division', 'nom_division']);
        $prochainMatricule = $this->repo->nextMatricule();

        return view('rh.agents.comptes-a-completer', compact('comptes', 'services', 'divisions', 'prochainMatricule'));
    }

    /**
     * Formulaire de création d'un nouvel agent (de zéro)
     */
    public function create()
    {
        Gate::authorize('create', Agent::class);

        $services  = Service::orderBy('nom_service')->get(['id_service', 'nom_service']);
        $divisions = Division::orderBy('nom_division')->get(['id_division', 'nom_division']);
        $prochainMatricule = $this->repo->nextMatricule();

        return view('rh.agents.create', compact('services', 'divisions', 'prochainMatricule'));
    }

    /**
     * Complétion dossier RH : redirige vers la liste avec le modal pré-ouvert.
     * Le formulaire est désormais un modal dans comptes-a-completer.
     */
    public function completerDossierForm(int $userId)
    {
        Gate::authorize('create', Agent::class);

        // Vérifier existence + pas AdminSystème
        \App\Models\User::where('agent_completed', false)
            ->whereDoesntHave('roles', fn($q) => $q->where('name', 'AdminSystème'))
            ->findOrFail($userId);

        return redirect()
            ->route('rh.agents.comptes-a-completer')
            ->with('open_modal_user_id', $userId);
    }

    /**
     * Enregistrer un nouvel agent
     * Intégrité CID : FormRequest + transaction dans le Service
     */
    public function store(CreateAgentRequest $request)
    {
        Gate::authorize('create', Agent::class);

        $agent = $this->service->creerAgent(
            $request->validated(),
            $request->file('photo')
        );

        return redirect()
            ->route('rh.agents.show', $agent->id_agent)
            ->with('success', "Agent {$agent->nom_complet} ({$agent->matricule}) créé avec succès.");
    }

    /**
     * Dossier complet d'un agent
     */
    public function show(int $id)
    {
        $agent = $this->repo->findById($id);
        Gate::authorize('view', $agent);

        // Données sensibles déchiffrées selon les droits
        $voirSensibles = Gate::allows('voirDonneesSensibles', $agent);

        return view('rh.agents.show', compact('agent', 'voirSensibles'));
    }

    /**
     * Formulaire de modification
     */
    public function edit(int $id)
    {
        $agent = $this->repo->findById($id);
        Gate::authorize('update', $agent);

        $services  = Service::orderBy('nom_service')->get(['id_service', 'nom_service']);
        $divisions = Division::orderBy('nom_division')->get(['id_division', 'nom_division']);

        return view('rh.agents.edit', compact('agent', 'services', 'divisions'));
    }

    /**
     * Mettre à jour un agent
     */
    public function update(UpdateAgentRequest $request, int $id)
    {
        $agent = Agent::findOrFail($id);
        Gate::authorize('update', $agent);

        $agent = $this->service->modifierAgent(
            $id,
            $request->validated(),
            $request->file('photo')
        );

        return redirect()
            ->route('rh.agents.show', $agent->id_agent)
            ->with('success', "Dossier de {$agent->nom_complet} mis à jour.");
    }

    /**
     * Soft delete d'un agent
     */
    public function destroy(int $id)
    {
        $agent = Agent::findOrFail($id);
        Gate::authorize('delete', $agent);

        $nom = $agent->nom_complet;
        $this->service->supprimerAgent($id);

        return redirect()
            ->route('rh.agents.index')
            ->with('success', "Agent {$nom} archivé.");
    }

    /**
     * Export CSV des agents
     * Confidentialité CID : sans champs sensibles (adresse, téléphone, n° assurance)
     */
    public function export(Request $request)
    {
        Gate::authorize('export', Agent::class);

        return (new AgentsExport($request->only(['statut', 'service'])))->download();
    }
}
