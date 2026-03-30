<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Agent;
use App\Models\Demande;
use App\Models\Mouvement;
use App\Models\Service;
use App\Services\ServiceStatisticsService;

/**
 * MonServiceController
 * Dashboard et statistiques du service du manager connecté.
 * Isolation stricte : le manager ne voit QUE son service.
 */
class MonServiceController extends Controller
{
    public function __construct(private ServiceStatisticsService $statsService) {}

    /**
     * Résoudre le service du manager connecté (et bloquer si non assigné)
     */
    private function getManagerService(): Service
    {
        $service = Service::where('id_agent_manager', auth()->id())
            ->with(['divisions', 'agents'])
            ->first();

        if (!$service) {
            abort(403, 'Vous n\'êtes assigné à aucun service. Contactez l\'administration.');
        }

        return $service;
    }

    /**
     * Vue d'ensemble du service
     */
    public function index()
    {
        $service = $this->getManagerService();
        $stats   = $this->statsService->getServiceStats($service->id_service);

        // Dernières demandes en attente (5 max)
        $demandesPending = Demande::with(['agent', 'conge.typeConge', 'absence'])
            ->where('statut_demande', 'En_attente')
            ->whereHas('agent', fn($q) => $q->where('id_service', $service->id_service)
                ->where('id_agent', '!=', auth()->user()->agent?->id_agent ?? 0))
            ->latest()
            ->take(5)
            ->get();

        // Absences d'aujourd'hui
        $absencesAujourdhui = Absence::with(['demande.agent'])
            ->forService($service->id_service)
            ->where('date_absence', today())
            ->get();

        return view('manager.service.index', compact('service', 'stats', 'demandesPending', 'absencesAujourdhui'));
    }

    /**
     * Liste des agents du service (isolation stricte)
     */
    public function agents()
    {
        $service = $this->getManagerService();

        $agents = Agent::with(['user', 'contratActif'])
            ->where('id_service', $service->id_service)
            ->orderBy('nom')
            ->get();

        return view('manager.service.agents', compact('service', 'agents'));
    }

    /**
     * Mouvements du service (lecture seule pour le manager)
     */
    public function mouvements()
    {
        $service = $this->getManagerService();

        $mouvements = Mouvement::with(['agent', 'serviceDestination', 'serviceOrigine'])
            ->where(function ($q) use ($service) {
                $q->where('id_service', $service->id_service)
                  ->orWhere('id_service_origine', $service->id_service);
            })
            ->orderByDesc('date_mouvement')
            ->paginate(20);

        $stats = [
            'entrants' => Mouvement::where('id_service', $service->id_service)
                                   ->whereIn('statut', ['valide_drh', 'effectue'])->count(),
            'sortants' => Mouvement::where('id_service_origine', $service->id_service)
                                   ->whereIn('statut', ['valide_drh', 'effectue'])->count(),
            'en_cours' => Mouvement::where(fn($q) => $q
                            ->where('id_service', $service->id_service)
                            ->orWhere('id_service_origine', $service->id_service))
                            ->whereIn('statut', ['en_attente', 'valide_drh'])->count(),
        ];

        return view('manager.mouvements.index', compact('service', 'mouvements', 'stats'));
    }

    /**
     * Statistiques détaillées du service
     */
    public function statistics()
    {
        $service         = $this->getManagerService();
        $stats           = $this->statsService->getServiceStats($service->id_service);
        $absencesByMonth = $this->statsService->getAbsencesByMonth($service->id_service);
        $agentsByStatut  = $this->statsService->getAgentsByStatut($service->id_service);

        return view('manager.service.statistics', compact(
            'service', 'stats', 'absencesByMonth', 'agentsByStatut'
        ));
    }
}
