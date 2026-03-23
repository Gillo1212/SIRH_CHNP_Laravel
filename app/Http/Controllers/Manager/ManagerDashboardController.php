<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Demande;
use App\Models\Service;
use Illuminate\Http\Request;

class ManagerDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Vérifier que l'utilisateur est bien manager d'un service
        $service = Service::where('id_agent_manager', $user->id)->first();
        
        if (!$service) {
            // Rendre le dashboard avec service null — éviter redirect vers agent.dashboard
            // (un Manager n'a pas le rôle Agent)
            return view('manager.dashboard', [
                'stats' => [
                    'service'          => null,
                    'total_agents'     => 0,
                    'demandes_pending' => 0,
                    'conges_pending'   => 0,
                    'agents'           => collect(),
                    'recent_demandes'  => collect(),
                ],
                'noService' => true,
            ]);
        }

        // Statistiques du service
        $stats = [
            'service' => $service,
            'total_agents' => $service->agents()->actif()->count(),
            'demandes_pending' => Demande::whereHas('agent', function($query) use ($service) {
                $query->where('id_service', $service->id_service);
            })->where('statut_demande', 'En_attente')->count(),
            
            'conges_pending' => Demande::whereHas('agent', function($query) use ($service) {
                $query->where('id_service', $service->id_service);
            })
            ->where('type_demande', 'Conge')
            ->where('statut_demande', 'En_attente')
            ->count(),
            
            // Agents du service
            'agents' => $service->agents()->actif()->get(),
            
            // Dernières demandes
            'recent_demandes' => Demande::with(['agent', 'conge', 'absence'])
                ->whereHas('agent', function($query) use ($service) {
                    $query->where('id_service', $service->id_service);
                })
                ->where('statut_demande', 'En_attente')
                ->latest()
                ->take(5)
                ->get(),
        ];

        return view('manager.dashboard', compact('stats'));
    }
}