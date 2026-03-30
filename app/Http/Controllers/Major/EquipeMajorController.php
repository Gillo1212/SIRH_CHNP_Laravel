<?php

namespace App\Http\Controllers\Major;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Service;

/**
 * EquipeMajorController — Lecture seule (Confidentialité CID)
 * Le Major consulte l'équipe de son service sans pouvoir modifier les dossiers.
 */
class EquipeMajorController extends Controller
{
    private function getService(): ?Service
    {
        return Service::where('id_agent_major', auth()->id())
            ->with('divisions')
            ->first();
    }

    public function index()
    {
        $service = $this->getService();

        if (!$service) {
            return redirect()->route('major.dashboard')
                ->with('error', 'Vous n\'êtes pas encore assigné à un service. Contactez le service RH.');
        }

        $agents = Agent::where('id_service', $service->id_service)
            ->with([
                'contratActif',
                'demandes' => fn($q) => $q->where('type_demande', 'Absence')
                    ->whereMonth('created_at', now()->month),
            ])
            ->orderBy('nom')
            ->get();

        $stats = [
            'total'     => $agents->count(),
            'actifs'    => $agents->where('statut_agent', 'Actif')->count(),
            'en_conge'  => $agents->where('statut_agent', 'En_congé')->count(),
            'suspendus' => $agents->where('statut_agent', 'Suspendu')->count(),
        ];

        return view('major.equipe.index', compact('service', 'agents', 'stats'));
    }
}
