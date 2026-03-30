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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CongeRHController extends Controller
{
    public function index(Request $request)
    {
        $query = Demande::with(['agent.service', 'conge.typeConge'])
            ->where('type_demande', 'Congé')
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
            'total'      => Demande::where('type_demande', 'Congé')->count(),
            'en_attente' => Demande::where('type_demande', 'Congé')->where('statut_demande', 'En attente')->count(),
            'valides'    => Demande::where('type_demande', 'Congé')->where('statut_demande', 'Validé Manager')->count(),
            'approuves'  => Demande::where('type_demande', 'Congé')->where('statut_demande', 'Approuvé')->count(),
            'rejetes'    => Demande::where('type_demande', 'Congé')->where('statut_demande', 'Refusé')->count(),
        ];

        $services   = Service::orderBy('nom_service')->get();
        $typesConge = TypeConge::orderBy('libelle')->get();
        $agents     = Agent::orderBy('nom')->get();

        return view('rh.conges.index', compact('demandes', 'stats', 'services', 'typesConge', 'agents'));
    }

    public function pending()
    {
        $pending = Demande::with(['agent.service', 'conge.typeConge'])
            ->where('type_demande', 'Congé')
            ->where('statut_demande', 'Validé Manager')
            ->orderBy('created_at')
            ->paginate(20);

        return view('rh.conges.pending', compact('pending'));
    }

    public function show($id)
    {
        $demande = Demande::with(['agent.service', 'conge.typeConge'])->findOrFail($id);

        return view('rh.conges.show', compact('demande'));
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
                'statut_demande'  => 'Refusé',
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

    public function soldes(Request $request)
    {
        $annee = $request->get('annee', now()->year);

        $agents = Agent::with(['soldeConges' => fn($q) => $q->where('annee', $annee)->with('typeConge')])
            ->where('statut_agent', 'Actif')
            ->orderBy('nom')
            ->get();

        $soldes = $agents->mapWithKeys(fn($a) => [
            $a->id_agent => $a->soldeConges->keyBy('id_type_conge'),
        ]);

        $typesConge = TypeConge::deductible()->orderBy('libelle')->get();
        $annees     = range(now()->year - 2, now()->year + 1);

        return view('rh.conges.soldes', compact('agents', 'soldes', 'typesConge', 'annee', 'annees'));
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

        $agent = Agent::findOrFail($request->id_agent);

        DB::transaction(function () use ($request, $agent) {
            $demande = Demande::create([
                'id_agent'        => $agent->id_agent,
                'type_demande'    => 'Congé',
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
