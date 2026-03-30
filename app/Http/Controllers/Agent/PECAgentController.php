<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Demande;
use App\Models\PriseEnCharge;
use App\Models\User;
use App\Notifications\PriseEnChargeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PECAgentController extends Controller
{
    private function agentId(): int
    {
        return Auth::user()->agent->id_agent;
    }

    public function index()
    {
        $pecs = PriseEnCharge::with('demande')
            ->whereHas('demande', fn($q) => $q->where('id_agent', $this->agentId()))
            ->latest()
            ->paginate(10);

        return view('agent.pec.index', compact('pecs'));
    }

    public function create()
    {
        return view('agent.pec.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ayant_droit'    => 'required|string|in:Agent,Conjoint,Enfant',
            'type_prise'     => 'required|string',
            'raison_medical' => 'required|string|max:1000',
        ]);

        DB::transaction(function () use ($validated) {
            $demande = Demande::create([
                'id_agent'       => $this->agentId(),
                'type_demande'   => 'PriseEnCharge',
                'statut_demande' => 'En_attente',
            ]);

            PriseEnCharge::create([
                'id_demande'     => $demande->id_demande,
                'raison_medical' => $validated['raison_medical'],
                'ayant_droit'    => $validated['ayant_droit'],
                'type_prise'     => $validated['type_prise'],
                'date_edition'   => now()->toDateString(),
            ]);
        });

        activity()->causedBy(Auth::user())->log('Demande PEC soumise');

        // Notifier les AgentRH (hors transaction)
        try {
            $agent = Auth::user()->agent;
            User::role(['AgentRH', 'DRH'])->each(
                fn(User $rh) => $rh->notify(new PriseEnChargeNotification($agent))
            );
        } catch (\Throwable $e) {
            \Log::warning('Notification PEC RH échouée : ' . $e->getMessage());
        }

        return redirect()->route('agent.pec.index')
            ->with('success', 'Votre demande de prise en charge a été soumise avec succès.');
    }

    public function show($id)
    {
        $pec = PriseEnCharge::with('demande')
            ->whereHas('demande', fn($q) => $q->where('id_agent', $this->agentId()))
            ->findOrFail($id);

        return view('agent.pec.show', compact('pec'));
    }

    public function download($id)
    {
        $pec = PriseEnCharge::with(['demande.agent'])
            ->whereHas('demande', fn($q) => $q->where('id_agent', $this->agentId()))
            ->whereHas('demande', fn($q) => $q->where('statut_demande', 'Approuvé'))
            ->findOrFail($id);

        return redirect()->route('agent.pec.show', $id)
            ->with('info', 'Votre attestation est disponible. Contactez le service RH pour l\'obtenir en version imprimée.');
    }
}
