<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agent\StoreDemandeDocumentRequest;
use App\Models\DemandeDocument;
use App\Models\User;
use App\Notifications\DemandeDocumentNotification;
use App\Services\DocumentGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class DocAdminAgentController extends Controller
{
    public function __construct(
        protected DocumentGeneratorService $documentService
    ) {}

    private function agentId(): int
    {
        return Auth::user()->agent->id_agent;
    }

    /**
     * Champs que l'agent peut renseigner pour chaque type de document.
     */
    private function getChampsAgent(string $type): array
    {
        return match($type) {
            'ordre_mission' => [
                'destination'      => ['label' => 'Destination', 'type' => 'text', 'required' => true],
                'objet'            => ['label' => 'Objet de la mission', 'type' => 'textarea', 'required' => true],
                'date_debut'       => ['label' => 'Date de départ', 'type' => 'date', 'required' => true],
                'date_fin'         => ['label' => 'Date de retour', 'type' => 'date', 'required' => true],
                'moyens_transport' => ['label' => 'Moyen de transport', 'type' => 'text', 'required' => false, 'default' => 'Véhicule de service'],
            ],
            'autorisation_sortie_territoire' => [
                'destination' => ['label' => 'Pays / Ville de destination', 'type' => 'text', 'required' => true],
                'motif'       => ['label' => 'Motif du déplacement', 'type' => 'textarea', 'required' => true],
                'date_debut'  => ['label' => 'Date de départ', 'type' => 'date', 'required' => true],
                'date_fin'    => ['label' => 'Date de retour', 'type' => 'date', 'required' => true],
                'organisme'   => ['label' => "Organisme d'accueil", 'type' => 'text', 'required' => false],
            ],
            'decision_conge_administratif' => [
                'duree_jours'       => ['label' => 'Nombre de jours demandés', 'type' => 'number', 'required' => true, 'default' => 30],
                'periode_ref_debut' => ['label' => 'Période de référence — Début', 'type' => 'date', 'required' => false],
                'periode_ref_fin'   => ['label' => 'Période de référence — Fin',   'type' => 'date', 'required' => false],
            ],
            'attestation_jouissance_conge' => [
                'duree_totale'     => ['label' => 'Durée totale du congé (jours)', 'type' => 'number', 'required' => false, 'default' => 30],
                'duree_jouissance' => ['label' => 'Jours à jouir',                  'type' => 'number', 'required' => false, 'default' => 15],
                'date_debut'       => ['label' => 'Date de début de jouissance',     'type' => 'date',   'required' => false],
                'date_reprise'     => ['label' => 'Date de reprise',                 'type' => 'date',   'required' => false],
            ],
            'attestation_cessation_maternite' => [
                'date_cessation' => ['label' => 'Date de cessation souhaitée', 'type' => 'date',   'required' => true],
                'duree_semaines' => ['label' => 'Durée du congé (semaines)',   'type' => 'number', 'required' => false, 'default' => 14],
            ],
            'attestation_prise_service' => [
                'date_prise_service' => ['label' => 'Date de prise de service', 'type' => 'date',   'required' => true],
                'specialite'         => ['label' => 'Spécialité / DES',          'type' => 'text',   'required' => false],
            ],
            'attestation_stage' => [
                'date_debut'    => ['label' => 'Date de début du stage', 'type' => 'date', 'required' => true],
                'date_fin'      => ['label' => 'Date de fin du stage',   'type' => 'date', 'required' => true],
                'service_stage' => ['label' => 'Service de stage',       'type' => 'text', 'required' => false],
            ],
            'attestation_prime_motivation' => [
                'periodicite' => ['label' => 'Périodicité', 'type' => 'select', 'required' => false,
                    'options' => ['mensuelle' => 'Mensuelle', 'trimestrielle' => 'Trimestrielle', 'annuelle' => 'Annuelle']],
            ],
            default => [],
        };
    }

    public function index(Request $request)
    {
        $query = DemandeDocument::where('agent_id', $this->agentId())->latest();

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $demandes = $query->paginate(10)->withQueryString();

        $stats = [
            'total'      => DemandeDocument::where('agent_id', $this->agentId())->count(),
            'en_attente' => DemandeDocument::where('agent_id', $this->agentId())->where('statut', 'en_attente')->count(),
            'en_cours'   => DemandeDocument::where('agent_id', $this->agentId())->where('statut', 'en_cours')->count(),
            'pret'       => DemandeDocument::where('agent_id', $this->agentId())->where('statut', 'pret')->count(),
        ];

        return view('agent.docs.index', compact('demandes', 'stats'));
    }

    public function create()
    {
        $categories = DemandeDocument::CATEGORIES_DOCUMENTS;
        $types      = DemandeDocument::TYPES_DOCUMENTS;

        // Champs spécifiques par type (pour Alpine.js dans la vue)
        $champsParType = [];
        foreach (array_keys($types) as $typeKey) {
            $champsParType[$typeKey] = $this->getChampsAgent($typeKey);
        }

        return view('agent.docs.create', compact('categories', 'types', 'champsParType'));
    }

    public function store(StoreDemandeDocumentRequest $request)
    {
        $validated = $request->validated();

        // Collecter les données spécifiques saisies par l'agent
        $champsAgent = $this->getChampsAgent($validated['type_document']);
        $donneesSpecifiques = [];
        foreach ($champsAgent as $champ => $config) {
            if ($request->has($champ) && $request->input($champ) !== '') {
                $donneesSpecifiques[$champ] = $request->input($champ);
            }
        }

        $demande = DemandeDocument::create([
            'agent_id'            => $this->agentId(),
            'type_document'       => $validated['type_document'],
            'motif'               => $validated['motif'] ?? null,
            'donnees_specifiques' => $donneesSpecifiques ?: null,
            'statut'              => 'en_attente',
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($demande)
            ->withProperties(['type' => $validated['type_document']])
            ->log('Demande document : ' . DemandeDocument::TYPES_DOCUMENTS[$validated['type_document']]);

        try {
            $agent = Auth::user()->agent;
            User::role(['AgentRH', 'DRH'])->each(
                fn(User $rh) => $rh->notify(new DemandeDocumentNotification($agent, $validated['type_document']))
            );
        } catch (\Throwable $e) {
            \Log::warning('Notification demande doc RH échouée : ' . $e->getMessage());
        }

        return redirect()->route('agent.docs.show', $demande->id)
            ->with('success', 'Votre demande a été soumise. Le service RH vous notifiera dès que votre document sera prêt.');
    }

    public function show($id)
    {
        $demande = DemandeDocument::where('agent_id', $this->agentId())->findOrFail($id);
        $champsAgent = $this->getChampsAgent($demande->type_document);
        return view('agent.docs.show', compact('demande', 'champsAgent'));
    }

    public function cancel($id)
    {
        $demande = DemandeDocument::where('agent_id', $this->agentId())
            ->where('statut', 'en_attente')
            ->findOrFail($id);

        $demande->update([
            'statut'      => 'rejete',
            'motif_rejet' => "Annulé par l'agent",
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($demande)
            ->log('Demande document annulée par l\'agent : ' . $demande->libelleType);

        return redirect()->route('agent.docs.index')
            ->with('success', 'Votre demande a été annulée.');
    }

    public function download($id)
    {
        $demande = DemandeDocument::where('agent_id', $this->agentId())
            ->where('statut', 'pret')
            ->with(['agent.service', 'agentRemplacant', 'serviceDestination'])
            ->findOrFail($id);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($demande)
            ->withProperties(['type' => $demande->type_document, 'demande_id' => $demande->id])
            ->log('Consultation document administratif : ' . $demande->libelleType);

        $data = $this->documentService->preparerDonnees($demande);
        $data['preview']        = false;
        $data['isAgentView']    = true;
        $data['titre_document'] = $demande->libelleType;

        $viewName = 'rh.documents-admin.templates.' . str_replace('_', '-', $demande->type_document);

        if (!View::exists($viewName)) {
            return view('agent.docs.view-document', ['demande' => $demande, 'agent' => $demande->agent]);
        }

        return view($viewName, $data);
    }
}
