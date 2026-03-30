<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Agent;
use App\Models\Contrat;
use App\Models\Demande;
use App\Models\Mouvement;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RapportRHController extends Controller
{
    public function index(Request $request)
    {
        $view = $request->get('view', 'mensuel');

        $stats = [
            'agents'          => Agent::actif()->count(),
            'contrats_actifs' => Contrat::where('statut_contrat', 'Actif')->count(),
            'absences_mois'   => Absence::whereMonth('date_absence', now()->month)->whereYear('date_absence', now()->year)->count(),
            'mouvements_mois' => Mouvement::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'conges_en_cours' => Demande::where('type_demande', 'Conge')->whereIn('statut_demande', ['En_attente', 'Validé'])->count(),
        ];

        $data = compact('stats', 'view');

        if ($view === 'mensuel') {
            $mois  = $request->integer('mois', now()->month);
            $annee = $request->integer('annee', now()->year);

            $absences = Absence::with(['demande.agent.service'])
                ->whereMonth('date_absence', $mois)->whereYear('date_absence', $annee)->get();
            $absByType  = $absences->groupBy('type_absence')->map->count();
            $conges     = Demande::with(['agent.service', 'conge'])->where('type_demande', 'Conge')
                ->whereMonth('created_at', $mois)->whereYear('created_at', $annee)->get();
            $mouvements = Mouvement::with(['agent.service'])
                ->whereMonth('created_at', $mois)->whereYear('created_at', $annee)->get();
            $statsMois  = [
                'total_absences'    => $absences->count(),
                'absences_justif'   => $absences->where('justifie', true)->count(),
                'absences_injustif' => $absences->where('justifie', false)->count(),
                'total_conges'      => $conges->count(),
                'conges_approuves'  => $conges->where('statut_demande', 'Approuvé')->count(),
                'total_mouvements'  => $mouvements->count(),
            ];
            $moisLabel = \Carbon\Carbon::createFromDate($annee, $mois, 1)->isoFormat('MMMM YYYY');

            $data += compact('absences', 'absByType', 'conges', 'mouvements', 'statsMois', 'mois', 'annee', 'moisLabel');

        } elseif ($view === 'effectifs') {
            $serviceId = $request->service;
            $statut    = $request->statut ?? 'actif';
            $query     = Agent::with(['service.division', 'contratActif'])->orderBy('nom');
            if ($serviceId) $query->where('id_service', $serviceId);
            if ($statut)    $query->where('statut_agent', $statut);

            $agents       = $query->paginate(20)->withQueryString();
            $services     = Service::orderBy('nom_service')->get();
            $statsEff     = [
                'total'     => Agent::count(),
                'actifs'    => Agent::actif()->count(),
                'en_conge'  => Agent::where('statut_agent', 'En_congé')->count(),
                'retraites' => Agent::where('statut_agent', 'Retraité')->count(),
                'suspendus' => Agent::where('statut_agent', 'Suspendu')->count(),
            ];
            $parCategorie = Agent::actif()
                ->selectRaw('categorie_cp, COUNT(*) as total')
                ->groupBy('categorie_cp')->pluck('total', 'categorie_cp');

            $data += compact('agents', 'services', 'statsEff', 'parCategorie', 'statut', 'serviceId');

        } elseif ($view === 'graphiques') {
            // Chart builder – no server data needed (loaded via AJAX /chart-data)

        } elseif ($view === 'statistiques') {
            $statsContrats = [
                'actifs'   => Contrat::where('statut_contrat', 'Actif')->count(),
                'expires'  => Contrat::where('statut_contrat', 'Expiré')->count(),
                'expiring' => Contrat::where('statut_contrat', 'Actif')->where('date_fin', '<=', now()->addDays(60))->where('date_fin', '>', now())->count(),
                'clotured' => Contrat::where('statut_contrat', 'Clôturé')->count(),
            ];
            $absParMois = [];
            $labels     = [];
            for ($i = 11; $i >= 0; $i--) {
                $m = now()->subMonths($i);
                $labels[]     = $m->isoFormat('MMM YY');
                $absParMois[] = Absence::whereMonth('date_absence', $m->month)->whereYear('date_absence', $m->year)->count();
            }
            $absByType = Absence::selectRaw('type_absence, COUNT(*) as total')
                ->whereYear('date_absence', now()->year)->groupBy('type_absence')->pluck('total', 'type_absence');
            $mouvMois  = Mouvement::selectRaw('type_mouvement, COUNT(*) as total')
                ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)
                ->groupBy('type_mouvement')->pluck('total', 'type_mouvement');
            $effParService = Service::withCount(['agents as actifs' => fn($q) => $q->where('statut_agent', 'Actif')])
                ->having('actifs', '>', 0)->orderByDesc('actifs')->take(10)->get();

            $data += compact('statsContrats', 'absParMois', 'labels', 'absByType', 'mouvMois', 'effParService');
        }

        return view('rh.rapports.index', $data);
    }

    public function mensuel(Request $request)
    {
        $mois = $request->integer('mois', now()->month);
        $annee = $request->integer('annee', now()->year);

        // Absences du mois
        $absences = Absence::with(['demande.agent.service'])
            ->whereMonth('date_absence', $mois)
            ->whereYear('date_absence', $annee)
            ->get();

        $absByType = $absences->groupBy('type_absence')->map->count();

        // Congés du mois
        $conges = Demande::with(['agent.service', 'conge'])
            ->where('type_demande', 'Conge')
            ->whereMonth('created_at', $mois)
            ->whereYear('created_at', $annee)
            ->get();

        // Mouvements du mois
        $mouvements = Mouvement::with(['agent.service'])
            ->whereMonth('created_at', $mois)
            ->whereYear('created_at', $annee)
            ->get();

        $stats = [
            'total_absences'    => $absences->count(),
            'absences_justif'   => $absences->where('justifie', true)->count(),
            'absences_injustif' => $absences->where('justifie', false)->count(),
            'total_conges'      => $conges->count(),
            'conges_approuves'  => $conges->where('statut_demande', 'Approuvé')->count(),
            'total_mouvements'  => $mouvements->count(),
        ];

        $moisLabel = \Carbon\Carbon::createFromDate($annee, $mois, 1)->isoFormat('MMMM YYYY');

        return view('rh.rapports.mensuel', compact(
            'absences', 'absByType', 'conges', 'mouvements', 'stats',
            'mois', 'annee', 'moisLabel'
        ));
    }

    public function effectifs(Request $request)
    {
        $serviceId = $request->service;
        $statut    = $request->statut ?? 'actif';

        $query = Agent::with(['service.division', 'contratActif'])->orderBy('nom');

        if ($serviceId) {
            $query->where('id_service', $serviceId);
        }
        if ($statut) {
            $query->where('statut_agent', $statut);
        }

        $agents   = $query->paginate(20)->withQueryString();
        $services = Service::orderBy('nom_service')->get();

        $stats = [
            'total'     => Agent::count(),
            'actifs'    => Agent::actif()->count(),
            'en_conge'  => Agent::where('statut_agent', 'En_congé')->count(),
            'retraites' => Agent::where('statut_agent', 'Retraité')->count(),
            'suspendus' => Agent::where('statut_agent', 'Suspendu')->count(),
        ];

        // Répartition par catégorie
        $parCategorie = Agent::actif()
            ->selectRaw('categorie_cp, COUNT(*) as total')
            ->groupBy('categorie_cp')
            ->pluck('total', 'categorie_cp');

        return view('rh.rapports.effectifs', compact('agents', 'services', 'stats', 'parCategorie', 'statut', 'serviceId'));
    }

    public function stats()
    {
        $effectif = Agent::actif()->count();

        // Contrats
        $statsContrats = [
            'actifs'    => Contrat::where('statut_contrat', 'Actif')->count(),
            'expires'   => Contrat::where('statut_contrat', 'Expiré')->count(),
            'expiring'  => Contrat::where('statut_contrat', 'Actif')->where('date_fin', '<=', now()->addDays(60))->where('date_fin', '>', now())->count(),
            'clotured'  => Contrat::where('statut_contrat', 'Clôturé')->count(),
        ];

        // Absences 12 mois
        $absParMois = [];
        $labels = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $labels[]     = $m->isoFormat('MMM YY');
            $absParMois[] = Absence::whereMonth('date_absence', $m->month)->whereYear('date_absence', $m->year)->count();
        }

        // Types d'absence
        $absByType = Absence::selectRaw('type_absence, COUNT(*) as total')
            ->whereYear('date_absence', now()->year)
            ->groupBy('type_absence')
            ->pluck('total', 'type_absence');

        // Congés en attente
        $congesEnAttente = Demande::where('type_demande', 'Conge')
            ->whereIn('statut_demande', ['En_attente', 'Validé'])->count();

        // Mouvements ce mois
        $mouvMois = Mouvement::selectRaw('type_mouvement, COUNT(*) as total')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->groupBy('type_mouvement')
            ->pluck('total', 'type_mouvement');

        // Effectifs par service
        $effParService = Service::withCount(['agents as actifs' => fn($q) => $q->where('statut_agent', 'Actif')])
            ->having('actifs', '>', 0)->orderByDesc('actifs')->take(10)->get();

        return view('rh.rapports.stats', compact(
            'effectif', 'statsContrats', 'absParMois', 'labels',
            'absByType', 'congesEnAttente', 'mouvMois', 'effParService'
        ));
    }

    public function export()
    {
        $services = Service::orderBy('nom_service')->get();

        return view('rh.rapports.export', compact('services'));
    }

    /**
     * API JSON — données pour le constructeur de graphiques
     * GET /rh/rapports/chart-data?source=effectifs_par_service
     * Réponses mises en cache 5 min (données agrégées, pas temps réel)
     */
    public function chartData(Request $request)
    {
        $this->authorize('viewAny', Agent::class);

        $source = $request->get('source', 'effectifs_par_service');

        $data = Cache::remember('rh_chart_' . $source, 300, fn() => $this->buildChartData($source));

        if (isset($data['_error'])) {
            return response()->json(['error' => $data['_error']], 422);
        }

        return response()->json($data);
    }

    private function buildChartData(string $source): array
    {
        switch ($source) {
            case 'effectifs_par_service':
                $rows = Service::withCount(['agents as total' => fn($q) => $q->where('statut_agent', 'Actif')])
                    ->having('total', '>', 0)->orderByDesc('total')->take(12)->get();
                return ['labels' => $rows->pluck('nom_service')->values(), 'data' => $rows->pluck('total')->values(), 'label' => 'Agents actifs par service'];

            case 'effectifs_par_categorie':
                $rows = Agent::actif()->selectRaw('categorie_cp, COUNT(*) as total')->groupBy('categorie_cp')->orderByDesc('total')->get();
                return ['labels' => $rows->pluck('categorie_cp')->map(fn($v) => str_replace('_', ' ', $v))->values(), 'data' => $rows->pluck('total')->values(), 'label' => 'Effectifs par catégorie CSP'];

            case 'effectifs_par_statut':
                $statuts = ['Actif', 'En_congé', 'Suspendu', 'Retraité'];
                return [
                    'labels' => collect($statuts)->map(fn($v) => str_replace('_', ' ', $v))->values(),
                    'data'   => collect($statuts)->map(fn($s) => Agent::where('statut_agent', $s)->count())->values(),
                    'label'  => 'Effectifs par statut',
                ];

            case 'effectifs_par_sexe':
                return ['labels' => ['Masculin', 'Féminin'], 'data' => [Agent::actif()->where('sexe', 'M')->count(), Agent::actif()->where('sexe', 'F')->count()], 'label' => 'Répartition H/F'];

            case 'absences_par_type':
                $rows = Absence::selectRaw('type_absence, COUNT(*) as total')->whereYear('date_absence', now()->year)->groupBy('type_absence')->get();
                return ['labels' => $rows->pluck('type_absence')->values(), 'data' => $rows->pluck('total')->values(), 'label' => 'Absences par type (' . now()->year . ')'];

            case 'absences_par_mois':
                [$labels, $data] = [[], []];
                for ($i = 11; $i >= 0; $i--) {
                    $m = now()->subMonths($i);
                    $labels[] = $m->isoFormat('MMM YY');
                    $data[]   = Absence::whereMonth('date_absence', $m->month)->whereYear('date_absence', $m->year)->count();
                }
                return ['labels' => $labels, 'data' => $data, 'label' => 'Absences / mois (12m)'];

            case 'contrats_par_statut':
                $statuts = ['Actif', 'Expiré', 'En_renouvellement', 'Clôturé'];
                return [
                    'labels' => collect($statuts)->map(fn($v) => str_replace('_', ' ', $v))->values(),
                    'data'   => collect($statuts)->map(fn($s) => Contrat::where('statut_contrat', $s)->count())->values(),
                    'label'  => 'Contrats par statut',
                ];

            case 'contrats_par_type':
                $rows = Contrat::where('statut_contrat', 'Actif')->selectRaw('type_contrat, COUNT(*) as total')->groupBy('type_contrat')->orderByDesc('total')->get();
                return ['labels' => $rows->pluck('type_contrat')->values(), 'data' => $rows->pluck('total')->values(), 'label' => 'Contrats actifs par type'];

            case 'mouvements_par_type':
                $rows = Mouvement::selectRaw('type_mouvement, COUNT(*) as total')->whereYear('created_at', now()->year)->groupBy('type_mouvement')->get();
                return ['labels' => $rows->pluck('type_mouvement')->values(), 'data' => $rows->pluck('total')->values(), 'label' => 'Mouvements par type (' . now()->year . ')'];

            case 'mouvements_par_mois':
                [$labels, $data] = [[], []];
                for ($i = 11; $i >= 0; $i--) {
                    $m = now()->subMonths($i);
                    $labels[] = $m->isoFormat('MMM YY');
                    $data[]   = Mouvement::whereMonth('created_at', $m->month)->whereYear('created_at', $m->year)->count();
                }
                return ['labels' => $labels, 'data' => $data, 'label' => 'Mouvements / mois (12m)'];

            default:
                return ['_error' => 'Source inconnue'];
        }
    }
}
