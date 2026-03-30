<?php

namespace App\Http\Controllers\Major;

use App\Http\Controllers\Controller;
use App\Http\Requests\Major\StoreAbsenceMajorRequest;
use App\Models\Absence;
use App\Models\Agent;
use App\Models\Demande;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * AbsenceMajorController
 * Gestion des absences du service du major.
 * Isolation stricte : seulement les agents du service assigné au major.
 */
class AbsenceMajorController extends Controller
{
    private function getMajorService(): Service
    {
        $service = Service::where('id_agent_major', auth()->id())->first();

        if (!$service) {
            abort(403, 'Aucun service assigné. Contactez l\'administration.');
        }

        return $service;
    }

    public function index(Request $request)
    {
        $service = $this->getMajorService();

        $query = Absence::with(['demande.agent.service'])
            ->forService($service->id_service)
            ->orderByDesc('date_absence');

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

        $statsMois = [
            'total'      => Absence::forService($service->id_service)->whereMonth('date_absence', now()->month)->count(),
            'justifiees' => Absence::forService($service->id_service)->whereMonth('date_absence', now()->month)->where('justifie', true)->count(),
            'maladie'    => Absence::forService($service->id_service)->whereMonth('date_absence', now()->month)->where('type_absence', 'Maladie')->count(),
        ];

        $agents = Agent::where('id_service', $service->id_service)->orderBy('nom')->get();

        return view('major.absences.index', compact('absences', 'service', 'statsMois', 'agents'));
    }

    public function create()
    {
        $service = $this->getMajorService();

        $agents = Agent::where('id_service', $service->id_service)
            ->where('statut_agent', 'Actif')
            ->orderBy('nom')
            ->get();

        return view('major.absences.create', compact('service', 'agents'));
    }

    public function show($id)
    {
        $service = $this->getMajorService();

        $absence = Absence::with(['demande.agent.service'])->findOrFail($id);

        $agent = $absence->demande->agent ?? null;
        if (!$agent || $agent->id_service !== $service->id_service) {
            abort(403, 'Accès non autorisé à cette absence.');
        }

        return view('major.absences.show', compact('absence', 'agent', 'service'));
    }

    public function store(StoreAbsenceMajorRequest $request)
    {
        $service = $this->getMajorService();

        $agent = Agent::where('id_agent', $request->id_agent)
            ->where('id_service', $service->id_service)
            ->firstOrFail();

        DB::transaction(function () use ($request, $agent) {
            $demande = Demande::create([
                'id_agent'        => $agent->id_agent,
                'type_demande'    => 'Absence',
                'statut_demande'  => 'Approuvé',
                'date_traitement' => now(),
            ]);

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
                    'agent' => $agent->nom_complet,
                    'date'  => $request->date_absence,
                    'type'  => $request->type_absence,
                ])
                ->log('Absence enregistrée par le major');
        });

        return redirect()->route('major.absences.index')
            ->with('success', "L'absence de {$agent->nom_complet} a été enregistrée.");
    }
}
