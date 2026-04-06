<?php

namespace App\Http\Controllers\Major;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Conge;
use App\Models\Demande;
use App\Models\Service;
use Illuminate\Http\Request;

/**
 * CongesMajorController
 *
 * Consultation (lecture seule) des congés de l'équipe du Major.
 * Le Major NE VALIDE PAS les congés — c'est le rôle du Manager/RH.
 * Isolation stricte : seulement les agents du service assigné au Major.
 */
class CongesMajorController extends Controller
{
    private function getMajorService(): ?Service
    {
        return Service::where('id_agent_major', auth()->id())->first();
    }

    public function index(Request $request)
    {
        $service = $this->getMajorService();

        if (!$service) {
            return redirect()->route('major.dashboard')
                ->with('error', 'Vous n\'êtes pas assigné à un service. Contactez le service RH.');
        }

        // IDs des agents du service
        $agentIds = Agent::where('id_service', $service->id_service)
            ->pluck('id_agent');

        // Requête de base : congés des agents du service
        $query = Demande::with(['agent', 'conge.typeConge'])
            ->whereIn('id_agent', $agentIds)
            ->where('type_demande', 'Conge')
            ->orderByDesc('created_at');

        // Filtres
        if ($request->filled('agent')) {
            $query->where('id_agent', $request->agent);
        }

        if ($request->filled('statut')) {
            $query->where('statut_demande', $request->statut);
        }

        if ($request->filled('mois') && $request->filled('annee')) {
            $query->whereHas('conge', function ($q) use ($request) {
                $q->whereMonth('date_debut', $request->mois)
                  ->whereYear('date_debut', $request->annee);
            });
        } elseif ($request->filled('annee')) {
            $query->whereHas('conge', function ($q) use ($request) {
                $q->whereYear('date_debut', $request->annee);
            });
        }

        $demandes = $query->paginate(15)->withQueryString();

        // Stats globales du service (toutes périodes)
        $statsGlobales = Demande::whereIn('id_agent', $agentIds)
            ->where('type_demande', 'Conge')
            ->selectRaw('statut_demande, count(*) as total')
            ->groupBy('statut_demande')
            ->pluck('total', 'statut_demande');

        $stats = [
            'total'      => $statsGlobales->sum(),
            'en_attente' => $statsGlobales->get('En_attente', 0),
            'valide'     => $statsGlobales->get('Validé', 0) + $statsGlobales->get('Approuvé', 0),
            'rejete'     => $statsGlobales->get('Rejeté', 0),
            'en_cours'   => Demande::whereIn('id_agent', $agentIds)
                ->where('type_demande', 'Conge')
                ->whereHas('conge', function ($q) {
                    $q->where('date_debut', '<=', now())
                      ->where('date_fin', '>=', now());
                })
                ->count(),
        ];

        // Liste agents pour le filtre
        $agents = Agent::where('id_service', $service->id_service)
            ->orderBy('nom')
            ->get(['id_agent', 'nom', 'prenom']);

        return view('major.conges.index', compact(
            'demandes', 'service', 'stats', 'agents'
        ));
    }

    /**
     * Enregistrer l'avis du Major sur une demande de congé.
     * Le Major ne change PAS le statut — il ajoute uniquement un commentaire.
     */
    public function avis(Request $request, int $id)
    {
        $service = $this->getMajorService();

        if (!$service) {
            return back()->with('error', 'Service non trouvé.');
        }

        $request->validate([
            'avis_major' => 'required|string|max:500',
        ], [
            'avis_major.required' => 'L\'avis ne peut pas être vide.',
            'avis_major.max'      => 'L\'avis ne doit pas dépasser 500 caractères.',
        ]);

        $agentIds = Agent::where('id_service', $service->id_service)->pluck('id_agent');

        $demande = Demande::whereIn('id_agent', $agentIds)
            ->where('type_demande', 'Conge')
            ->findOrFail($id);

        $demande->update([
            'avis_major'    => $request->avis_major,
            'avis_major_at' => now(),
        ]);

        return back()->with('success', 'Votre avis a été enregistré avec succès.');
    }
}
