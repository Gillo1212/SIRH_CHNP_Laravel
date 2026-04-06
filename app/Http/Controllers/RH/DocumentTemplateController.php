<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\DemandeDocument;
use App\Models\Service;
use App\Services\DocumentGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

/**
 * Controller pour la génération des documents administratifs.
 * 
 * @author Gilbert - Mémoire M2 SIRH CHNP
 */
class DocumentTemplateController extends Controller
{
    public function __construct(
        protected DocumentGeneratorService $documentService
    ) {}

    /**
     * Affiche la page de sélection du type de document.
     */
    public function selectType(int $agentId)
    {
        $agent = Agent::with('service')->findOrFail($agentId);
        $categories = DemandeDocument::CATEGORIES_DOCUMENTS;
        $types = DemandeDocument::TYPES_DOCUMENTS;

        return view('rh.documents-admin.select-type', compact('agent', 'categories', 'types'));
    }

    /**
     * Affiche le formulaire de saisie des données spécifiques.
     */
    public function formulaire(int $agentId, string $type)
    {
        if (!array_key_exists($type, DemandeDocument::TYPES_DOCUMENTS)) {
            return redirect()->route('documents-admin.select-type', $agentId)
                ->with('error', 'Type de document invalide.');
        }

        $agent = Agent::with(['service', 'contratActif'])->findOrFail($agentId);
        $champs = $this->documentService->getChampsSpecifiques($type);
        $libelleType = DemandeDocument::TYPES_DOCUMENTS[$type];
        
        $services = Service::orderBy('nom_service')->get();
        $agents = Agent::actif()->orderBy('nom')->get();

        return view('rh.documents-admin.formulaire', compact(
            'agent', 
            'type', 
            'champs', 
            'libelleType',
            'services',
            'agents'
        ));
    }

    /**
     * Prévisualise le document avant génération.
     */
    public function preview(Request $request, int $agentId, string $type)
    {
        if (!array_key_exists($type, DemandeDocument::TYPES_DOCUMENTS)) {
            return redirect()->route('documents-admin.select-type', $agentId)
                ->with('error', 'Type de document invalide.');
        }

        $agent = Agent::with(['service', 'contratActif'])->findOrFail($agentId);

        $demande = new DemandeDocument([
            'agent_id'            => $agent->id_agent,
            'type_document'       => $type,
            'donnees_specifiques' => $request->except(['_token']),
            'statut'              => 'en_attente',
        ]);
        
        $demande->setRelation('agent', $agent);
        
        if ($request->filled('agent_remplacant_id')) {
            $demande->setRelation('agentRemplacant', Agent::find($request->agent_remplacant_id));
        }
        
        if ($request->filled('service_destination_id')) {
            $demande->setRelation('serviceDestination', Service::find($request->service_destination_id));
        }

        $data = $this->documentService->preparerDonnees($demande);
        $data['preview'] = true;
        $data['titre_document'] = DemandeDocument::TYPES_DOCUMENTS[$type];

        $typeView = $this->resolveTemplateType($type);
        $viewName = 'rh.documents-admin.templates.' . str_replace('_', '-', $typeView);

        if (!View::exists($viewName)) {
            return redirect()->route('documents-admin.select-type', $agentId)
                ->with('error', 'Template non disponible pour ce type de document.');
        }

        return view($viewName, $data);
    }

    /**
     * Génère et enregistre le document final.
     */
    public function generer(Request $request, int $agentId, string $type)
    {
        if (!array_key_exists($type, DemandeDocument::TYPES_DOCUMENTS)) {
            return redirect()->route('documents-admin.select-type', $agentId)
                ->with('error', 'Type de document invalide.');
        }

        $agent = Agent::findOrFail($agentId);

        $demande = DemandeDocument::create([
            'agent_id'               => $agent->id_agent,
            'type_document'          => $type,
            'donnees_specifiques'    => $request->except(['_token']),
            'motif'                  => $request->input('motif_demande', 'Demande RH'),
            'statut'                 => 'pret',
            'traite_par'             => Auth::id(),
            'date_traitement'        => now(),
            'agent_remplacant_id'    => $request->input('agent_remplacant_id'),
            'service_destination_id' => $request->input('service_destination_id'),
            'date_debut_validite'    => $request->input('date_debut'),
            'date_fin_validite'      => $request->input('date_fin'),
        ]);

        $demande->update([
            'numero_reference' => $demande->genererNumeroReference(),
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($demande)
            ->withProperties(['type' => $type, 'agent' => $agent->nom_complet])
            ->log('Document généré : ' . DemandeDocument::TYPES_DOCUMENTS[$type]);

        return redirect()->route('documents-admin.show-generated', $demande->id)
            ->with('success', 'Document généré avec succès. Référence : ' . $demande->numero_reference);
    }

    /**
     * Affiche un document généré.
     */
    public function showGenerated(int $id)
    {
        $demande = DemandeDocument::with(['agent.service', 'agentRemplacant', 'serviceDestination', 'traitePar'])
            ->findOrFail($id);

        if (!in_array($demande->statut, ['pret', 'rejete'])) {
            return redirect()->route('demandes-docs.show', $id)
                ->with('error', 'Ce document n\'est pas encore généré.');
        }

        $data = $this->documentService->preparerDonnees($demande);
        $data['preview'] = false;
        $data['titre_document'] = $demande->libelleType;

        $typeView = $this->resolveTemplateType($demande->type_document);
        $viewName = 'rh.documents-admin.templates.' . str_replace('_', '-', $typeView);
        
        if (!View::exists($viewName)) {
            return view('rh.documents-admin.templates.generic', $data);
        }

        return view($viewName, $data);
    }

    /**
     * Historique des documents générés.
     */
    public function historique(Request $request)
    {
        $query = DemandeDocument::with(['agent.service', 'traitePar'])
            ->whereIn('statut', ['pret', 'rejete'])
            ->latest('date_traitement');

        if ($request->filled('type')) {
            $query->where('type_document', $request->type);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        
        if ($request->filled('agent')) {
            $query->whereHas('agent', function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->agent . '%')
                  ->orWhere('prenom', 'like', '%' . $request->agent . '%')
                  ->orWhere('matricule', 'like', '%' . $request->agent . '%');
            });
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date_traitement', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('date_traitement', '<=', $request->date_fin);
        }

        $documents = $query->paginate(20)->withQueryString();
        $types = DemandeDocument::TYPES_DOCUMENTS;

        return view('rh.documents-admin.historique', compact('documents', 'types'));
    }

    /**
     * Affiche le formulaire pré-rempli pour modifier un document généré.
     */
    public function modifier(int $id)
    {
        $demande = DemandeDocument::with(['agent.service', 'agent.contratActif'])->findOrFail($id);
        $agent   = $demande->agent;
        $type    = $demande->type_document;

        $champs      = $this->documentService->getChampsSpecifiques($type);
        $libelleType = DemandeDocument::TYPES_DOCUMENTS[$type];
        $services    = Service::orderBy('nom_service')->get();
        $agents      = Agent::actif()->orderBy('nom')->get();

        // Injecte les valeurs existantes comme defaults
        foreach ($champs as $nom => $config) {
            $champs[$nom]['default'] = $demande->donnees_specifiques[$nom] ?? $config['default'] ?? null;
        }

        return view('rh.documents-admin.formulaire', [
            'agent'       => $agent,
            'type'        => $type,
            'champs'      => $champs,
            'libelleType' => $libelleType,
            'services'    => $services,
            'agents'      => $agents,
            'sourceId'    => $id,
        ]);
    }

    /**
     * Met à jour les données d'un document et le régénère.
     */
    public function update(Request $request, int $id)
    {
        $demande = DemandeDocument::with('agent')->findOrFail($id);

        $demande->update([
            'donnees_specifiques' => $request->except(['_token', '_method']),
            'traite_par'          => Auth::id(),
            'date_traitement'     => now(),
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($demande)
            ->withProperties(['type' => $demande->type_document, 'agent' => $demande->agent->nom_complet])
            ->log('Document modifié : ' . $demande->libelleType);

        return redirect()->route('documents-admin.show-generated', $id)
            ->with('success', 'Document mis à jour avec succès (réf. ' . $demande->numero_reference . ').');
    }

    /**
     * Annule (invalide) un document généré.
     */
    public function annuler(int $id)
    {
        $demande = DemandeDocument::findOrFail($id);
        $demande->update(['statut' => 'rejete']);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($demande)
            ->withProperties(['reference' => $demande->numero_reference])
            ->log('Document annulé : ' . $demande->libelleType);

        return redirect()->route('documents-admin.historique')
            ->with('success', 'Document ' . $demande->numero_reference . ' annulé avec succès.');
    }

    /**
     * Résout le type de document vers le template à utiliser.
     * Permet de fusionner des types partageant le même template.
     */
    private function resolveTemplateType(string $type): string
    {
        return match($type) {
            'attestation_travail' => 'certificat_travail',
            default               => $type,
        };
    }

    /**
     * Duplique un document existant.
     */
    public function duplicate(int $id)
    {
        $demande = DemandeDocument::with('agent')->findOrFail($id);

        return redirect()->route('documents-admin.formulaire', [
            'agentId' => $demande->agent_id,
            'type'    => $demande->type_document,
        ])->with('info', 'Vous pouvez modifier les données avant de générer un nouveau document.');
    }
}
