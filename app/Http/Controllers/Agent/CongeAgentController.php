<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agent\StoreCongeRequest;
use App\Models\Demande;
use App\Models\Conge;
use App\Models\Service;
use App\Models\SoldeConge;
use App\Models\TypeConge;
use App\Notifications\CongeDemandeNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
            'jours_pris' => $demandes->where('statut_demande', 'Approuvé')->sum(fn($d) => $d->conge?->nbres_jours ?? 0),
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

        // Bloquer si l'agent est déjà en congé
        if ($agent->statut_agent === 'En_Conge') {
            return redirect()->route('agent.conges.index')
                ->with('error', 'Vous êtes actuellement en congé. Vous ne pouvez pas soumettre une nouvelle demande tant que vos jours de congé ne sont pas écoulés. Pour une demande de rallonge, veuillez vous rapprocher du service RH en fournissant un justificatif.');
        }

        $typesConge = TypeConge::all()
            ->when($agent->sexe !== 'F', fn($c) => $c->filter(fn($t) => !$t->est_maternite)->values());

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
    public function store(StoreCongeRequest $request)
    {
        $agent = Auth::user()->agent;

        if (!$agent) {
            return redirect()->route('agent.dashboard')
                ->with('error', 'Votre dossier agent n\'est pas encore complet.');
        }

        // Bloquer si l'agent est déjà en congé
        if ($agent->statut_agent === 'En_Conge') {
            return redirect()->route('agent.conges.index')
                ->with('error', 'Vous êtes actuellement en congé. Nouvelle demande impossible tant que vos jours ne sont pas écoulés. Pour une rallonge, contactez le service RH avec un justificatif.');
        }

        $validated = $request->validated();

        $dateDebut = Carbon::parse($validated['date_debut']);
        $dateFin   = Carbon::parse($validated['date_fin']);
        $nbJours   = $dateDebut->diffInDays($dateFin) + 1;

        $typeConge = TypeConge::findOrFail($validated['id_type_conge']);

        // Congé de maternité réservé aux agentes féminines
        if ($typeConge->est_maternite && $agent->sexe !== 'F') {
            return back()->withInput()
                ->with('error', 'Le congé de maternité est réservé aux agentes féminines.');
        }

        // Certificat médical obligatoire pour congé de maternité
        if ($typeConge->est_maternite && !$request->hasFile('justificatif')) {
            return back()->withInput()
                ->with('error', 'Le certificat médical est obligatoire pour un congé de maternité.');
        }

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

        // Stocker le justificatif avant la transaction
        $justificatifPath = null;
        if ($request->hasFile('justificatif')) {
            $justificatifPath = $request->file('justificatif')->store('conges-justificatifs', 'private');
        }

        DB::transaction(function () use ($agent, $validated, $nbJours, $justificatifPath) {
            // Créer la demande parente
            $demande = Demande::create([
                'id_agent'      => $agent->id_agent,
                'type_demande'  => 'Conge',
                'statut_demande' => 'En_attente',
            ]);

            // Créer le congé associé
            Conge::create([
                'id_demande'      => $demande->id_demande,
                'id_type_conge'   => $validated['id_type_conge'],
                'date_debut'      => $validated['date_debut'],
                'date_fin'        => $validated['date_fin'],
                'nbres_jours'     => $nbJours,
                'justificatif_path' => $justificatifPath,
            ]);
        });

        // Notifier le manager du service (hors transaction)
        try {
            $demande->load('conge');
            $service = Service::where('id_service', $agent->id_service)->first();
            if ($service && $service->manager) {
                $service->manager->notify(new CongeDemandeNotification($demande));
            }
        } catch (\Throwable $e) {
            \Log::warning('Notification congé manager échouée : ' . $e->getMessage());
        }

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

    /**
     * Télécharger le justificatif (certificat médical)
     */
    public function downloadJustificatif($id)
    {
        $agent = Auth::user()->agent;

        $demande = Demande::with('conge')
            ->where('id_agent', $agent->id_agent)
            ->where('id_demande', $id)
            ->where('type_demande', 'Conge')
            ->firstOrFail();

        $conge = $demande->conge;

        if (!$conge || !$conge->justificatif_path) {
            abort(404, 'Aucun justificatif disponible.');
        }

        return Storage::disk('private')->download($conge->justificatif_path, 'certificat_medical_conge_' . $id);
    }
}
