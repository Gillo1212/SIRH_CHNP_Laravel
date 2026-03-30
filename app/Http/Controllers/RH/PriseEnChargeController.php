<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Demande;
use App\Models\PriseEnCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PriseEnChargeController extends Controller
{
    public function index()
    {
        $prises = PriseEnCharge::with(['demande.agent'])
            ->latest()
            ->paginate(15);

        $stats = [
            'total'    => PriseEnCharge::count(),
            'attente'  => Demande::where('type_demande', 'PriseEnCharge')->where('statut_demande', 'En_attente')->count(),
            'validees' => Demande::where('type_demande', 'PriseEnCharge')->whereIn('statut_demande', ['Validé', 'Approuvé'])->count(),
            'rejetees' => Demande::where('type_demande', 'PriseEnCharge')->where('statut_demande', 'Rejeté')->count(),
        ];

        return view('rh.prises-en-charge.index', compact('prises', 'stats'));
    }

    public function create()
    {
        $agents = Agent::actif()->orderBy('nom')->get();
        return view('rh.prises-en-charge.create', compact('agents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'agent_id'      => 'required|exists:agents,id_agent',
            'ayant_droit'   => 'required|string|in:Agent,Conjoint,Enfant',
            'type_prise'    => 'required|string',
            'raison_medical'=> 'required|string|max:1000',
            'date_debut'    => 'required|date',
            'exceptionnelle'=> 'nullable|boolean',
        ]);

        DB::transaction(function () use ($validated) {
            $demande = Demande::create([
                'id_agent'      => $validated['agent_id'],
                'type_demande'  => 'PriseEnCharge',
                'statut_demande'=> 'En_attente',
            ]);

            PriseEnCharge::create([
                'id_demande'     => $demande->id_demande,
                'raison_medical' => $validated['raison_medical'],
                'ayant_droit'    => $validated['ayant_droit'],
                'type_prise'     => $validated['type_prise'],
                'exceptionnelle' => $validated['exceptionnelle'] ?? false,
                'date_edition'   => $validated['date_debut'],
            ]);
        });

        activity()->causedBy(Auth::user())->log('Prise en charge créée pour l\'agent #' . $validated['agent_id']);

        return redirect()->route('rh.pec.index')->with('success', 'Prise en charge enregistrée avec succès.');
    }

    public function show(int $id)
    {
        $prise = PriseEnCharge::with(['demande.agent.service'])->findOrFail($id);
        return view('rh.prises-en-charge.show', compact('prise'));
    }

    public function update(Request $request, int $id)
    {
        $prise = PriseEnCharge::with('demande')->findOrFail($id);
        $action = $request->input('action');

        if ($action === 'valider') {
            DB::transaction(function () use ($prise) {
                $prise->update(['validee_par' => Auth::id()]);
                $prise->demande->update([
                    'statut_demande'  => 'Validé',
                    'date_traitement' => now(),
                ]);
            });
            activity()->causedBy(Auth::user())->on($prise)->log('PEC validée par RH');
            return back()->with('success', 'Prise en charge validée.');
        }

        if ($action === 'rejeter') {
            $request->validate(['motif_rejet' => 'nullable|string|max:500']);
            DB::transaction(function () use ($prise, $request) {
                $prise->demande->update([
                    'statut_demande' => 'Rejeté',
                    'motif_refus'    => $request->input('motif_rejet', 'Rejetée par RH'),
                    'date_traitement'=> now(),
                ]);
            });
            activity()->causedBy(Auth::user())->on($prise)->log('PEC rejetée par RH');
            return back()->with('success', 'Prise en charge rejetée.');
        }

        return back()->with('error', 'Action invalide.');
    }

    public function historique()
    {
        $prises = PriseEnCharge::with(['demande.agent'])
            ->whereHas('demande', fn($q) => $q->whereIn('statut_demande', ['Validé', 'Approuvé', 'Rejeté']))
            ->latest()
            ->paginate(20);

        return view('rh.prises-en-charge.historique', compact('prises'));
    }
}
