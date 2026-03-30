<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DossierAgent;
use App\Models\Etagere;
use App\Models\Agent;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GEDController extends Controller
{
    // =====================================================================
    // DASHBOARD GED
    // =====================================================================

    public function index(Request $request)
    {
        $stats = [
            'total_documents'  => Document::where('statut_document', 'Actif')->count(),
            'total_dossiers'   => DossierAgent::where('statut_da', 'Actif')->count(),
            'total_etageres'   => Etagere::where('actif', true)->count(),
            'docs_archives'    => Document::where('statut_document', 'Archivé')->count(),
            'docs_confidentiels'=> Document::whereIn('niveau_confidentialite', ['Confidentiel', 'Secret'])
                                            ->where('statut_document', 'Actif')->count(),
            'docs_recents'     => Document::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        // Répartition par type
        $parType = Document::where('statut_document', 'Actif')
            ->selectRaw('type_document, COUNT(*) as total')
            ->groupBy('type_document')
            ->pluck('total', 'type_document');

        // Répartition par confidentialité
        $parConfidentialite = Document::where('statut_document', 'Actif')
            ->selectRaw('niveau_confidentialite, COUNT(*) as total')
            ->groupBy('niveau_confidentialite')
            ->pluck('total', 'niveau_confidentialite');

        // Documents récents
        $docsRecents = Document::with(['dossier.agent', 'uploadePar'])
            ->where('statut_document', 'Actif')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Étagères avec stats
        $etageres = Etagere::with(['service', 'dossiers'])
            ->where('actif', true)
            ->withCount('dossiers')
            ->get();

        return view('rh.documents.index', compact(
            'stats', 'parType', 'parConfidentialite', 'docsRecents', 'etageres'
        ));
    }

    // =====================================================================
    // LISTE DES DOSSIERS
    // =====================================================================

    public function dossiers(Request $request)
    {
        // On part des AGENTS (pas des dossiers) pour afficher tous les agents,
        // même ceux qui n'ont pas encore de dossier GED.
        $query = Agent::with([
            'service',
            'dossier' => fn($q) => $q->with(['etagere'])
                                     ->withCount(['documents', 'documentsActifs']),
        ])->orderBy('nom')->orderBy('prenom');

        if ($request->filled('service')) {
            $query->where('id_service', $request->service);
        }
        if ($request->filled('statut')) {
            if ($request->statut === 'sans_dossier') {
                $query->doesntHave('dossier');
            } else {
                $query->whereHas('dossier', fn($q) => $q->where('statut_da', $request->statut));
            }
        }
        if ($request->filled('q')) {
            $terme = $request->q;
            $query->where(function ($q) use ($terme) {
                $q->where('nom', 'like', "%{$terme}%")
                  ->orWhere('prenom', 'like', "%{$terme}%")
                  ->orWhere('matricule', 'like', "%{$terme}%");
            });
        }

        $agents = $query->get();
        $services = Service::orderBy('nom_service')->get();

        $dossiersByService = $agents->groupBy(
            fn($a) => $a->service?->nom_service ?? 'Sans service affecté'
        )->sortKeys();

        return view('rh.documents.dossiers', compact('agents', 'dossiersByService', 'services'));
    }

    // =====================================================================
    // DÉTAIL D'UN DOSSIER (ENVELOPPE)
    // =====================================================================

    public function dossierShow(int $id)
    {
        $dossier = DossierAgent::with(['agent.service', 'agent.contratActif', 'etagere.service'])
            ->findOrFail($id);

        // Documents filtrés selon le rôle
        $roleUser = Auth::user()->getRoleNames()->first();
        $documents = Document::with('uploadePar')
            ->where('id_dossier', $id)
            ->visiblePour($roleUser)
            ->orderBy('type_document')
            ->orderBy('date_creation', 'desc')
            ->get()
            ->groupBy('type_document');

        // Stats du dossier
        $statsDoc = [
            'actifs'   => Document::where('id_dossier', $id)->where('statut_document', 'Actif')->count(),
            'archives' => Document::where('id_dossier', $id)->where('statut_document', 'Archivé')->count(),
            'total'    => Document::where('id_dossier', $id)->count(),
        ];

        // Audit trail pour ce dossier
        $activites = \Spatie\Activitylog\Models\Activity::query()
            ->where('subject_type', Document::class)
            ->whereIn('subject_id',
                Document::where('id_dossier', $id)->pluck('id_document')
            )
            ->with('causer')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('rh.documents.dossier-show', compact('dossier', 'documents', 'statsDoc', 'activites'));
    }

    // =====================================================================
    // FORMULAIRE UPLOAD DOCUMENT
    // =====================================================================

    public function create(Request $request)
    {
        $dossierId = $request->query('dossier');
        $dossier   = $dossierId ? DossierAgent::with('agent')->find($dossierId) : null;
        $agents    = Agent::with('service')->where('statut_agent', 'Actif')->orderBy('nom')->get();
        $types     = Document::TYPES;
        $niveaux   = Document::NIVEAUX_CONFIDENTIALITE;

        return view('rh.documents.create', compact('dossier', 'agents', 'types', 'niveaux'));
    }

    // =====================================================================
    // STORE DOCUMENT
    // =====================================================================

    public function store(Request $request)
    {
        $validated = $request->validate([
            'agent_id'           => 'required|exists:agents,id_agent',
            'titre'              => 'required|string|max:200',
            'type_document'      => 'required|in:' . implode(',', array_keys(Document::TYPES)),
            'niveau_confidentialite' => 'required|in:' . implode(',', array_keys(Document::NIVEAUX_CONFIDENTIALITE)),
            'date_creation'      => 'nullable|date',
            'mots_cles'          => 'nullable|string|max:255',
            'description'        => 'nullable|string|max:1000',
            'fichier'            => 'required|file|max:20480|mimes:pdf,doc,docx,jpg,jpeg,png,gif,xls,xlsx',
            'version'            => 'nullable|string|max:10',
        ]);

        return DB::transaction(function () use ($request, $validated) {
            $agent = Agent::findOrFail($validated['agent_id']);

            // Récupérer ou créer le dossier de l'agent
            $dossier = $this->obtenirOuCreerDossier($agent);

            // Upload sécurisé du fichier
            $fichier       = $request->file('fichier');
            $extension     = $fichier->getClientOriginalExtension();
            $nomFichier    = 'ged/' . $dossier->id_dossier . '/' . time() . '_' . uniqid() . '.' . $extension;
            $chemin        = $fichier->storeAs('', $nomFichier, 'public');

            // Créer le document
            $document = Document::create([
                'id_dossier'           => $dossier->id_dossier,
                'reference'            => Document::genererReference($validated['type_document']),
                'titre'                => $validated['titre'],
                'type_document'        => $validated['type_document'],
                'niveau_confidentialite'=> $validated['niveau_confidentialite'],
                'statut_document'      => 'Actif',
                'date_creation'        => $validated['date_creation'] ?? now()->toDateString(),
                'date_archivage'       => now(),
                'mots_cles'            => $validated['mots_cles'],
                'description'          => $validated['description'],
                'document_url'         => $nomFichier,
                'format_fichier'       => strtolower($fichier->getClientOriginalExtension()),
                'taille_fichier'       => $fichier->getSize(),
                'version'              => $validated['version'] ?? '1.0',
                'charge_par'           => Auth::id(),
            ]);

            // Log audit (Intégrité CID)
            activity('documents')
                ->causedBy(Auth::user())
                ->performedOn($document)
                ->withProperties([
                    'action' => 'upload',
                    'agent'  => $agent->nom_complet,
                    'type'   => $document->type_document,
                    'niveau' => $document->niveau_confidentialite,
                ])
                ->log("Document uploadé dans le dossier de {$agent->nom_complet}");

            return redirect()
                ->route('rh.ged.dossier.show', $dossier->id_dossier)
                ->with('success', "Document « {$document->titre} » archivé avec succès.");
        });
    }

    // =====================================================================
    // DÉTAIL DOCUMENT
    // =====================================================================

    public function show(int $id)
    {
        $document = Document::with(['dossier.agent.service', 'uploadePar'])->findOrFail($id);

        // Vérification accès confidentialité
        if (!$document->estVisiblePar(Auth::user())) {
            abort(403, 'Accès refusé — niveau de confidentialité insuffisant.');
        }

        // Log de consultation (Audit — Disponibilité CID)
        activity('documents')
            ->causedBy(Auth::user())
            ->performedOn($document)
            ->withProperties(['action' => 'consultation', 'niveau' => $document->niveau_confidentialite])
            ->log("Document consulté : {$document->titre}");

        // Historique du document
        $historique = \Spatie\Activitylog\Models\Activity::query()
            ->where('subject_type', Document::class)
            ->where('subject_id', $document->id_document)
            ->with('causer')
            ->orderBy('created_at', 'desc')
            ->get();

        $types  = Document::TYPES;
        $niveaux = Document::NIVEAUX_CONFIDENTIALITE;

        return view('rh.documents.show', compact('document', 'historique', 'types', 'niveaux'));
    }

    // =====================================================================
    // PRÉVISUALISATION SÉCURISÉE (VIEWER)
    // =====================================================================

    public function preview(int $id)
    {
        $document = Document::findOrFail($id);

        if (!$document->estVisiblePar(Auth::user())) {
            abort(403);
        }

        $chemin = storage_path('app/public/' . $document->document_url);
        if (!file_exists($chemin)) {
            abort(404, 'Fichier introuvable.');
        }

        $mimeType = mime_content_type($chemin) ?: 'application/octet-stream';
        return response()->file($chemin, [
            'Content-Type'        => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $document->nom_fichier . '"',
        ]);
    }

    // =====================================================================
    // TÉLÉCHARGEMENT FORCÉ
    // =====================================================================

    public function download(int $id)
    {
        $document = Document::findOrFail($id);

        if (!$document->estVisiblePar(Auth::user())) {
            abort(403);
        }

        $chemin = storage_path('app/public/' . $document->document_url);
        if (!file_exists($chemin)) {
            abort(404, 'Fichier introuvable.');
        }

        activity('documents')
            ->causedBy(Auth::user())
            ->performedOn($document)
            ->withProperties(['action' => 'téléchargement'])
            ->log("Document téléchargé : {$document->titre}");

        return response()->download($chemin, $document->titre . '.' . $document->extension);
    }

    // =====================================================================
    // ARCHIVER DOCUMENT
    // =====================================================================

    public function archiver(int $id)
    {
        $document = Document::findOrFail($id);
        if ($document->statut_document === 'Détruit') {
            return back()->with('error', 'Document déjà détruit.');
        }
        $document->archiver(Auth::id());
        return back()->with('success', "Document « {$document->titre} » archivé.");
    }

    // =====================================================================
    // RESTAURER DOCUMENT ARCHIVÉ
    // =====================================================================

    public function restaurer(int $id)
    {
        $document = Document::findOrFail($id);
        $document->restaurer();
        return back()->with('success', "Document « {$document->titre} » restauré.");
    }

    // =====================================================================
    // DÉTRUIRE DOCUMENT (marque comme détruit)
    // =====================================================================

    public function detruire(int $id)
    {
        $document = Document::findOrFail($id);
        $document->detruire();
        return back()->with('success', "Document « {$document->titre} » marqué comme détruit.");
    }

    // =====================================================================
    // RECHERCHE GLOBALE
    // =====================================================================

    public function search(Request $request)
    {
        $terme   = $request->q;
        $type    = $request->type;
        $statut  = $request->statut ?? 'Actif';
        $niveau  = $request->niveau;
        $service = $request->service;

        $roleUser = Auth::user()->getRoleNames()->first();

        $query = Document::with(['dossier.agent.service', 'uploadePar'])
            ->visiblePour($roleUser);

        if ($terme) {
            $query->recherche($terme);
        }
        if ($type) {
            $query->parType($type);
        }
        if ($statut !== 'tous') {
            $query->where('statut_document', $statut);
        }
        if ($niveau) {
            $query->where('niveau_confidentialite', $niveau);
        }
        if ($service) {
            $query->whereHas('dossier.agent', fn($q) => $q->where('id_service', $service));
        }

        $documents = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $services  = Service::orderBy('nom_service')->get();
        $types     = Document::TYPES;
        $niveaux   = Document::NIVEAUX_CONFIDENTIALITE;

        return view('rh.documents.search', compact(
            'documents', 'services', 'types', 'niveaux', 'terme', 'type', 'statut', 'niveau', 'service'
        ));
    }

    // =====================================================================
    // ÉTAGÈRES
    // =====================================================================

    public function etageres(Request $request)
    {
        $etageres = Etagere::with([
                'service',
                'dossiers' => fn($q) => $q->withCount('documents'),
                'dossiers.agent',
            ])
            ->withCount('dossiers')
            ->orderBy('id_service')
            ->get()
            ->groupBy(fn($e) => $e->service?->nom_service ?? 'Sans service');

        $services = Service::orderBy('nom_service')->get();

        return view('rh.documents.etageres', compact('etageres', 'services'));
    }

    public function etagereStore(Request $request)
    {
        $validated = $request->validate([
            'id_service'  => 'required|exists:services,id_service',
            'nom_etagere' => 'required|string|max:100',
            'numero'      => 'nullable|string|max:20',
            'description' => 'nullable|string|max:500',
        ]);

        $etagere = Etagere::create(array_merge($validated, ['actif' => true]));

        return back()->with('success', "Étagère « {$etagere->nom_etagere} » créée.");
    }

    // =====================================================================
    // HELPERS PRIVÉS
    // =====================================================================

    private function obtenirOuCreerDossier(Agent $agent): DossierAgent
    {
        if ($agent->dossier) {
            return $agent->dossier;
        }

        // Trouver ou créer une étagère pour le service de l'agent
        $etagere = Etagere::where('id_service', $agent->id_service)
                          ->where('actif', true)
                          ->first();

        if (!$etagere) {
            $etagere = Etagere::create([
                'id_service'  => $agent->id_service,
                'nom_etagere' => 'Étagère ' . ($agent->service->nom_service ?? 'Principale'),
                'numero'      => '01',
                'actif'       => true,
            ]);
        }

        return DossierAgent::create([
            'id_etagere'   => $etagere->id_etagere,
            'id_agent'     => $agent->id_agent,
            'reference'    => DossierAgent::genererReference(),
            'statut_da'    => 'Actif',
            'date_creation'=> now(),
        ]);
    }
}
