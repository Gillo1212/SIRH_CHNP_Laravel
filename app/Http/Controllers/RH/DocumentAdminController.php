<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Mouvement;
use App\Models\Service;
use Illuminate\Http\Request;

class DocumentAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Agent::with('service')
            ->where('statut_agent', 'actif')
            ->orderBy('nom');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nom', 'like', "%{$s}%")
                  ->orWhere('prenom', 'like', "%{$s}%")
                  ->orWhere('matricule', 'like', "%{$s}%");
            });
        }

        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        if ($request->filled('fonction')) {
            $query->where('fonction', 'like', '%' . $request->fonction . '%');
        }

        $agents   = $query->paginate(20)->withQueryString();
        $services = Service::orderBy('nom_service')->get();

        $stats = [
            'total'        => \App\Models\DemandeDocument::count(),
            'ce_mois'      => \App\Models\DemandeDocument::whereMonth('created_at', now()->month)->count(),
            'en_attente'   => \App\Models\DemandeDocument::where('statut', 'en_attente')->count(),
            'cette_semaine'=> \App\Models\DemandeDocument::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];

        return view('rh.documents-admin.index', compact('agents', 'stats', 'services'));
    }

    public function attestation(Agent $agent)
    {
        return redirect()->route('documents-admin.formulaire', [
            'agentId' => $agent->id_agent,
            'type'    => 'attestation_travail',
        ]);
    }

    public function certificat(Agent $agent)
    {
        return redirect()->route('documents-admin.formulaire', [
            'agentId' => $agent->id_agent,
            'type'    => 'certificat_travail',
        ]);
    }

    public function decisionAffectation(Mouvement $mouvement)
    {
        return redirect()->route('documents-admin.formulaire', [
            'agentId' => $mouvement->id_agent,
            'type'    => 'note_affectation',
        ]);
    }

    public function ordreMission(Agent $agent)
    {
        return redirect()->route('documents-admin.formulaire', [
            'agentId' => $agent->id_agent,
            'type'    => 'ordre_mission',
        ]);
    }
}
