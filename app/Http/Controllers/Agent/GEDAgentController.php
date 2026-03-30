<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DossierAgent;
use Illuminate\Support\Facades\Auth;

class GEDAgentController extends Controller
{
    /**
     * Dossier personnel de l'agent (son enveloppe)
     */
    public function index()
    {
        $agent   = Auth::user()->agent;
        $dossier = $agent?->dossier;

        if (!$dossier) {
            return view('agent.documents.index', [
                'dossier'   => null,
                'documents' => collect(),
                'parType'   => collect(),
                'agent'     => $agent,
            ]);
        }

        // L'agent ne voit que ses docs Public et Interne
        $documents = Document::with('uploadePar')
            ->where('id_dossier', $dossier->id_dossier)
            ->whereIn('niveau_confidentialite', ['Public', 'Interne'])
            ->where('statut_document', 'Actif')
            ->orderBy('type_document')
            ->orderBy('date_creation', 'desc')
            ->get();

        $parType = $documents->groupBy('type_document');
        $types   = Document::TYPES;

        // Log de consultation (Audit CID)
        activity('documents')
            ->causedBy(Auth::user())
            ->withProperties(['action' => 'consultation_dossier', 'agent' => $agent->nom_complet])
            ->log("Consultation dossier personnel : {$dossier->reference}");

        return view('agent.documents.index', compact('agent', 'dossier', 'documents', 'parType', 'types'));
    }

    /**
     * Visualiser un document
     */
    public function show(int $id)
    {
        $agent    = Auth::user()->agent;
        $dossier  = $agent?->dossier;

        $document = Document::with(['dossier', 'uploadePar'])->findOrFail($id);

        // Vérification : le document appartient bien à cet agent
        if (!$dossier || $document->id_dossier !== $dossier->id_dossier) {
            abort(403, 'Ce document ne vous appartient pas.');
        }

        // L'agent ne peut pas voir les documents confidentiels/secrets
        if (in_array($document->niveau_confidentialite, ['Confidentiel', 'Secret'])) {
            abort(403, 'Ce document est confidentiel et non accessible.');
        }

        // Audit trail
        activity('documents')
            ->causedBy(Auth::user())
            ->performedOn($document)
            ->withProperties(['action' => 'consultation', 'agent' => $agent->nom_complet])
            ->log("Agent consulte son document : {$document->titre}");

        return view('agent.documents.show', compact('document', 'agent'));
    }

    /**
     * Prévisualisation inline (PDF, images)
     */
    public function preview(int $id)
    {
        $agent   = Auth::user()->agent;
        $dossier = $agent?->dossier;
        $document = Document::findOrFail($id);

        if (!$dossier || $document->id_dossier !== $dossier->id_dossier) {
            abort(403);
        }
        if (in_array($document->niveau_confidentialite, ['Confidentiel', 'Secret'])) {
            abort(403);
        }

        $chemin = storage_path('app/public/' . $document->document_url);
        if (!file_exists($chemin)) {
            abort(404);
        }

        $mimeType = mime_content_type($chemin) ?: 'application/octet-stream';
        return response()->file($chemin, [
            'Content-Type'        => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $document->nom_fichier . '"',
        ]);
    }

    /**
     * Téléchargement
     */
    public function download(int $id)
    {
        $agent   = Auth::user()->agent;
        $dossier = $agent?->dossier;
        $document = Document::findOrFail($id);

        if (!$dossier || $document->id_dossier !== $dossier->id_dossier) {
            abort(403);
        }
        if (in_array($document->niveau_confidentialite, ['Confidentiel', 'Secret'])) {
            abort(403);
        }

        $chemin = storage_path('app/public/' . $document->document_url);
        if (!file_exists($chemin)) {
            abort(404);
        }

        activity('documents')
            ->causedBy(Auth::user())
            ->performedOn($document)
            ->withProperties(['action' => 'téléchargement'])
            ->log("Téléchargement : {$document->titre}");

        return response()->download($chemin, $document->titre . '.' . $document->extension);
    }
}
