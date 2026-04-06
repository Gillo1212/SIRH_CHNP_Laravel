<?php

namespace App\Http\Controllers\Major;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\HeureSup;
use App\Models\LignePlanning;
use App\Models\Service;
use App\Models\User;
use App\Notifications\HeuresSupDeclareeNotification;
use Illuminate\Http\Request;

/**
 * HeuresSupMajorController
 *
 * Le Major est au cœur de la gestion des heures supplémentaires.
 * C'est lui qui connaît le mieux son équipe et les réalités terrain.
 *
 * Workflow :
 *   1. Le Major consulte ses lignes de planning et identifie les dépassements.
 *   2. Il déclare les heures supplémentaires sur chaque ligne concernée.
 *   3. La RH vérifie la conformité (sans modifier les données).
 *   4. Si anomalie signalée par la RH, le Major corrige (supprime et re-déclare).
 *
 * Le Major peut supprimer ses déclarations tant qu'elles sont au statut
 * "Déclaré" ou "Anomalie" (une anomalie lui appartient : c'est sa déclaration à corriger).
 */
class HeuresSupMajorController extends Controller
{
    private const HEURES_STANDARD = 8.0;

    private function getMajorService(): ?Service
    {
        return Service::where('id_agent_major', auth()->id())->first();
    }

    public function index(Request $request)
    {
        $service = $this->getMajorService();

        if (!$service) {
            return redirect()->route('major.dashboard')
                ->with('error', 'Vous n\'êtes pas assigné à un service. Contactez le service RH.');
        }

        $lignesQuery = LignePlanning::with(['agent', 'typePoste', 'planning', 'heureSup'])
            ->whereHas('planning', fn($q) => $q->where('id_service', $service->id_service))
            ->orderByDesc('date_poste');

        if ($request->filled('agent')) {
            $lignesQuery->where('id_agent', $request->agent);
        }
        if ($request->filled('planning')) {
            $lignesQuery->where('id_planning', $request->planning);
        }

        if ($request->filled('avec_sup')) {
            $lignes = $lignesQuery->get()->filter(fn($l) => $l->nb_heures > self::HEURES_STANDARD);
            $lignesPaginated = null;
        } else {
            $lignesPaginated = $lignesQuery->paginate(20)->withQueryString();
            $lignes = null;
        }

        $declarations = HeureSup::with(['lignePlanning.agent', 'lignePlanning.typePoste', 'lignePlanning.planning'])
            ->whereHas('lignePlanning.planning', fn($q) => $q->where('id_service', $service->id_service))
            ->orderByDesc('created_at')
            ->get();

        $stats = [
            'total_lignes'   => LignePlanning::whereHas('planning', fn($q) => $q->where('id_service', $service->id_service))->count(),
            'total_declares' => $declarations->count(),
            'heures_totales' => $declarations->sum('nb_heures'),
            'a_corriger'     => $declarations->where('statut_hs', HeureSup::STATUT_ANOMALIE)->count(),
            'conformes'      => $declarations->where('statut_hs', HeureSup::STATUT_CONFORME)->count(),
        ];

        $agents = Agent::where('id_service', $service->id_service)
            ->orderBy('nom')
            ->get(['id_agent', 'nom', 'prenom']);

        $plannings = \App\Models\Planning::where('id_service', $service->id_service)
            ->orderByDesc('periode_debut')
            ->get(['id_planning', 'periode_debut', 'periode_fin', 'statut_planning']);

        return view('major.heures-sup.index', compact(
            'service', 'stats', 'agents', 'plannings',
            'lignesPaginated', 'lignes', 'declarations'
        ));
    }

    /**
     * Déclarer des heures supplémentaires sur une ligne de planning.
     * Règle : 1 seule déclaration par ligne (0..1).
     */
    public function store(Request $request)
    {
        $service = $this->getMajorService();

        if (!$service) {
            return back()->with('error', 'Service non trouvé.');
        }

        $validated = $request->validate([
            'id_ligne'  => 'required|integer|exists:ligne_plannings,id_ligne',
            'nb_heures' => 'required|numeric|min:0.5|max:24',
            'periode'   => 'required|in:Trimestre,Semestre',
        ], [
            'id_ligne.required'  => 'La ligne de planning est obligatoire.',
            'nb_heures.required' => 'Le nombre d\'heures supplémentaires est obligatoire.',
            'nb_heures.min'      => 'Minimum 0,5 heure.',
            'nb_heures.max'      => 'Maximum 24 heures par poste.',
            'periode.required'   => 'La période de référence est obligatoire.',
        ]);

        $ligne = LignePlanning::whereHas('planning', fn($q) => $q->where('id_service', $service->id_service))
            ->findOrFail($validated['id_ligne']);

        if ($ligne->heureSup()->exists()) {
            return back()->with('error', 'Des heures supplémentaires sont déjà déclarées pour ce poste.');
        }

        $heureSup = HeureSup::create([
            'id_ligne'  => $ligne->id_ligne,
            'nb_heures' => $validated['nb_heures'],
            'taux'      => 1.25,
            'montant'   => 0.00,
            'periode'   => $validated['periode'],
            'statut_hs' => HeureSup::STATUT_DECLARE,
        ]);

        // Notifier les AgentRH pour vérification
        User::role('AgentRH')->get()->each(
            fn($rh) => $rh->notify(new HeuresSupDeclareeNotification($heureSup))
        );

        return back()->with('success', 'Heures supplémentaires déclarées et transmises au service RH pour vérification.');
    }

    /**
     * Supprimer une déclaration.
     * Autorisé si statut = Déclaré (en attente de vérification)
     *                   ou Anomalie (la RH a demandé une correction).
     */
    public function destroy(int $id)
    {
        $service = $this->getMajorService();

        if (!$service) {
            return back()->with('error', 'Service non trouvé.');
        }

        $heureSup = HeureSup::whereHas('lignePlanning.planning', fn($q) => $q->where('id_service', $service->id_service))
            ->whereIn('statut_hs', [HeureSup::STATUT_DECLARE, HeureSup::STATUT_ANOMALIE])
            ->findOrFail($id);

        $heureSup->delete();

        return back()->with('success', 'Déclaration supprimée. Vous pouvez soumettre une nouvelle déclaration corrigée.');
    }
}
