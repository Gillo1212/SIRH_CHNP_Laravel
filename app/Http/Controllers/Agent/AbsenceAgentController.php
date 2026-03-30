<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Demande;
use App\Models\PieceJustificative;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbsenceAgentController extends Controller
{
    /**
     * Mes absences — liste avec stats
     */
    public function index()
    {
        $agent = auth()->user()->agent;

        if (!$agent) {
            return redirect()->route('agent.profil')
                ->with('warning', 'Veuillez compléter votre profil.');
        }

        $absences = Absence::with(['demande', 'piecesJustificatives'])
            ->whereHas('demande', fn($q) => $q->where('id_agent', $agent->id_agent))
            ->orderByDesc('date_absence')
            ->paginate(20);

        $base = Absence::whereHas('demande', fn($q) => $q->where('id_agent', $agent->id_agent));

        $statsAnnee = [
            'total'        => (clone $base)->whereYear('date_absence', now()->year)->count(),
            'justifiees'   => (clone $base)->whereYear('date_absence', now()->year)->where('justifie', true)->count(),
            'mois_courant' => (clone $base)->whereMonth('date_absence', now()->month)->whereYear('date_absence', now()->year)->count(),
        ];
        $statsAnnee['injustifiees'] = $statsAnnee['total'] - $statsAnnee['justifiees'];

        // Compter les demandes En_attente
        $statsAnnee['en_attente'] = Absence::whereHas(
            'demande',
            fn($q) => $q->where('id_agent', $agent->id_agent)->where('statut_demande', 'En_attente')
        )->count();

        return view('agent.absences.index', compact('absences', 'statsAnnee', 'agent'));
    }

    /**
     * Soumettre une demande d'absence
     */
    public function store(Request $request)
    {
        $agent = auth()->user()->agent;

        if (!$agent) {
            return redirect()->route('agent.profil')
                ->with('warning', 'Veuillez compléter votre profil.');
        }

        $request->validate([
            'date_absence' => ['required', 'date', 'before_or_equal:today'],
            'type_absence' => ['required', 'in:Maladie,Personnelle,Professionnelle,Injustifiée'],
            'commentaire'  => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($request, $agent) {
            $demande = Demande::create([
                'id_agent'       => $agent->id_agent,
                'type_demande'   => 'Absence',
                'statut_demande' => 'En_attente',
                'motif_refus'    => $request->commentaire,
            ]);

            Absence::create([
                'id_demande'   => $demande->id_demande,
                'date_absence' => $request->date_absence,
                'type_absence' => $request->type_absence,
                'justifie'     => false,
            ]);

            activity()
                ->causedBy(auth()->user())
                ->withProperties(['agent' => $agent->matricule, 'date' => $request->date_absence])
                ->log("Agent a soumis une demande d'absence pour le {$request->date_absence}");
        });

        return redirect()->route('agent.absences.index')
            ->with('success', 'Votre demande d\'absence a été soumise et est en attente de validation.');
    }

    /**
     * Soumettre un justificatif pour une absence
     */
    public function uploadJustificatif(Request $request, $id)
    {
        $agent = auth()->user()->agent;

        $absence = Absence::with(['demande', 'piecesJustificatives'])->findOrFail($id);

        // Vérifier que cette absence appartient bien à cet agent
        if (!$absence->demande || $absence->demande->id_agent !== $agent->id_agent) {
            abort(403, 'Accès non autorisé.');
        }

        $request->validate([
            'type_piece' => ['required', 'in:Certificat médical,Acte décès,Convocation'],
            'fichier'    => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $path = $request->file('fichier')->store('justificatifs/' . $agent->matricule, 'public');

        PieceJustificative::create([
            'id_absence'  => $absence->id_absence,
            'type_piece'  => $request->type_piece,
            'fichier_url' => $path,
            'date_depot'  => now(),
            'valide'      => false,
        ]);

        activity()
            ->causedBy(auth()->user())
            ->withProperties(['absence_id' => $id, 'type_piece' => $request->type_piece])
            ->log("Agent a soumis un justificatif pour l'absence #{$id}");

        return redirect()->route('agent.absences.index')
            ->with('success', 'Justificatif soumis avec succès. Le service RH le validera prochainement.');
    }
}
