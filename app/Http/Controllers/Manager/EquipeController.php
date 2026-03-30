<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Service;

class EquipeController extends Controller
{
    private function getService(): ?Service
    {
        return Service::where('id_agent_manager', auth()->id())
            ->with('divisions')
            ->first();
    }

    public function index()
    {
        $service = $this->getService();

        if (!$service) {
            return redirect()->route('manager.dashboard')
                ->with('error', 'Vous n\'êtes pas encore assigné à un service. Contactez le service RH.');
        }

        $agents = Agent::where('id_service', $service->id_service)
            ->with(['contratActif', 'demandes' => fn($q) => $q->where('type_demande', 'Absence')->whereMonth('created_at', now()->month)])
            ->orderBy('nom')
            ->get();

        $stats = [
            'total'      => $agents->count(),
            'actifs'     => $agents->where('statut_agent', 'Actif')->count(),
            'en_conge'   => $agents->where('statut_agent', 'En_congé')->count(),
            'suspendus'  => $agents->where('statut_agent', 'Suspendu')->count(),
        ];

        return view('manager.equipe.index', compact('service', 'agents', 'stats'));
    }

    public function dossiers()
    {
        $service = $this->getService();

        if (!$service) {
            return redirect()->route('manager.dashboard');
        }

        $agents = Agent::where('id_service', $service->id_service)
            ->with(['contratActif', 'demandes'])
            ->orderBy('nom')
            ->get();

        return view('manager.equipe.dossiers', compact('service', 'agents'));
    }

    public function show($id)
    {
        $service = $this->getService();

        if (!$service) {
            return redirect()->route('manager.dashboard');
        }

        // Vérifier que l'agent appartient bien au service du manager
        $agent = Agent::where('id_service', $service->id_service)
            ->with([
                'service.division',
                'contratActif',
                'demandes' => fn($q) => $q->latest()->take(10),
                'demandes.conge',
                'demandes.absence',
                'enfants',
            ])
            ->where('id_agent', $id)
            ->firstOrFail();

        return view('manager.equipe.show', compact('agent', 'service'));
    }
}
