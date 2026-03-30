<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Http\Requests\RH\StoreDivisionRequest;
use App\Models\Division;
use Illuminate\Support\Facades\DB;

class DivisionController extends Controller
{
    /**
     * Liste des divisions avec services et agents
     */
    public function index()
    {
        $divisions = Division::with(['services' => function ($q) {
            $q->withCount('agents')->orderBy('nom_service');
        }])->orderBy('nom_division')->get();

        $totaux = [
            'divisions' => $divisions->count(),
            'services'  => $divisions->sum(fn($d) => $d->services->count()),
            'agents'    => $divisions->sum(fn($d) => $d->services->sum('agents_count')),
        ];

        return view('rh.divisions.index', compact('divisions', 'totaux'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        return view('rh.divisions.create');
    }

    /**
     * Enregistrer une division (Intégrité CID)
     */
    public function store(StoreDivisionRequest $request)
    {
        DB::transaction(function () use ($request) {
            $division = Division::create($request->validated());

            activity()
                ->causedBy(auth()->user())
                ->performedOn($division)
                ->withProperties(['nom' => $division->nom_division])
                ->log('Division créée');
        });

        return redirect()->route('rh.divisions.index')
            ->with('success', "La division \"{$request->nom_division}\" a été créée.");
    }

    /**
     * Formulaire d'édition
     */
    public function edit(int $id)
    {
        $division = Division::with('services')->findOrFail($id);
        return view('rh.divisions.edit', compact('division'));
    }

    /**
     * Mettre à jour une division
     */
    public function update(StoreDivisionRequest $request, int $id)
    {
        $division = Division::findOrFail($id);

        DB::transaction(function () use ($request, $division) {
            $division->update($request->validated());

            activity()
                ->causedBy(auth()->user())
                ->performedOn($division)
                ->withProperties($request->validated())
                ->log('Division modifiée');
        });

        return redirect()->route('rh.divisions.index')
            ->with('success', "La division \"{$division->nom_division}\" a été mise à jour.");
    }

    /**
     * Supprimer une division (seulement si vide)
     */
    public function destroy(int $id)
    {
        $division = Division::withCount('services')->findOrFail($id);

        if ($division->services_count > 0) {
            return back()->with('error', "Impossible de supprimer : {$division->services_count} service(s) appartiennent à cette division.");
        }

        DB::transaction(function () use ($division) {
            activity()->causedBy(auth()->user())->log("Division supprimée : {$division->nom_division}");
            $division->delete();
        });

        return redirect()->route('rh.divisions.index')
            ->with('success', "La division a été supprimée.");
    }
}
