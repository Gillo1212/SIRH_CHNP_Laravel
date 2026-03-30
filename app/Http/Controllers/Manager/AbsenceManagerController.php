<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\StoreAbsenceRequest;
use App\Models\Absence;
use App\Models\Agent;
use App\Models\Demande;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * AbsenceManagerController
 * Gestion des absences du service du manager.
 * Isolation stricte : seulement les agents du service du manager.
 */
class AbsenceManagerController extends Controller
{
    /**
     * Résoudre le service du manager (ou abort 403)
     */
    private function getManagerService(): Service
    {
        $service = Service::where('id_agent_manager', auth()->id())->first();

        if (!$service) {
            abort(403, 'Aucun service assigné. Contactez l\'administration.');
        }

        return $service;
    }

    /**
     * Liste des absences du service
     */
    public function index(Request $request)
    {
        $service = $this->getManagerService();

        $query = Absence::with(['demande.agent.service'])
            ->forService($service->id_service)
            ->orderByDesc('date_absence');

        // Filtres
        if ($request->filled('mois')) {
            $query->whereMonth('date_absence', $request->mois);
        }
        if ($request->filled('annee')) {
            $query->whereYear('date_absence', $request->annee);
        }
        if ($request->filled('type')) {
            $query->where('type_absence', $request->type);
        }
        if ($request->filled('agent')) {
            $query->whereHas('demande.agent', fn($q) => $q->where('id_agent', $request->agent));
        }

        $absences = $query->paginate(20)->withQueryString();

        // Stats rapides du mois courant
        $statsMois = [
            'total'      => Absence::forService($service->id_service)->whereMonth('date_absence', now()->month)->count(),
            'justifiees' => Absence::forService($service->id_service)->whereMonth('date_absence', now()->month)->where('justifie', true)->count(),
            'maladie'    => Absence::forService($service->id_service)->whereMonth('date_absence', now()->month)->where('type_absence', 'Maladie')->count(),
        ];

        $agents = Agent::where('id_service', $service->id_service)->orderBy('nom')->get();

        return view('manager.absences.index', compact('absences', 'service', 'statsMois', 'agents'));
    }

    /**
     * Formulaire d'enregistrement d'absence
     */
    public function create()
    {
        $service = $this->getManagerService();

        $agents = Agent::where('id_service', $service->id_service)
            ->where('statut_agent', 'Actif')
            ->orderBy('nom')
            ->get();

        return view('manager.absences.create', compact('service', 'agents'));
    }

    /**
     * Détail d'une absence (lecture seule pour le manager)
     */
    public function show($id)
    {
        $service = $this->getManagerService();

        $absence = Absence::with(['demande.agent.service'])
            ->findOrFail($id);

        // Vérifier que l'absence appartient au service du manager (Confidentialité CID)
        $agent = $absence->demande->agent ?? null;
        if (!$agent || $agent->id_service !== $service->id_service) {
            abort(403, 'Accès non autorisé à cette absence.');
        }

        return view('manager.absences.show', compact('absence', 'agent', 'service'));
    }

    /**
     * Enregistrer une absence (Intégrité CID : transaction)
     */
    public function store(StoreAbsenceRequest $request)
    {
        $service = $this->getManagerService();

        // Vérifier que l'agent appartient bien au service du manager (sécurité)
        $agent = Agent::where('id_agent', $request->id_agent)
            ->where('id_service', $service->id_service)
            ->firstOrFail();

        DB::transaction(function () use ($request, $agent) {
            // Créer la demande parente
            $demande = Demande::create([
                'id_agent'       => $agent->id_agent,
                'type_demande'   => 'Absence',
                'statut_demande' => 'Approuvé', // Manager enregistre directement
                'date_traitement'=> now(),
            ]);

            // Créer l'absence
            $absence = Absence::create([
                'id_demande'   => $demande->id_demande,
                'date_absence' => $request->date_absence,
                'type_absence' => $request->type_absence,
                'justifie'     => $request->boolean('justifie', false),
            ]);

            activity()
                ->causedBy(auth()->user())
                ->performedOn($absence)
                ->withProperties([
                    'agent'  => $agent->nom_complet,
                    'date'   => $request->date_absence,
                    'type'   => $request->type_absence,
                ])
                ->log('Absence enregistrée par le manager');
        });

        return redirect()->route('manager.absences.index')
            ->with('success', "L'absence de {$agent->nom_complet} a été enregistrée.");
    }
}
