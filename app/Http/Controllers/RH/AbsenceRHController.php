<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Http\Requests\RH\StoreAbsenceRHRequest;
use App\Models\Absence;
use App\Models\Agent;
use App\Models\Demande;
use App\Models\PieceJustificative;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class AbsenceRHController extends Controller
{
    /**
     * Liste de toutes les absences (scope RH = tous les services)
     */
    public function index(Request $request)
    {
        $query = Absence::with(['demande.agent.service'])
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
        if ($request->filled('service')) {
            $query->whereHas('demande.agent', fn($q) => $q->where('id_service', $request->service));
        }
        if ($request->filled('agent')) {
            $query->whereHas('demande', fn($q) => $q->where('id_agent', $request->agent));
        }
        if ($request->filled('justifie') && $request->justifie !== '') {
            $query->where('justifie', (bool) $request->justifie);
        }

        $absences = $query->paginate(20)->withQueryString();

        $now = now();
        $totalAgents = Agent::where('statut_agent', 'Actif')->count();
        $joursOuvrablesEstimes = max(1, $now->daysInMonth * 5 / 7);

        $kpis = [
            'total_mois'        => Absence::whereMonth('date_absence', $now->month)->whereYear('date_absence', $now->year)->count(),
            'injustifiees_mois' => Absence::whereMonth('date_absence', $now->month)->whereYear('date_absence', $now->year)->where('justifie', false)->count(),
            'maladie_mois'      => Absence::whereMonth('date_absence', $now->month)->whereYear('date_absence', $now->year)->where('type_absence', 'Maladie')->count(),
            'taux_absenteisme'  => $totalAgents > 0
                ? round(
                    Absence::whereMonth('date_absence', $now->month)->whereYear('date_absence', $now->year)->count()
                    / ($totalAgents * $joursOuvrablesEstimes) * 100,
                    1
                )
                : 0,
        ];

        $services = Service::orderBy('nom_service')->get();
        $agents   = Agent::orderBy('nom')->get();

        // Données analytiques pour les panneaux graphiques
        $parType = Absence::whereMonth('date_absence', $now->month)
            ->whereYear('date_absence', $now->year)
            ->selectRaw('type_absence, COUNT(*) as total')
            ->groupBy('type_absence')
            ->pluck('total', 'type_absence');

        $parService = Absence::whereMonth('date_absence', $now->month)
            ->whereYear('date_absence', $now->year)
            ->join('demandes', 'absences.id_demande', '=', 'demandes.id_demande')
            ->join('agents', 'demandes.id_agent', '=', 'agents.id_agent')
            ->join('services', 'agents.id_service', '=', 'services.id_service')
            ->selectRaw('services.nom_service, COUNT(*) as total')
            ->groupBy('services.nom_service')
            ->orderByDesc('total')
            ->limit(5)
            ->pluck('total', 'nom_service');

        return view('rh.absences.index', compact('absences', 'kpis', 'services', 'agents', 'parType', 'parService'));
    }

    /**
     * Formulaire de saisie d'une absence
     */
    public function create()
    {
        $agents   = Agent::where('statut_agent', 'Actif')->orderBy('nom')->get();
        $services = Service::orderBy('nom_service')->get();

        return view('rh.absences.create', compact('agents', 'services'));
    }

    /**
     * Enregistrer une absence (Intégrité CID : transaction)
     */
    public function store(StoreAbsenceRHRequest $request)
    {
        $agent = Agent::findOrFail($request->id_agent);

        DB::transaction(function () use ($request, $agent) {
            $demande = Demande::create([
                'id_agent'        => $agent->id_agent,
                'type_demande'    => 'Absence',
                'statut_demande'  => 'Approuvé',
                'motif_refus'     => $request->commentaire,
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
                    'agent'  => $agent->nom_complet,
                    'date'   => $request->date_absence,
                    'type'   => $request->type_absence,
                ])
                ->log('Absence enregistrée par le service RH');
        });

        return redirect()->route('rh.absences.index')
            ->with('success', "L'absence de {$agent->nom_complet} a été enregistrée.");
    }

    /**
     * Détail d'une absence
     */
    public function show($id)
    {
        $absence = Absence::with(['demande.agent.service', 'piecesJustificatives'])->findOrFail($id);

        return view('rh.absences.show', compact('absence'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit($id)
    {
        $absence  = Absence::with('demande.agent')->findOrFail($id);
        $agents   = Agent::orderBy('nom')->get();
        $services = Service::orderBy('nom_service')->get();

        return view('rh.absences.edit', compact('absence', 'agents', 'services'));
    }

    /**
     * Mettre à jour une absence
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'date_absence' => ['required', 'date', 'before_or_equal:today'],
            'type_absence' => ['required', 'in:Maladie,Personnelle,Professionnelle,Injustifiée'],
            'justifie'     => ['nullable', 'boolean'],
            'commentaire'  => ['nullable', 'string', 'max:500'],
        ]);

        $absence = Absence::with('demande')->findOrFail($id);

        DB::transaction(function () use ($request, $absence) {
            $absence->update([
                'date_absence' => $request->date_absence,
                'type_absence' => $request->type_absence,
                'justifie'     => $request->boolean('justifie', false),
            ]);

            $absence->demande->update([
                'motif_refus' => $request->commentaire,
            ]);

            activity()
                ->causedBy(auth()->user())
                ->performedOn($absence)
                ->log('Absence modifiée par le service RH');
        });

        return redirect()->route('rh.absences.show', $id)
            ->with('success', 'Absence mise à jour avec succès.');
    }

    /**
     * Supprimer une absence
     */
    public function destroy($id)
    {
        $absence = Absence::with('demande')->findOrFail($id);

        DB::transaction(function () use ($absence) {
            $demandeId = $absence->id_demande;
            $absence->delete();
            Demande::destroy($demandeId);

            activity()
                ->causedBy(auth()->user())
                ->log("Absence #{$absence->id_absence} supprimée");
        });

        return redirect()->route('rh.absences.index')
            ->with('success', 'Absence supprimée.');
    }

    /**
     * Valider un justificatif d'absence
     */
    public function validerJustificatif($id)
    {
        $absence = Absence::findOrFail($id);

        $absence->update(['justifie' => true]);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($absence)
            ->log('Justificatif validé');

        return back()->with('success', 'Justificatif validé.');
    }

    /**
     * Rejeter un justificatif d'absence
     */
    public function rejeterJustificatif($id)
    {
        $absence = Absence::findOrFail($id);

        $absence->update(['justifie' => false]);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($absence)
            ->log('Justificatif rejeté');

        return back()->with('success', 'Justificatif rejeté.');
    }

    /**
     * Valider une pièce justificative spécifique
     */
    public function validerPiece($id, $pieceId)
    {
        $piece = PieceJustificative::where('id_absence', $id)->findOrFail($pieceId);
        $piece->update(['valide' => true]);

        return back()->with('success', 'Pièce validée.');
    }

    /**
     * Rejeter une pièce justificative spécifique
     */
    public function rejeterPiece($id, $pieceId)
    {
        $piece = PieceJustificative::where('id_absence', $id)->findOrFail($pieceId);
        $piece->update(['valide' => false]);

        return back()->with('success', 'Pièce rejetée.');
    }

    /**
     * Export CSV des absences
     */
    public function export(Request $request)
    {
        $query = Absence::with(['demande.agent.service'])
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
        if ($request->filled('service')) {
            $query->whereHas('demande.agent', fn($q) => $q->where('id_service', $request->service));
        }
        if ($request->filled('agent')) {
            $query->whereHas('demande', fn($q) => $q->where('id_agent', $request->agent));
        }
        if ($request->filled('justifie') && $request->justifie !== '') {
            $query->where('justifie', (bool) $request->justifie);
        }

        $absences = $query->get();

        $export = new \App\Exports\ExcelExport('Absences CHNP');
        $export->setHeaders(['Agent', 'Matricule', 'Service', 'Date', 'Type absence', 'Justifiée']);

        foreach ($absences as $a) {
            $agent = $a->demande->agent ?? null;
            $export->addRow([
                $agent?->nom_complet ?? '—',
                $agent?->matricule ?? '—',
                $agent?->service?->nom_service ?? '—',
                $a->date_absence->format('d/m/Y'),
                $a->type_absence,
                $a->justifie ? 'Oui' : 'Non',
            ]);
        }

        return $export->download('absences_chnp_' . now()->format('Y-m-d'));
    }
}
