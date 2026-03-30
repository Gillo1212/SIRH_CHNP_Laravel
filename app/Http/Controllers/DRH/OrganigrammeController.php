<?php

namespace App\Http\Controllers\DRH;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Organigramme;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrganigrammeController extends Controller
{
    /**
     * Affiche le constructeur d'organigramme.
     */
    public function index()
    {
        $org = Organigramme::latest()->first();

        if (!$org) {
            $org = new Organigramme([
                'titre'       => 'Organigramme du CHNP',
                'donnees_json' => $this->buildDefaultTree(),
            ]);
        }

        // Palette : services et divisions disponibles
        $services = Service::with('divisions')->orderBy('nom_service')->get();
        $totalAgents = Agent::where('statut_agent', 'Actif')->count();

        return view('drh.organigramme.index', compact('org', 'services', 'totalAgents'));
    }

    /**
     * Sauvegarde l'organigramme (requête AJAX).
     */
    public function sauvegarder(Request $request)
    {
        $validated = $request->validate([
            'titre'        => 'required|string|max:200',
            'donnees_json' => 'required|array',
        ]);

        DB::transaction(function () use ($validated) {
            $org = Organigramme::latest()->first();

            if ($org) {
                $org->update([
                    'titre'        => $validated['titre'],
                    'donnees_json' => $validated['donnees_json'],
                    'cree_par'     => auth()->id(),
                ]);
            } else {
                Organigramme::create([
                    'titre'        => $validated['titre'],
                    'donnees_json' => $validated['donnees_json'],
                    'cree_par'     => auth()->id(),
                ]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Organigramme enregistré.']);
    }

    /**
     * Réinitialise l'organigramme à partir de la structure DB.
     */
    public function reinitialiser()
    {
        DB::transaction(function () {
            $defaultTree = $this->buildDefaultTree();
            $org = Organigramme::latest()->first();

            if ($org) {
                $org->update([
                    'donnees_json' => $defaultTree,
                    'cree_par'     => auth()->id(),
                ]);
            } else {
                Organigramme::create([
                    'titre'        => 'Organigramme du CHNP',
                    'donnees_json' => $defaultTree,
                    'cree_par'     => auth()->id(),
                ]);
            }
        });

        return redirect()->route('drh.organigramme')
            ->with('success', 'Organigramme réinitialisé selon la structure actuelle.');
    }

    /**
     * Construit l'arbre par défaut depuis la structure DB (Services → Divisions).
     */
    private function buildDefaultTree(): array
    {
        $services = Service::with(['divisions', 'manager.agent'])
            ->withCount(['agents as agents_actifs' => fn($q) => $q->where('statut_agent', 'Actif')])
            ->orderBy('nom_service')
            ->get();

        $root = [
            'id'         => 'node_root',
            'label'      => 'Centre Hospitalier National de Pikine',
            'sous_titre' => 'Établissement Public de Santé de Niveau 3',
            'type'       => 'institution',
            'children'   => [],
        ];

        foreach ($services as $service) {
            $serviceNode = [
                'id'         => 'service_' . $service->id_service,
                'label'      => $service->nom_service,
                'sous_titre' => $service->type_service . ($service->agents_actifs ? ' · ' . $service->agents_actifs . ' agents' : ''),
                'type'       => 'service',
                'children'   => [],
            ];

            foreach ($service->divisions as $division) {
                $serviceNode['children'][] = [
                    'id'         => 'division_' . $division->id_division,
                    'label'      => $division->nom_division,
                    'sous_titre' => 'Division',
                    'type'       => 'division',
                    'children'   => [],
                ];
            }

            $root['children'][] = $serviceNode;
        }

        return $root;
    }
}
