<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\DemandeDocument;
use App\Models\User;
use App\Notifications\DemandeDocumentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocAdminAgentController extends Controller
{
    private function agentId(): int
    {
        return Auth::user()->agent->id_agent;
    }

    public function index()
    {
        $demandes = DemandeDocument::where('agent_id', $this->agentId())
            ->latest()
            ->paginate(10);

        return view('agent.docs.index', compact('demandes'));
    }

    public function create()
    {
        return view('agent.docs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type_document' => 'required|in:attestation_travail,certificat_travail,ordre_mission',
            'motif'         => 'nullable|string|max:500',
        ]);

        DemandeDocument::create([
            'agent_id'      => $this->agentId(),
            'type_document' => $validated['type_document'],
            'motif'         => $validated['motif'] ?? null,
            'statut'        => 'en_attente',
        ]);

        activity()->causedBy(Auth::user())->log('Demande document : ' . $validated['type_document']);

        // Notifier les AgentRH (hors transaction)
        try {
            $agent = Auth::user()->agent;
            User::role(['AgentRH', 'DRH'])->each(
                fn(User $rh) => $rh->notify(new DemandeDocumentNotification($agent, $validated['type_document']))
            );
        } catch (\Throwable $e) {
            \Log::warning('Notification demande doc RH échouée : ' . $e->getMessage());
        }

        return redirect()->route('agent.docs.index')
            ->with('success', 'Votre demande a été soumise. Vous serez notifié lorsqu\'elle sera prête.');
    }

    public function show($id)
    {
        $demande = DemandeDocument::where('agent_id', $this->agentId())->findOrFail($id);
        return view('agent.docs.show', compact('demande'));
    }

    public function download($id)
    {
        // Vérification : document appartient à l'agent + statut prêt (Confidentialité CID)
        $demande = DemandeDocument::where('agent_id', $this->agentId())
            ->where('statut', 'pret')
            ->findOrFail($id);

        $agent = $demande->agent->load(['service', 'division']);

        // Audit trail (Intégrité CID)
        activity()
            ->causedBy(Auth::user())
            ->withProperties(['type' => $demande->type_document, 'demande_id' => $demande->id])
            ->log('Consultation document administratif : ' . $demande->libelleType);

        // Rendu direct de la vue agent — évite la redirection vers la route
        // protégée par role:AgentRH|DRH|AdminSystème
        return view('agent.docs.view-document', compact('demande', 'agent'));
    }
}
