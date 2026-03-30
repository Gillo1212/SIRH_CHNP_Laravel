<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Http\Requests\RH\StoreDivisionRequest;
use App\Models\Division;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class DivisionController extends Controller
{
    /**
     * Liste des services avec leurs divisions (hiérarchie correcte)
     */
    public function index()
    {
        $services = Service::with(['divisions' => function ($q) {
            $q->withCount('agents')->orderBy('nom_division');
        }])->orderBy('nom_service')->get();

        $totaux = [
            'services'   => $services->count(),
            'divisions'  => $services->sum(fn($s) => $s->divisions->count()),
            'agents'     => $services->sum(fn($s) => $s->divisions->sum('agents_count')),
        ];

        return view('rh.divisions.index', compact('services', 'totaux'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        $services = Service::orderBy('nom_service')->get(['id_service', 'nom_service']);
        return view('rh.divisions.create', compact('services'));
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
        $division = Division::with('service')->findOrFail($id);
        $services = Service::orderBy('nom_service')->get(['id_service', 'nom_service']);
        return view('rh.divisions.edit', compact('division', 'services'));
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
     * Supprimer une division (seulement si aucun agent affecté)
     */
    public function destroy(int $id)
    {
        $division = Division::withCount('agents')->findOrFail($id);

        if ($division->agents_count > 0) {
            return back()->with('error', "Impossible de supprimer : {$division->agents_count} agent(s) sont affectés à cette division.");
        }

        DB::transaction(function () use ($division) {
            activity()->causedBy(auth()->user())->log("Division supprimée : {$division->nom_division}");
            $division->delete();
        });

        return redirect()->route('rh.divisions.index')
            ->with('success', "La division a été supprimée.");
    }
}
