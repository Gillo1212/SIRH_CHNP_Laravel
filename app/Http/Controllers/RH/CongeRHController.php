<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Conge;
use App\Models\Demande;
use App\Models\Service;
use App\Models\SoldeConge;
use App\Models\TypeConge;
use App\Notifications\CongeApprouveNotification;
use App\Notifications\CongeRejeteNotification;
use App\Exports\ExcelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CongeRHController extends Controller
{
    public function index(Request $request)
    {
        $query = Demande::with(['agent.service', 'conge.typeConge'])
            ->where('type_demande', 'Conge')
            ->orderByDesc('created_at');

        if ($request->filled('statut')) {
            $query->where('statut_demande', $request->statut);
        }
        if ($request->filled('service')) {
            $query->whereHas('agent', fn($q) => $q->where('id_service', $request->service));
        }
        if ($request->filled('type_conge')) {
            $query->whereHas('conge', fn($q) => $q->where('id_type_conge', $request->type_conge));
        }
        if ($request->filled('agent')) {
            $query->where('id_agent', $request->agent);
        }

        $demandes = $query->paginate(20)->withQueryString();

        $stats = [
            'total'      => Demande::where('type_demande', 'Conge')->count(),
            'en_attente' => Demande::where('type_demande', 'Conge')->where('statut_demande', 'En_attente')->count(),
            'valides'    => Demande::where('type_demande', 'Conge')->where('statut_demande', 'Validé')->count(),
            'approuves'  => Demande::where('type_demande', 'Conge')->where('statut_demande', 'Approuvé')->count(),
            'rejetes'    => Demande::where('type_demande', 'Conge')->where('statut_demande', 'Rejeté')->count(),
        ];

        $services   = Service::orderBy('nom_service')->get();
        $typesConge = TypeConge::orderBy('libelle')->get();
        $agents     = Agent::orderBy('nom')->get();

        return view('rh.conges.index', compact('demandes', 'stats', 'services', 'typesConge', 'agents'));
    }

    public function pending()
    {
        $pending = Demande::with(['agent.service', 'conge.typeConge'])
            ->where('type_demande', 'Conge')
            ->where('statut_demande', 'Validé')
            ->orderBy('created_at')
            ->paginate(20);

        return view('rh.conges.pending', compact('pending'));
    }

    public function show($id)
    {
        $demande = Demande::with(['agent.service', 'conge.typeConge'])->findOrFail($id);

        // Historique des congés précédents de l'agent (hors demande courante)
        $historique = Demande::with(['conge.typeConge'])
            ->where('id_agent', $demande->id_agent)
            ->where('type_demande', 'Conge')
            ->where('id_demande', '!=', $demande->id_demande)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Solde actuel pour ce type de congé
        $solde = null;
        if ($demande->conge?->id_type_conge) {
            $solde = SoldeConge::where('id_agent', $demande->id_agent)
                ->where('id_type_conge', $demande->conge->id_type_conge)
                ->where('annee', now()->year)
                ->first();
        }

        return view('rh.conges.show', compact('demande', 'historique', 'solde'));
    }

    public function approuver(Request $request, $id)
    {
        $demande = Demande::with(['agent', 'conge.typeConge'])->findOrFail($id);

        DB::transaction(function () use ($demande) {
            $conge = $demande->conge;

            if ($conge && $conge->typeConge?->deductible) {
                $solde = SoldeConge::where('id_agent', $demande->id_agent)
                    ->where('id_type_conge', $conge->id_type_conge)
                    ->where('annee', now()->year)
                    ->first();

                if ($solde) {
                    $solde->deduire($conge->nbres_jours);
                }
            }

            if ($conge) {
                $conge->update(['date_approbation' => now()]);
            }

            $demande->update([
                'statut_demande'  => 'Approuvé',
                'date_traitement' => now(),
            ]);

            $demande->agent->update(['statut_agent' => 'En_Conge']);

            activity()
                ->causedBy(auth()->user())
                ->performedOn($demande)
                ->withProperties(['agent' => $demande->agent->nom_complet])
                ->log('Congé approuvé par RH');
        });

        // Notifier l'agent (hors transaction)
        try {
            if ($demande->agent->user) {
                $demande->agent->user->notify(new CongeApprouveNotification($demande));
            }
        } catch (\Throwable $e) {
            \Log::warning('Notification approbation congé échouée : ' . $e->getMessage());
        }

        return back()->with('success', 'Congé approuvé et solde mis à jour.');
    }

    public function rejeter(Request $request, $id)
    {
        $request->validate(['motif' => ['required', 'string', 'max:500']]);

        $demande = Demande::findOrFail($id);

        DB::transaction(function () use ($request, $demande) {
            $demande->update([
                'statut_demande'  => 'Rejeté',
                'motif_refus'     => $request->motif,
                'date_traitement' => now(),
            ]);

            activity()
                ->causedBy(auth()->user())
                ->performedOn($demande)
                ->log('Congé rejeté par RH');
        });

        // Notifier l'agent (hors transaction)
        try {
            $demande->load('agent');
            if ($demande->agent?->user) {
                $demande->agent->user->notify(new CongeRejeteNotification($demande, 'le service RH'));
            }
        } catch (\Throwable $e) {
            \Log::warning('Notification rejet congé RH échouée : ' . $e->getMessage());
        }

        return back()->with('success', 'Congé rejeté.');
    }

    public function downloadJustificatif(Request $request, $id)
    {
        $demande = Demande::with('conge')->findOrFail($id);
        $conge = $demande->conge;

        if (!$conge || !$conge->justificatif_path) {
            abort(404, 'Aucun justificatif disponible.');
        }

        $path     = $conge->justificatif_path;
        $mime     = Storage::disk('private')->mimeType($path);
        $filename = 'certificat_medical_conge_' . $id;

        if ($request->query('inline')) {
            return response(Storage::disk('private')->get($path), 200)
                ->header('Content-Type', $mime)
                ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
        }

        return Storage::disk('private')->download($path, $filename);
    }

    public function enCours(Request $request)
    {
        $annee     = now()->year;
        $serviceId = $request->get('service');

        $query = Agent::with([
            'service',
            'soldeConges' => fn($q) => $q->where('annee', $annee)->with('typeConge'),
            'demandes'    => fn($q) => $q
                ->where('type_demande', 'Conge')
                ->where('statut_demande', 'Approuvé')
                ->with('conge.typeConge')
                ->orderByDesc('created_at'),
        ])->where('statut_agent', 'En_Conge');

        if ($serviceId) {
            $query->where('id_service', $serviceId);
        }

        $agentsEnConge = $query->orderBy('nom')->get()->map(function ($agent) {
            $agent->conge_en_cours = $agent->demandes
                ->first(fn($d) => $d->conge && now()->between($d->conge->date_debut, $d->conge->date_fin));
            if (!$agent->conge_en_cours) {
                $agent->conge_en_cours = $agent->demandes->first();
            }
            return $agent;
        });

        $typesConge = TypeConge::deductible()->orderBy('libelle')->get();
        $services   = Service::orderBy('nom_service')->get();

        return view('rh.conges.en-cours', compact('agentsEnConge', 'typesConge', 'services', 'annee', 'serviceId'));
    }

    public function soldes(Request $request)
    {
        $annee       = $request->get('annee', now()->year);
        $search      = $request->get('search');
        $serviceId   = $request->get('service');
        $typeCongeId = $request->get('type_conge');

        $query = Agent::with([
            'service',
            'soldeConges' => fn($q) => $q->where('annee', $annee)->with('typeConge'),
        ])->where('statut_agent', 'Actif');

        if ($search) {
            $query->where(fn($q) => $q
                ->where('nom', 'like', "%{$search}%")
                ->orWhere('prenom', 'like', "%{$search}%")
                ->orWhere('matricule', 'like', "%{$search}%")
            );
        }
        if ($serviceId) {
            $query->where('id_service', $serviceId);
        }

        $agents = $query->orderBy('nom')->get();

        if ($typeCongeId) {
            $agents = $agents->filter(fn($a) => $a->soldeConges->where('id_type_conge', $typeCongeId)->isNotEmpty());
        } else {
            $agents = $agents->filter(fn($a) => $a->soldeConges->isNotEmpty());
        }

        $soldes = $agents->mapWithKeys(fn($a) => [
            $a->id_agent => $a->soldeConges->keyBy('id_type_conge'),
        ]);

        $typesConge = TypeConge::deductible()->orderBy('libelle')->get();
        $services   = Service::orderBy('nom_service')->get();
        $annees     = range(now()->year - 2, now()->year + 1);

        $allSoldes = SoldeConge::where('annee', $annee)->get();
        $kpis = [
            'agents'         => $agents->count(),
            'total_initial'  => $allSoldes->sum('solde_initial'),
            'total_pris'     => $allSoldes->sum('solde_pris'),
            'total_reliquat' => $allSoldes->sum('solde_restant'),
        ];

        return view('rh.conges.soldes', compact(
            'agents', 'soldes', 'typesConge', 'services',
            'annee', 'annees', 'kpis', 'search', 'serviceId', 'typeCongeId'
        ));
    }

    public function exportSoldes(Request $request)
    {
        $annee      = $request->get('annee', now()->year);
        $search     = $request->get('search');
        $serviceId  = $request->get('service');
        $typeCongeId = $request->get('type_conge');

        $query = Agent::with([
            'service',
            'soldeConges' => fn($q) => $q->where('annee', $annee)->with('typeConge'),
        ])->where('statut_agent', 'Actif');

        if ($search) {
            $query->where(fn($q) => $q
                ->where('nom', 'like', "%{$search}%")
                ->orWhere('prenom', 'like', "%{$search}%")
                ->orWhere('matricule', 'like', "%{$search}%")
            );
        }
        if ($serviceId) {
            $query->where('id_service', $serviceId);
        }

        $agents     = $query->orderBy('nom')->get();
        $typesConge = TypeConge::deductible()->orderBy('libelle')->get();

        if ($typeCongeId) {
            $agents = $agents->filter(fn($a) => $a->soldeConges->where('id_type_conge', $typeCongeId)->isNotEmpty());
        }

        $headers = ['Matricule', 'Nom', 'Prénom', 'Service'];
        foreach ($typesConge as $t) {
            $headers[] = $t->libelle . ' — Initial';
            $headers[] = $t->libelle . ' — Pris';
            $headers[] = $t->libelle . ' — Reliquat';
        }

        $export = (new ExcelExport('Soldes Congés ' . $annee))->setHeaders($headers);

        foreach ($agents as $agent) {
            $agentSoldes = $agent->soldeConges->keyBy('id_type_conge');
            $row = [
                $agent->matricule,
                $agent->nom,
                $agent->prenom,
                $agent->service->nom_service ?? '—',
            ];
            foreach ($typesConge as $t) {
                $s = $agentSoldes->get($t->id_type_conge);
                $row[] = $s ? $s->solde_initial : 0;
                $row[] = $s ? $s->solde_pris    : 0;
                $row[] = $s ? $s->solde_restant  : 0;
            }
            $export->addRow($row);
        }

        return $export->download("soldes-conges-{$annee}");
    }

    public function ajusterSolde(Request $request, $id)
    {
        $request->validate([
            'solde_initial' => ['required', 'integer', 'min:0'],
            'solde_pris'    => ['required', 'integer', 'min:0'],
            'motif'         => ['required', 'string', 'max:300'],
        ]);

        $solde = SoldeConge::findOrFail($id);

        DB::transaction(function () use ($request, $solde) {
            $solde->solde_initial  = $request->solde_initial;
            $solde->solde_pris     = $request->solde_pris;
            $solde->solde_restant  = $request->solde_initial - $request->solde_pris;
            $solde->save();

            activity()
                ->causedBy(auth()->user())
                ->performedOn($solde)
                ->withProperties(['motif' => $request->motif])
                ->log('Ajustement manuel du solde de congé');
        });

        return back()->with('success', 'Solde ajusté avec succès.');
    }

    public function initSoldes(Request $request)
    {
        $request->validate(['annee' => ['required', 'integer', 'min:2020', 'max:2030']]);

        $annee      = (int) $request->annee;
        $agents     = Agent::where('statut_agent', 'Actif')->get();
        $typesConge = TypeConge::deductible()->get();
        $count      = 0;

        DB::transaction(function () use ($agents, $typesConge, $annee, &$count) {
            foreach ($agents as $agent) {
                foreach ($typesConge as $type) {
                    // Le congé de maternité ne concerne que les agentes féminines
                    if ($type->est_maternite && $agent->sexe !== 'F') {
                        continue;
                    }

                    SoldeConge::firstOrCreate(
                        [
                            'id_agent'      => $agent->id_agent,
                            'id_type_conge' => $type->id_type_conge,
                            'annee'         => $annee,
                        ],
                        [
                            'solde_initial'  => $type->duree_max ?? 30,
                            'solde_pris'     => 0,
                            'solde_restant'  => $type->duree_max ?? 30,
                        ]
                    );
                    $count++;
                }
            }
        });

        return back()->with('success', "{$count} soldes initialisés pour {$annee}.");
    }

    public function saisiePhysique()
    {
        $agents     = Agent::where('statut_agent', 'Actif')->orderBy('nom')->get();
        $typesConge = TypeConge::orderBy('libelle')->get();

        return view('rh.conges.saisie-physique', compact('agents', 'typesConge'));
    }

    public function storeSaisiePhysique(Request $request)
    {
        $request->validate([
            'id_agent'      => ['required', 'exists:agents,id_agent'],
            'id_type_conge' => ['required', 'exists:type_conges,id_type_conge'],
            'date_debut'    => ['required', 'date'],
            'date_fin'      => ['required', 'date', 'after_or_equal:date_debut'],
            'nbres_jours'   => ['required', 'integer', 'min:1'],
        ]);

        $agent     = Agent::findOrFail($request->id_agent);
        $typeConge = TypeConge::findOrFail($request->id_type_conge);

        if ($typeConge->est_maternite && $agent->sexe !== 'F') {
            return back()->withInput()
                ->with('error', 'Le congé de maternité ne peut être accordé qu\'à une agente féminine.');
        }

        DB::transaction(function () use ($request, $agent) {
            $demande = Demande::create([
                'id_agent'        => $agent->id_agent,
                'type_demande'    => 'Conge',
                'statut_demande'  => 'Approuvé',
                'date_traitement' => now(),
            ]);

            $conge = Conge::create([
                'id_demande'       => $demande->id_demande,
                'id_type_conge'    => $request->id_type_conge,
                'date_debut'       => $request->date_debut,
                'date_fin'         => $request->date_fin,
                'nbres_jours'      => $request->nbres_jours,
                'date_approbation' => now(),
            ]);

            $typeConge = TypeConge::find($request->id_type_conge);
            if ($typeConge?->deductible) {
                $solde = SoldeConge::where('id_agent', $agent->id_agent)
                    ->where('id_type_conge', $request->id_type_conge)
                    ->where('annee', now()->year)
                    ->first();

                if ($solde) {
                    $solde->deduire($request->nbres_jours);
                }
            }

            $agent->update(['statut_agent' => 'En_Conge']);

            activity()
                ->causedBy(auth()->user())
                ->performedOn($conge)
                ->withProperties(['agent' => $agent->nom_complet, 'jours' => $request->nbres_jours])
                ->log('Saisie physique de congé par RH');
        });

        return redirect()->route('rh.conges.index')
            ->with('success', "Congé de {$agent->nom_complet} enregistré.");
    }
}
