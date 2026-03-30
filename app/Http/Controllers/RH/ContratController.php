<?php

namespace App\Http\Controllers\RH;

use App\Exports\ExcelExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\RH\StoreContratRequest;
use App\Http\Requests\RH\UpdateContratRequest;
use App\Models\Agent;
use App\Models\Contrat;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContratController extends Controller
{
    // =====================================================
    // LISTE PRINCIPALE
    // =====================================================

    public function index(Request $request)
    {
        $this->authorize('viewAny', Contrat::class);

        $query = $this->buildQuery($request);

        $contrats = $query->paginate(15)->withQueryString();

        $stats = [
            'total'       => Contrat::count(),
            'actifs'      => Contrat::where('statut_contrat', 'Actif')->count(),
            'expiring_30' => Contrat::actif()->expirant(30)->count(),
            'expiring_60' => Contrat::actif()->expirant(60)->count(),
            'expires'     => Contrat::where('statut_contrat', 'Expiré')->count(),
            'en_renouv'   => Contrat::where('statut_contrat', 'En_renouvellement')->count(),
            'clotured'    => Contrat::where('statut_contrat', 'Clôturé')->count(),
        ];

        $services = Service::orderBy('nom_service')->get();
        $agents   = Agent::with('contratActif')
            ->whereIn('statut_agent', ['Actif', 'En_congé'])
            ->orderBy('nom')
            ->get();

        return view('rh.contrats.index', compact('contrats', 'stats', 'services', 'agents'));
    }

    /**
     * Export Excel des contrats avec les filtres actifs.
     */
    public function export(Request $request)
    {
        $this->authorize('viewAny', Contrat::class);

        $contrats = $this->buildQuery($request)->get();

        $export = new ExcelExport('Contrats CHNP');
        $export->setHeaders([
            'Matricule', 'Agent', 'Service', 'Type', 'Statut',
            'Date début', 'Date fin', 'Jours restants', 'Observation',
        ]);

        foreach ($contrats as $c) {
            $export->addRow([
                $c->agent?->matricule ?? '—',
                $c->agent?->nom_complet ?? '—',
                $c->agent?->service?->nom_service ?? '—',
                $c->type_contrat,
                $c->statut_contrat,
                $c->date_debut?->format('d/m/Y') ?? '—',
                $c->date_fin?->format('d/m/Y') ?? 'Indéterminée',
                $c->jours_restants !== null ? $c->jours_restants : '—',
                $c->observation ?? '',
            ]);
        }

        $filename = 'contrats_chnp_' . now()->format('Y-m-d');
        return $export->download($filename);
    }

    /**
     * Construit la requête filtrée (partagée entre index et export).
     */
    private function buildQuery(Request $request)
    {
        $query = Contrat::with(['agent.service'])->orderByDesc('date_debut');

        if ($search = $request->search) {
            $query->whereHas('agent', fn($q) => $q
                ->where('nom', 'like', "%{$search}%")
                ->orWhere('prenom', 'like', "%{$search}%")
                ->orWhere('matricule', 'like', "%{$search}%")
            );
        }

        if ($statut = $request->statut) {
            $query->where('statut_contrat', $statut);
        }

        if ($type = $request->type_contrat) {
            $query->where('type_contrat', $type);
        }

        if ($service = $request->service) {
            $query->whereHas('agent', fn($q) => $q->where('id_service', $service));
        }

        if ($dateFrom = $request->date_debut_from) {
            $query->where('date_debut', '>=', $dateFrom);
        }

        if ($dateTo = $request->date_debut_to) {
            $query->where('date_debut', '<=', $dateTo);
        }

        if ($finFrom = $request->date_fin_from) {
            $query->where('date_fin', '>=', $finFrom);
        }

        if ($finTo = $request->date_fin_to) {
            $query->where('date_fin', '<=', $finTo);
        }

        return $query;
    }

    // =====================================================
    // CRÉATION
    // =====================================================

    public function create()
    {
        $this->authorize('create', Contrat::class);
        return redirect()->route('rh.contrats.index');
    }

    public function store(StoreContratRequest $request)
    {
        $this->authorize('create', Contrat::class);

        DB::transaction(function () use ($request) {
            // Si nouveau contrat est Actif → clôturer les actifs existants
            if ($request->statut_contrat === 'Actif') {
                Contrat::where('id_agent', $request->id_agent)
                       ->where('statut_contrat', 'Actif')
                       ->update(['statut_contrat' => 'Clôturé']);
            }

            $contrat = Contrat::create($request->validated());

            activity()
                ->causedBy(auth()->user())
                ->on($contrat)
                ->withProperties([
                    'agent_id'     => $request->id_agent,
                    'type_contrat' => $request->type_contrat,
                    'date_debut'   => $request->date_debut,
                ])
                ->log("Création contrat {$request->type_contrat} pour agent #{$request->id_agent}");
        });

        return redirect()->route('rh.contrats.index')
            ->with('success', 'Contrat créé avec succès.');
    }

    // =====================================================
    // CONSULTATION (JSON pour modal AJAX)
    // =====================================================

    public function show(Request $request, $id)
    {
        $contrat = Contrat::with(['agent.service.division'])->findOrFail($id);

        $this->authorize('view', $contrat);

        if ($request->expectsJson()) {
            return response()->json([
                'id_contrat'     => $contrat->id_contrat,
                'id_agent'       => $contrat->id_agent,
                'type_contrat'   => $contrat->type_contrat,
                'statut_contrat' => $contrat->statut_contrat,
                'date_debut'     => $contrat->date_debut?->format('Y-m-d'),
                'date_fin'       => $contrat->date_fin?->format('Y-m-d'),
                'observation'    => $contrat->observation,
                'jours_restants' => $contrat->jours_restants,
                'est_expirant'   => $contrat->est_expirant,
                'duree'          => $contrat->duree,
                'agent' => [
                    'nom_complet' => $contrat->agent->nom_complet,
                    'matricule'   => $contrat->agent->matricule,
                    'famille_d_emploi' => $contrat->agent->famille_d_emploi ? str_replace('_', ' ', $contrat->agent->famille_d_emploi) : '—',
                    'service'     => $contrat->agent->service->nom_service ?? '—',
                ],
            ]);
        }

        return redirect()->route('rh.contrats.index');
    }

    // =====================================================
    // MODIFICATION
    // =====================================================

    public function edit($id)
    {
        $contrat = Contrat::findOrFail($id);
        $this->authorize('update', $contrat);
        return redirect()->route('rh.contrats.index');
    }

    public function update(UpdateContratRequest $request, $id)
    {
        $contrat = Contrat::findOrFail($id);
        $this->authorize('update', $contrat);

        DB::transaction(function () use ($request, $contrat) {
            $ancien = $contrat->replicate()->toArray();
            $contrat->update($request->validated());

            activity()
                ->causedBy(auth()->user())
                ->on($contrat)
                ->withProperties(['ancien' => $ancien, 'nouveau' => $request->validated()])
                ->log("Modification contrat #{$contrat->id_contrat}");
        });

        return redirect()->route('rh.contrats.index')
            ->with('success', 'Contrat mis à jour avec succès.');
    }

    // =====================================================
    // RENOUVELLEMENT
    // =====================================================

    public function renouveler(Request $request, $id)
    {
        $ancien = Contrat::with('agent')->findOrFail($id);
        $this->authorize('renouveler', $ancien);

        $request->validate([
            'date_debut'   => 'required|date',
            'date_fin'     => 'nullable|date|after:date_debut',
            'type_contrat' => 'required|in:PE,PCH,PU,Vacataire,CMSAS,Interne,Stagiaire',
            'observation'  => 'nullable|string|max:1000',
        ], [
            'date_debut.required'   => 'La date de début du nouveau contrat est obligatoire.',
            'date_debut.date'       => 'Format de date invalide.',
            'date_fin.after'        => 'La date de fin doit être postérieure à la date de début.',
            'type_contrat.required' => 'Le type de contrat est obligatoire.',
        ]);

        DB::transaction(function () use ($request, $ancien) {
            $ancien->update(['statut_contrat' => 'Clôturé']);

            $nouveau = Contrat::create([
                'id_agent'       => $ancien->id_agent,
                'type_contrat'   => $request->type_contrat,
                'date_debut'     => $request->date_debut,
                'date_fin'       => $request->date_fin,
                'statut_contrat' => 'Actif',
                'observation'    => $request->observation
                    ?: "Renouvellement du contrat #{$ancien->id_contrat} du " . $ancien->date_debut->format('d/m/Y'),
            ]);

            activity()
                ->causedBy(auth()->user())
                ->withProperties([
                    'ancien_id'  => $ancien->id_contrat,
                    'nouveau_id' => $nouveau->id_contrat,
                    'agent_id'   => $ancien->id_agent,
                ])
                ->log("Renouvellement contrat #{$ancien->id_contrat} → #{$nouveau->id_contrat}");
        });

        return redirect()->back()
            ->with('success', 'Contrat renouvelé avec succès. Un nouveau contrat actif a été créé.');
    }

    // =====================================================
    // CLÔTURE
    // =====================================================

    public function cloturer($id)
    {
        $contrat = Contrat::with('agent')->findOrFail($id);
        $this->authorize('cloturer', $contrat);

        DB::transaction(function () use ($contrat) {
            $contrat->update(['statut_contrat' => 'Clôturé']);

            activity()
                ->causedBy(auth()->user())
                ->on($contrat)
                ->log("Clôture contrat #{$contrat->id_contrat} — agent {$contrat->agent->nom_complet}");
        });

        return redirect()->back()
            ->with('success', 'Contrat clôturé avec succès.');
    }

    // =====================================================
    // CONTRATS EXPIRANTS
    // =====================================================

    public function expiring(Request $request)
    {
        $this->authorize('viewAny', Contrat::class);

        $jours = max(1, min(365, (int) $request->get('jours', 60)));

        $contrats = Contrat::with(['agent.service'])
            ->actif()
            ->expirant($jours)
            ->orderBy('date_fin')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'expiring_7'  => Contrat::actif()->expirant(7)->count(),
            'expiring_15' => Contrat::actif()->expirant(15)->count(),
            'expiring_30' => Contrat::actif()->expirant(30)->count(),
            'expiring_60' => Contrat::actif()->expirant(60)->count(),
        ];

        return view('rh.contrats.expiring', compact('contrats', 'stats', 'jours'));
    }
}
