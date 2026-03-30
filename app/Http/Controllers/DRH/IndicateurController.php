<?php

namespace App\Http\Controllers\DRH;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Agent;
use App\Models\Contrat;
use App\Models\Demande;
use App\Models\Mouvement;
use App\Models\Service;

class IndicateurController extends Controller
{
    public function effectifs()
    {
        $effectifTotal  = Agent::actif()->count();
        $effectifFemmes = Agent::actif()->where('sexe', 'F')->count();
        $effectifHommes = Agent::actif()->where('sexe', 'M')->count();
        $tauxFeminis    = $effectifTotal > 0 ? round($effectifFemmes / $effectifTotal * 100, 1) : 0;

        // Répartition par catégorie CP
        $parCategorie = Agent::actif()
            ->selectRaw('categorie_cp, COUNT(*) as total')
            ->groupBy('categorie_cp')
            ->pluck('total', 'categorie_cp');

        // Répartition par service
        $parService = Service::withCount(['agents as actifs_count' => fn($q) => $q->where('statut_agent', 'Actif')])
            ->having('actifs_count', '>', 0)
            ->orderByDesc('actifs_count')
            ->get();

        // Répartition par statut contrat
        $parStatut = Agent::selectRaw('statut_agent, COUNT(*) as total')
            ->groupBy('statut_agent')
            ->pluck('total', 'statut_agent');

        // Répartition par situation familiale
        $parSitFam = Agent::actif()
            ->selectRaw('situation_familiale, COUNT(*) as total')
            ->groupBy('situation_familiale')
            ->pluck('total', 'situation_familiale');

        // Répartition par famille d'emploi
        $parFamille = Agent::actif()
            ->selectRaw('famille_d_emploi, COUNT(*) as total')
            ->groupBy('famille_d_emploi')
            ->pluck('total', 'famille_d_emploi');

        // Nouveaux agents (12 mois — basé sur date de création du dossier)
        $recrutementsParMois = [];
        $labelsParMois = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $labelsParMois[]       = $m->isoFormat('MMM YY');
            $recrutementsParMois[] = Agent::whereMonth('created_at', $m->month)
                ->whereYear('created_at', $m->year)->count();
        }

        return view('drh.indicateurs.effectifs', compact(
            'effectifTotal', 'effectifFemmes', 'effectifHommes', 'tauxFeminis',
            'parCategorie', 'parService', 'parStatut', 'parFamille', 'parSitFam',
            'recrutementsParMois', 'labelsParMois'
        ));
    }

    public function turnover()
    {
        // Départs sur 12 mois
        $departsParMois = [];
        $recrutementsParMois = [];
        $labels = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $labels[]              = $m->isoFormat('MMM YY');
            $departsParMois[]      = Mouvement::where('type_mouvement', 'Départ')
                ->whereMonth('created_at', $m->month)->whereYear('created_at', $m->year)->count();
            $recrutementsParMois[] = Agent::whereMonth('created_at', $m->month)
                ->whereYear('created_at', $m->year)->count();
        }

        $totalDeparts      = array_sum($departsParMois);
        $totalRecrutements = array_sum($recrutementsParMois);
        $effectif          = Agent::actif()->count();
        $tauxTurnover      = $effectif > 0 ? round($totalDeparts / $effectif * 100, 1) : 0;

        // Motifs de départ
        $motifs = Mouvement::where('type_mouvement', 'Départ')
            ->selectRaw('motif, COUNT(*) as total')
            ->groupBy('motif')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // Départs récents
        $departsRecents = Mouvement::with(['agent.service'])
            ->where('type_mouvement', 'Départ')
            ->where('statut', 'effectue')
            ->latest()
            ->take(10)
            ->get();

        return view('drh.indicateurs.turnover', compact(
            'departsParMois', 'recrutementsParMois', 'labels',
            'totalDeparts', 'totalRecrutements', 'effectif', 'tauxTurnover',
            'motifs', 'departsRecents'
        ));
    }

    public function absenteisme()
    {
        $effectif = Agent::actif()->count();

        // Absences par mois (12 mois)
        $absParMois = [];
        $labels     = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $labels[]     = $m->isoFormat('MMM YY');
            $absParMois[] = Absence::whereMonth('date_absence', $m->month)
                ->whereYear('date_absence', $m->year)->count();
        }

        $absMonth     = $absParMois[11] ?? 0;
        $tauxAbsMois  = $effectif > 0 ? round($absMonth / ($effectif * 22) * 100, 2) : 0;

        // Absences par type
        $parType = Absence::selectRaw('type_absence, COUNT(*) as total')
            ->groupBy('type_absence')
            ->pluck('total', 'type_absence');

        // Top services avec le plus d'absences (ce mois)
        $topServices = Service::withCount(['agents as absences_count' => function ($q) {
            $q->whereHas('demandes', fn($d) => $d->whereHas('absence', fn($a) =>
                $a->whereMonth('date_absence', now()->month)
                  ->whereYear('date_absence', now()->year)
            ));
        }])->orderByDesc('absences_count')->take(8)->get();

        // Taux par service
        $labelsServices = $topServices->pluck('nom_service')->toArray();
        $dataServices   = $topServices->pluck('absences_count')->toArray();

        return view('drh.indicateurs.absenteisme', compact(
            'effectif', 'absParMois', 'labels', 'absMonth', 'tauxAbsMois',
            'parType', 'topServices', 'labelsServices', 'dataServices'
        ));
    }

    public function pyramideAges()
    {
        $tranches = [
            '< 25 ans'  => [Agent::actif()->whereDate('date_naissance', '>', now()->subYears(25))->count(), '#3B82F6'],
            '25–34 ans' => [Agent::actif()->whereDate('date_naissance', '<=', now()->subYears(25))->whereDate('date_naissance', '>', now()->subYears(35))->count(), '#10B981'],
            '35–44 ans' => [Agent::actif()->whereDate('date_naissance', '<=', now()->subYears(35))->whereDate('date_naissance', '>', now()->subYears(45))->count(), '#F59E0B'],
            '45–54 ans' => [Agent::actif()->whereDate('date_naissance', '<=', now()->subYears(45))->whereDate('date_naissance', '>', now()->subYears(55))->count(), '#EF4444'],
            '55–64 ans' => [Agent::actif()->whereDate('date_naissance', '<=', now()->subYears(55))->whereDate('date_naissance', '>', now()->subYears(65))->count(), '#8B5CF6'],
            '≥ 65 ans'  => [Agent::actif()->whereDate('date_naissance', '<=', now()->subYears(65))->count(), '#6B7280'],
        ];

        $ages = Agent::actif()->whereNotNull('date_naissance')
            ->get()
            ->map(fn($a) => $a->date_naissance->diffInYears(now()));

        $ageMoyen  = $ages->count() > 0 ? round($ages->average(), 1) : 0;
        $ageMedian = $ages->count() > 0 ? round($ages->sort()->values()[(int) ($ages->count() / 2)] ?? 0, 0) : 0;

        $agents55Plus = Agent::actif()
            ->whereDate('date_naissance', '<=', now()->subYears(55))
            ->with('service')
            ->orderBy('date_naissance')
            ->get();

        return view('drh.indicateurs.pyramide-ages', compact(
            'tranches', 'ageMoyen', 'ageMedian', 'agents55Plus'
        ));
    }
}
