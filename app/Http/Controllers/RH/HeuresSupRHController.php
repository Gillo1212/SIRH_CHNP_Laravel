<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Models\HeureSup;
use App\Models\Service;
use Illuminate\Http\Request;

/**
 * HeuresSupRHController
 *
 * Rôle de la RH : VÉRIFICATION DE CONFORMITÉ uniquement.
 *
 * La RH ne valide pas et ne modifie pas les heures déclarées par les Majors.
 * Elle compare les heures déclarées avec le dépassement réel issu du planning
 * et s'assure qu'il n'y a aucune altération (ni augmentation, ni diminution).
 *
 * Actions disponibles :
 *   - Conforme  : les heures déclarées correspondent au planning, aucun écart.
 *   - Anomalie  : un écart a été détecté, le Major doit revoir sa déclaration.
 */
class HeuresSupRHController extends Controller
{
    public function index(Request $request)
    {
        $query = HeureSup::with([
            'lignePlanning.agent',
            'lignePlanning.typePoste',
            'lignePlanning.planning.service',
        ])->orderByDesc('created_at');

        if ($request->filled('service')) {
            $query->whereHas('lignePlanning.planning', fn($q) => $q->where('id_service', $request->service));
        }
        if ($request->filled('statut')) {
            $query->where('statut_hs', $request->statut);
        }
        if ($request->filled('periode')) {
            $query->where('periode', $request->periode);
        }

        $heuresSup = $query->paginate(20)->withQueryString();

        $statsRaw = HeureSup::selectRaw('statut_hs, count(*) as nb, SUM(nb_heures) as total_heures')
            ->groupBy('statut_hs')->get()->keyBy('statut_hs');

        $stats = [
            'total'        => $statsRaw->sum('nb'),
            'total_heures' => $statsRaw->sum('total_heures'),
            'a_verifier'   => $statsRaw->get(HeureSup::STATUT_DECLARE)?->nb   ?? 0,
            'conformes'    => $statsRaw->get(HeureSup::STATUT_CONFORME)?->nb  ?? 0,
            'anomalies'    => $statsRaw->get(HeureSup::STATUT_ANOMALIE)?->nb  ?? 0,
        ];

        $services = Service::orderBy('nom_service')->get(['id_service', 'nom_service']);

        return view('rh.heures-sup.index', compact('heuresSup', 'stats', 'services'));
    }

    /**
     * Marquer conforme : heures déclarées vérifiées, aucune altération.
     */
    public function marquerConforme(int $id)
    {
        $heureSup = HeureSup::where('statut_hs', HeureSup::STATUT_DECLARE)->findOrFail($id);
        $heureSup->marquerConforme();

        // Notifier le Major
        $service = $heureSup->lignePlanning?->planning?->service;
        if ($service?->agentMajor) {
            $service->agentMajor->notify(
                new \App\Notifications\HeuresSupValideeNotification($heureSup)
            );
        }

        return back()->with('success', 'Déclaration vérifiée et jugée conforme.');
    }

    /**
     * Signaler une anomalie : écart détecté, le Major doit corriger.
     * Ne supprime pas la déclaration — le Major garde la main.
     */
    public function signalerAnomalie(Request $request, int $id)
    {
        $request->validate([
            'note' => 'required|string|min:10|max:500',
        ], [
            'note.required' => 'Précisez l\'anomalie constatée pour que le Major puisse corriger.',
            'note.min'      => 'La note doit faire au moins 10 caractères.',
        ]);

        $heureSup = HeureSup::where('statut_hs', HeureSup::STATUT_DECLARE)->findOrFail($id);
        $heureSup->signalerAnomalie($request->note);

        // Notifier le Major pour qu'il corrige
        $service = $heureSup->lignePlanning?->planning?->service;
        if ($service?->agentMajor) {
            $service->agentMajor->notify(
                new \App\Notifications\HeuresSupValideeNotification($heureSup)
            );
        }

        return back()->with('success', 'Anomalie signalée. Le Major du service a été notifié pour correction.');
    }
}
