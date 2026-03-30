<?php

namespace App\Http\Controllers\DRH;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Agent;
use App\Models\Contrat;
use App\Models\Mouvement;
use App\Models\Service;

class RapportDRHController extends Controller
{
    public function bilanSocial()
    {
        $annee = now()->year;

        $effectifDebut = Agent::actif()
            ->whereDate('created_at', '<', "{$annee}-01-01")
            ->count();
        $recrutements  = Agent::whereYear('created_at', $annee)->count();
        $departs       = Mouvement::where('type_mouvement', 'Départ')
            ->where('statut', 'effectue')
            ->whereYear('created_at', $annee)->count();
        $effectifFin   = Agent::actif()->count();

        $absAnnee    = Absence::whereYear('date_absence', $annee)->count();
        $parType     = Absence::whereYear('date_absence', $annee)
            ->selectRaw('type_absence, COUNT(*) as total')
            ->groupBy('type_absence')
            ->pluck('total', 'type_absence');

        $contratsActifs = Contrat::where('statut_contrat', 'Actif')
            ->selectRaw('type_contrat, COUNT(*) as total')
            ->groupBy('type_contrat')
            ->pluck('total', 'type_contrat');

        $ageFemmes = Agent::actif()->where('sexe', 'F')->count();
        $ageHommes = Agent::actif()->where('sexe', 'M')->count();

        return view('drh.rapports.bilan-social', compact(
            'annee', 'effectifDebut', 'recrutements', 'departs', 'effectifFin',
            'absAnnee', 'parType', 'contratsActifs', 'ageFemmes', 'ageHommes'
        ));
    }

    public function effectifs()
    {
        $parService = Service::withCount(['agents as actifs' => fn($q) => $q->where('statut_agent', 'Actif')])
            ->with('division')
            ->orderByDesc('actifs')
            ->get();

        $parCategorie = Agent::actif()
            ->selectRaw('categorie_cp, COUNT(*) as total')
            ->groupBy('categorie_cp')
            ->pluck('total', 'categorie_cp');

        $parSexe = [
            'F' => Agent::actif()->where('sexe', 'F')->count(),
            'M' => Agent::actif()->where('sexe', 'M')->count(),
        ];

        $parStatut = Agent::selectRaw('statut_agent, COUNT(*) as total')
            ->groupBy('statut_agent')
            ->pluck('total', 'statut_agent');

        $totalActifs = Agent::actif()->count();

        return view('drh.rapports.effectifs', compact(
            'parService', 'parCategorie', 'parSexe', 'parStatut', 'totalActifs'
        ));
    }

    public function previsions()
    {
        // Agents proches de la retraite (≥ 60 ans)
        $procheRetraite = Agent::actif()
            ->whereDate('date_naissance', '<=', now()->subYears(55))
            ->with(['service', 'contratActif'])
            ->orderBy('date_naissance')
            ->get();

        // Contrats expirant dans 90 jours
        $contratsExpirants = Contrat::with('agent.service')
            ->where('statut_contrat', 'Actif')
            ->where('date_fin', '<=', now()->addDays(90))
            ->where('date_fin', '>', now())
            ->orderBy('date_fin')
            ->get();

        // Projection effectifs : recrutements vs départs sur 12 mois
        $projLabels = [];
        $projRecrutements = [];
        $projDeparts = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $projLabels[]       = $m->isoFormat('MMM YY');
            $projRecrutements[] = Agent::whereMonth('created_at', $m->month)->whereYear('created_at', $m->year)->count();
            $projDeparts[]      = Mouvement::where('type_mouvement', 'Départ')->whereMonth('created_at', $m->month)->whereYear('created_at', $m->year)->count();
        }

        return view('drh.rapports.previsions', compact(
            'procheRetraite', 'contratsExpirants',
            'projLabels', 'projRecrutements', 'projDeparts'
        ));
    }

    public function exportConsolide()
    {
        $services   = Service::orderBy('nom_service')->get();
        $anneeActuelle = now()->year;

        return view('drh.rapports.export-consolide', compact('services', 'anneeActuelle'));
    }
}
