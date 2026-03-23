<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Demande;
use App\Models\Conge;
use App\Models\SoldeConge;
use App\Models\TypeConge;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CongeRHController extends Controller
{
    /**
     * Historique complet de tous les congés
     */
    public function index(Request $request)
    {
        $query = Demande::with(['conge.typeConge', 'agent.service'])
            ->where('type_demande', 'Conge')
            ->orderByDesc('created_at');

        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut_demande', $request->statut);
        }
        if ($request->filled('type_conge')) {
            $query->whereHas('conge', fn($q) => $q->where('id_type_conge', $request->type_conge));
        }
        if ($request->filled('service')) {
            $query->whereHas('agent', fn($q) => $q->where('id_service', $request->service));
        }
        if ($request->filled('search')) {
            $terme = $request->search;
            $query->whereHas('agent', function ($q) use ($terme) {
                $q->where('nom', 'like', "%{$terme}%")
                  ->orWhere('prenom', 'like', "%{$terme}%")
                  ->orWhere('matricule', 'like', "%{$terme}%");
            });
        }

        $demandes = $query->paginate(20)->withQueryString();

        // Stats
        $stats = [
            'total'       => Demande::where('type_demande', 'Conge')->count(),
            'en_attente'  => Demande::where('type_demande', 'Conge')->where('statut_demande', 'En_attente')->count(),
            'valides'     => Demande::where('type_demande', 'Conge')->where('statut_demande', 'Validé')->count(),
            'approuves'   => Demande::where('type_demande', 'Conge')->where('statut_demande', 'Approuvé')->count(),
            'rejetes'     => Demande::where('type_demande', 'Conge')->where('statut_demande', 'Rejeté')->count(),
        ];

        $typesConge = TypeConge::all();
        $services   = \App\Models\Service::all();

        return view('rh.conges.index', compact('demandes', 'stats', 'typesConge', 'services'));
    }

    /**
     * Demandes validées par Manager en attente d'approbation finale RH
     */
    public function pending()
    {
        $pending = Demande::with(['conge.typeConge', 'agent.service'])
            ->where('type_demande', 'Conge')
            ->where('statut_demande', 'Validé')
            ->orderBy('date_traitement')
            ->get();

        return view('rh.conges.pending', compact('pending'));
    }

    /**
     * Détail d'une demande de congé (RH)
     */
    public function show($id)
    {
        $demande = Demande::with(['conge.typeConge', 'agent.service'])
            ->where('type_demande', 'Conge')
            ->findOrFail($id);

        // Solde actuel de l'agent pour ce type
        $solde = null;
        if ($demande->conge) {
            $solde = SoldeConge::where('id_agent', $demande->id_agent)
                ->where('id_type_conge', $demande->conge->id_type_conge)
                ->where('annee', date('Y'))
                ->first();
        }

        // Historique des congés de cet agent
        $historique = Demande::with('conge.typeConge')
            ->where('id_agent', $demande->id_agent)
            ->where('type_demande', 'Conge')
            ->where('id_demande', '!=', $id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('rh.conges.show', compact('demande', 'solde', 'historique'));
    }

    /**
     * Approuver définitivement un congé + déduire du solde
     */
    public function approuver(Request $request, $id)
    {
        $demande = Demande::with(['conge.typeConge', 'agent'])
            ->where('type_demande', 'Conge')
            ->where('statut_demande', 'Validé')
            ->findOrFail($id);

        DB::transaction(function () use ($demande) {
            // 1. Approuver la demande
            $demande->update([
                'statut_demande'  => 'Approuvé',
                'date_traitement' => now(),
            ]);

            // 2. Mettre à jour la date d'approbation sur le congé
            $demande->conge->update([
                'date_approbation' => now()->toDateString(),
            ]);

            // 3. Déduire du solde si le type est déductible
            if ($demande->conge->typeConge && $demande->conge->typeConge->deductible) {
                $solde = SoldeConge::where('id_agent', $demande->id_agent)
                    ->where('id_type_conge', $demande->conge->id_type_conge)
                    ->where('annee', date('Y'))
                    ->first();

                if ($solde) {
                    $solde->deduireJours($demande->conge->nbres_jours);
                }
            }
        });

        $nomAgent = $demande->agent->nom_complet ?? 'l\'agent';
        $nbJours  = $demande->conge->nbres_jours;

        return redirect()->route('rh.conges.pending')
            ->with('success', "Le congé de {$nomAgent} ({$nbJours} jour(s)) a été approuvé. Le solde a été mis à jour.");
    }

    /**
     * Rejeter une demande de congé (RH)
     */
    public function rejeter(Request $request, $id)
    {
        $request->validate([
            'motif_refus' => 'required|string|min:10|max:500',
        ], [
            'motif_refus.required' => 'Veuillez indiquer le motif du rejet.',
            'motif_refus.min'      => 'Le motif doit contenir au moins 10 caractères.',
        ]);

        $demande = Demande::with(['conge', 'agent'])
            ->where('type_demande', 'Conge')
            ->whereIn('statut_demande', ['En_attente', 'Validé'])
            ->findOrFail($id);

        $demande->update([
            'statut_demande'  => 'Rejeté',
            'motif_refus'     => $request->motif_refus,
            'date_traitement' => now(),
        ]);

        $nomAgent = $demande->agent->nom_complet ?? 'l\'agent';

        return redirect()->route('rh.conges.index')
            ->with('success', "La demande de congé de {$nomAgent} a été rejetée.");
    }

    /**
     * Gestion des soldes de congés
     */
    public function soldes(Request $request)
    {
        $annee = $request->get('annee', date('Y'));

        $soldes = SoldeConge::with(['agent.service', 'typeConge'])
            ->where('annee', $annee)
            ->orderBy('id_agent')
            ->get()
            ->groupBy('id_agent');

        $agents    = Agent::with('service')->whereHas('user')->orderBy('nom')->get();
        $typesConge = TypeConge::all();

        // Années disponibles
        $annees = range(date('Y') - 2, date('Y') + 1);

        return view('rh.conges.soldes', compact('soldes', 'agents', 'typesConge', 'annee', 'annees'));
    }

    /**
     * Initialiser les soldes d'un agent pour une année
     */
    public function initSoldes(Request $request)
    {
        $request->validate([
            'id_agent'  => 'required|exists:agents,id_agent',
            'annee'     => 'required|integer|min:2020|max:2030',
        ]);

        $agent     = Agent::findOrFail($request->id_agent);
        $typesConge = TypeConge::where('deductible', true)->get();

        DB::transaction(function () use ($agent, $request, $typesConge) {
            foreach ($typesConge as $type) {
                SoldeConge::firstOrCreate(
                    [
                        'id_agent'      => $agent->id_agent,
                        'id_type_conge' => $type->id_type_conge,
                        'annee'         => $request->annee,
                    ],
                    [
                        'solde_initial'  => $type->nb_jours_droit,
                        'solde_pris'     => 0,
                        'solde_restant'  => $type->nb_jours_droit,
                    ]
                );
            }
        });

        return back()->with('success', "Soldes initialisés pour {$agent->nom_complet} — année {$request->annee}.");
    }

    /**
     * Formulaire de saisie physique (agent qui vient au bureau)
     */
    public function saisiePhysique()
    {
        $agents    = Agent::with('service')->where('statut', 'actif')->orderBy('nom')->get();
        $typesConge = TypeConge::all();

        return view('rh.conges.saisie-physique', compact('agents', 'typesConge'));
    }

    /**
     * Enregistrer un congé saisi physiquement par RH
     */
    public function storeSaisiePhysique(Request $request)
    {
        $validated = $request->validate([
            'id_agent'      => 'required|exists:agents,id_agent',
            'id_type_conge' => 'required|exists:type_conges,id_type_conge',
            'date_debut'    => 'required|date',
            'date_fin'      => 'required|date|after_or_equal:date_debut',
        ], [
            'id_agent.required'      => 'Veuillez sélectionner un agent.',
            'id_type_conge.required' => 'Veuillez sélectionner un type de congé.',
            'date_fin.after_or_equal' => 'La date de fin doit être après la date de début.',
        ]);

        $dateDebut = Carbon::parse($validated['date_debut']);
        $dateFin   = Carbon::parse($validated['date_fin']);
        $nbJours   = $dateDebut->diffInDays($dateFin) + 1;

        $agent     = Agent::findOrFail($validated['id_agent']);
        $typeConge = TypeConge::findOrFail($validated['id_type_conge']);

        // Vérification solde si déductible
        if ($typeConge->deductible) {
            $solde = SoldeConge::where('id_agent', $agent->id_agent)
                ->where('id_type_conge', $validated['id_type_conge'])
                ->where('annee', $dateDebut->year)
                ->first();

            if ($solde && !$solde->aSoldeSuffisant($nbJours)) {
                return back()->withInput()
                    ->with('error', "Solde insuffisant pour {$agent->nom_complet}. Disponible : {$solde->solde_restant} jour(s), demandé : {$nbJours} jour(s).");
            }
        }

        DB::transaction(function () use ($agent, $validated, $nbJours, $typeConge) {
            // Créer demande directement approuvée (saisie physique = RH approuve d'emblée)
            $demande = Demande::create([
                'id_agent'        => $agent->id_agent,
                'type_demande'    => 'Conge',
                'statut_demande'  => 'Approuvé',
                'date_traitement' => now(),
            ]);

            Conge::create([
                'id_demande'       => $demande->id_demande,
                'id_type_conge'    => $validated['id_type_conge'],
                'date_debut'       => $validated['date_debut'],
                'date_fin'         => $validated['date_fin'],
                'nbres_jours'      => $nbJours,
                'date_approbation' => now()->toDateString(),
            ]);

            // Déduire du solde si déductible
            if ($typeConge->deductible) {
                $solde = SoldeConge::where('id_agent', $agent->id_agent)
                    ->where('id_type_conge', $validated['id_type_conge'])
                    ->where('annee', Carbon::parse($validated['date_debut'])->year)
                    ->first();

                if ($solde) {
                    $solde->deduireJours($nbJours);
                }
            }
        });

        return redirect()->route('rh.conges.index')
            ->with('success', "Congé de {$agent->nom_complet} enregistré avec succès ({$nbJours} jour(s) du {$validated['date_debut']} au {$validated['date_fin']}).");
    }
}
