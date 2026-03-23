<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Demande;
use App\Models\Conge;
use App\Models\SoldeConge;
use App\Models\TypeConge;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CongeAgentController extends Controller
{
    /**
     * Mes congés — solde + historique
     */
    public function index()
    {
        $agent = Auth::user()->agent;

        if (!$agent) {
            return redirect()->route('agent.dashboard')
                ->with('error', 'Votre dossier agent n\'est pas encore complet.');
        }

        // Historique des demandes de congé
        $demandes = Demande::with(['conge.typeConge'])
            ->where('id_agent', $agent->id_agent)
            ->where('type_demande', 'Conge')
            ->orderByDesc('created_at')
            ->get();

        // Soldes de congé pour l'année en cours
        $soldes = SoldeConge::with('typeConge')
            ->where('id_agent', $agent->id_agent)
            ->where('annee', date('Y'))
            ->get();

        // Stats rapides
        $stats = [
            'en_attente' => $demandes->where('statut_demande', 'En_attente')->count(),
            'validees'   => $demandes->where('statut_demande', 'Validé')->count(),
            'approuvees' => $demandes->where('statut_demande', 'Approuvé')->count(),
            'rejetees'   => $demandes->where('statut_demande', 'Rejeté')->count(),
        ];

        return view('agent.conges.index', compact('demandes', 'soldes', 'stats', 'agent'));
    }

    /**
     * Formulaire de demande de congé
     */
    public function create()
    {
        $agent = Auth::user()->agent;

        if (!$agent) {
            return redirect()->route('agent.dashboard')
                ->with('error', 'Votre dossier agent n\'est pas encore complet.');
        }

        $typesConge = TypeConge::all();

        // Soldes disponibles pour l'année en cours
        $soldes = SoldeConge::with('typeConge')
            ->where('id_agent', $agent->id_agent)
            ->where('annee', date('Y'))
            ->get()
            ->keyBy('id_type_conge');

        return view('agent.conges.create', compact('typesConge', 'soldes', 'agent'));
    }

    /**
     * Soumettre la demande de congé
     */
    public function store(Request $request)
    {
        $agent = Auth::user()->agent;

        if (!$agent) {
            return redirect()->route('agent.dashboard')
                ->with('error', 'Votre dossier agent n\'est pas encore complet.');
        }

        $validated = $request->validate([
            'id_type_conge' => 'required|exists:type_conges,id_type_conge',
            'date_debut'    => 'required|date|after_or_equal:today',
            'date_fin'      => 'required|date|after_or_equal:date_debut',
            'motif'         => 'nullable|string|max:500',
        ], [
            'id_type_conge.required' => 'Veuillez sélectionner un type de congé.',
            'date_debut.after_or_equal' => 'La date de début ne peut pas être dans le passé.',
            'date_fin.after_or_equal'   => 'La date de fin doit être après la date de début.',
        ]);

        $dateDebut = Carbon::parse($validated['date_debut']);
        $dateFin   = Carbon::parse($validated['date_fin']);
        $nbJours   = $dateDebut->diffInDays($dateFin) + 1;

        $typeConge = TypeConge::findOrFail($validated['id_type_conge']);

        // Vérification du solde pour les types déductibles
        if ($typeConge->deductible) {
            $solde = SoldeConge::where('id_agent', $agent->id_agent)
                ->where('id_type_conge', $validated['id_type_conge'])
                ->where('annee', date('Y'))
                ->first();

            if (!$solde) {
                return back()->withInput()
                    ->with('error', 'Aucun solde de congé disponible pour ce type.');
            }

            if (!$solde->aSoldeSuffisant($nbJours)) {
                return back()->withInput()
                    ->with('error', "Solde insuffisant. Vous avez {$solde->solde_restant} jour(s) disponible(s), mais vous demandez {$nbJours} jour(s).");
            }
        }

        // Vérifier si le nb de jours ne dépasse pas le droit
        if ($typeConge->nb_jours_droit > 0 && $nbJours > $typeConge->nb_jours_droit) {
            return back()->withInput()
                ->with('error', "La durée demandée ({$nbJours} jours) dépasse le maximum autorisé ({$typeConge->nb_jours_droit} jours) pour ce type de congé.");
        }

        DB::transaction(function () use ($agent, $validated, $nbJours) {
            // Créer la demande parente
            $demande = Demande::create([
                'id_agent'      => $agent->id_agent,
                'type_demande'  => 'Conge',
                'statut_demande' => 'En_attente',
            ]);

            // Créer le congé associé
            Conge::create([
                'id_demande'    => $demande->id_demande,
                'id_type_conge' => $validated['id_type_conge'],
                'date_debut'    => $validated['date_debut'],
                'date_fin'      => $validated['date_fin'],
                'nbres_jours'   => $nbJours,
            ]);
        });

        return redirect()->route('agent.conges.index')
            ->with('success', "Votre demande de congé ({$nbJours} jour(s)) a été soumise avec succès. Elle est en attente de validation.");
    }

    /**
     * Détail d'une demande de congé
     */
    public function show($id)
    {
        $agent = Auth::user()->agent;

        $demande = Demande::with(['conge.typeConge'])
            ->where('id_agent', $agent->id_agent)
            ->where('id_demande', $id)
            ->where('type_demande', 'Conge')
            ->firstOrFail();

        return view('agent.conges.show', compact('demande'));
    }
}
