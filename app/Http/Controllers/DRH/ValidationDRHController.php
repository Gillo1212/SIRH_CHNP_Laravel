<?php

namespace App\Http\Controllers\DRH;

use App\Http\Controllers\Controller;
use App\Models\Demande;
use App\Models\Mouvement;
use App\Models\PriseEnCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ValidationDRHController extends Controller
{
    // ──────────────────────────────────────────────────
    // DÉCISIONS À SIGNER
    // ──────────────────────────────────────────────────
    public function decisions()
    {
        // Étape 1 : en attente de validation DRH
        $aValider = Mouvement::with(['agent.service', 'serviceDestination', 'serviceOrigine', 'createur'])
            ->where('statut', 'en_attente')
            ->orderByDesc('created_at')
            ->get();

        // Étape 2 : validés, en attente de signature DRH
        $aSignerPaginated = Mouvement::with(['agent.service', 'serviceDestination', 'serviceOrigine', 'validateur'])
            ->where('statut', 'valide_drh')
            ->orderByDesc('date_validation')
            ->paginate(15);

        // Historique des décisions signées
        $mouvementsEffectues = Mouvement::with(['agent.service', 'signataire'])
            ->where('statut', 'effectue')
            ->whereNotNull('decision_generee')
            ->latest('date_signature')
            ->take(10)
            ->get();

        $stats = [
            'a_valider' => $aValider->count(),
            'a_signer'  => Mouvement::where('statut', 'valide_drh')->count(),
            'signees'   => Mouvement::where('statut', 'effectue')->whereNotNull('decision_generee')->count(),
            'en_attente'=> $aValider->count() + Mouvement::where('statut', 'valide_drh')->count(),
        ];

        return view('drh.decisions.index', compact('aValider', 'aSignerPaginated', 'mouvementsEffectues', 'stats'));
    }

    public function signer(Request $request, $id)
    {
        $mouvement = Mouvement::with(['agent.service', 'serviceDestination'])->findOrFail($id);

        if ($mouvement->statut !== 'valide_drh') {
            return back()->with('error', 'Ce mouvement ne peut pas être signé dans son état actuel.');
        }

        DB::transaction(function () use ($request, $mouvement) {
            $reference = 'DEC-' . date('Y') . '-' . str_pad($mouvement->id_mouvement, 4, '0', STR_PAD_LEFT);

            $mouvement->update([
                'statut'           => 'effectue',
                'decision_generee' => $reference,
                'signe_par'        => Auth::id(),
                'date_signature'   => now(),
            ]);

            // Mise à jour du service de l'agent si mutation/affectation
            if (in_array($mouvement->type_mouvement, ['Affectation initiale', 'Mutation']) && $mouvement->id_service) {
                $mouvement->agent->update(['id_service' => $mouvement->id_service]);
            }
            if ($mouvement->type_mouvement === 'Départ') {
                $mouvement->agent->update(['statut_agent' => 'Retraité']);
            }

            activity()
                ->causedBy(Auth::user())
                ->on($mouvement)
                ->withProperties(['reference' => $reference, 'note' => $request->note])
                ->log("Signature DRH de la décision {$reference} — mouvement #{$mouvement->id_mouvement}");
        });

        return redirect()->route('drh.validations.decisions')
            ->with('success', 'Décision signée et mouvement effectué avec succès.');
    }

    // ──────────────────────────────────────────────────
    // VALIDATION MOUVEMENTS
    // ──────────────────────────────────────────────────
    public function mouvements(Request $request)
    {
        $this->authorize('viewAny', Mouvement::class);

        $query = Mouvement::with(['agent.service', 'serviceDestination', 'serviceOrigine', 'createur'])
            ->whereIn('statut', ['en_attente', 'valide_drh'])
            ->orderByDesc('date_mouvement');

        if ($type = $request->type_mouvement) {
            $query->where('type_mouvement', $type);
        }

        $mouvements = $query->paginate(15)->withQueryString();

        $stats = [
            'en_attente'   => Mouvement::enAttente()->count(),
            'valide_drh'   => Mouvement::valideDRH()->count(),
            'affectations' => Mouvement::enAttente()->parType('Affectation initiale')->count(),
            'mutations'    => Mouvement::enAttente()->parType('Mutation')->count(),
            'departs'      => Mouvement::enAttente()->parType('Départ')->count(),
        ];

        return view('drh.validations.mouvements', compact('mouvements', 'stats'));
    }

    public function validerMouvement(Request $request, $id)
    {
        $mouvement = Mouvement::findOrFail($id);
        $this->authorize('valider', $mouvement);

        DB::transaction(function () use ($request, $mouvement) {
            $mouvement->update([
                'statut'          => 'valide_drh',
                'valide_par'      => auth()->id(),
                'date_validation' => now(),
            ]);

            activity()
                ->causedBy(auth()->user())
                ->on($mouvement)
                ->withProperties(['note' => $request->note_validation])
                ->log("Validation DRH du mouvement #{$mouvement->id_mouvement}");
        });

        return redirect()->route('drh.validations.decisions')
            ->with('success', 'Mouvement validé. Il apparaît maintenant dans les décisions à signer.');
    }

    public function rejeterMouvement(Request $request, $id)
    {
        $mouvement = Mouvement::findOrFail($id);

        $request->validate([
            'motif_rejet' => 'required|string|min:10|max:500',
        ], ['motif_rejet.required' => 'Le motif du rejet est obligatoire.']);

        DB::transaction(function () use ($request, $mouvement) {
            $mouvement->update(['statut' => 'annule']);

            activity()
                ->causedBy(auth()->user())
                ->on($mouvement)
                ->withProperties(['motif_rejet' => $request->motif_rejet])
                ->log("Rejet DRH du mouvement #{$mouvement->id_mouvement} : {$request->motif_rejet}");
        });

        return redirect()->back()
            ->with('success', 'Mouvement rejeté et annulé.');
    }

    // ──────────────────────────────────────────────────
    // PEC EXCEPTIONNELLES
    // ──────────────────────────────────────────────────
    public function pecExceptionnelles()
    {
        $pecsEnAttente = PriseEnCharge::with(['demande.agent.service'])
            ->where('exceptionnelle', true)
            ->whereHas('demande', fn($q) => $q->where('statut_demande', 'Validé'))
            ->latest()
            ->paginate(10);

        $stats = [
            'en_attente'  => PriseEnCharge::where('exceptionnelle', true)
                ->whereHas('demande', fn($q) => $q->where('statut_demande', 'Validé'))->count(),
            'approuvees'  => PriseEnCharge::where('exceptionnelle', true)
                ->whereHas('demande', fn($q) => $q->where('statut_demande', 'Approuvé'))->count(),
        ];

        return view('drh.validations.pec-exceptionnelles', compact('pecsEnAttente', 'stats'));
    }

    public function validerPEC(Request $request, $id)
    {
        $pec = PriseEnCharge::with('demande')->findOrFail($id);
        $action = $request->input('action', 'approuver');

        if ($action === 'approuver') {
            DB::transaction(function () use ($pec) {
                $pec->update(['validee_par' => Auth::id()]);
                $pec->demande->update([
                    'statut_demande'  => 'Approuvé',
                    'date_traitement' => now(),
                ]);
            });
            activity()->causedBy(Auth::user())->on($pec)->log('PEC exceptionnelle approuvée par DRH');
            return back()->with('success', 'Prise en charge exceptionnelle approuvée par le DRH.');
        }

        if ($action === 'rejeter') {
            DB::transaction(function () use ($pec, $request) {
                $pec->demande->update([
                    'statut_demande' => 'Rejeté',
                    'motif_refus'    => $request->input('motif_rejet', 'Rejetée par DRH'),
                    'date_traitement'=> now(),
                ]);
            });
            activity()->causedBy(Auth::user())->on($pec)->log('PEC exceptionnelle rejetée par DRH');
            return back()->with('success', 'Prise en charge rejetée.');
        }

        return back()->with('error', 'Action invalide.');
    }
}
