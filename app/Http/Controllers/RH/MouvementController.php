<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Models\Mouvement;
use Illuminate\Http\Request;

class MouvementController extends Controller
{
    public function index()
    {
        $mouvements = Mouvement::with('agent')->latest()->paginate(15);
        return view('rh.mouvements.index', compact('mouvements'));
    }

    public function affectations()
    {
        $mouvements = Mouvement::with('agent')->where('type_mouvement', 'Affectation')->latest()->paginate(15);
        return view('rh.mouvements.index', compact('mouvements'));
    }

    public function mutations()
    {
        $mouvements = Mouvement::with('agent')->where('type_mouvement', 'Mutation')->latest()->paginate(15);
        return view('rh.mouvements.index', compact('mouvements'));
    }

    public function departs()
    {
        $mouvements = Mouvement::with('agent')->whereIn('type_mouvement', ['Depart', 'Retraite'])->latest()->paginate(15);
        return view('rh.mouvements.index', compact('mouvements'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'agent_id'         => 'required|exists:agents,id',
            'type_mouvement'   => 'required|string',
            'date_mouvement'   => 'required|date',
            'motif'            => 'nullable|string|max:500',
            'service_origine'  => 'nullable|exists:services,id',
            'service_destination' => 'nullable|exists:services,id',
        ]);

        Mouvement::create($validated);

        return redirect()->route('mouvements.index')->with('success', 'Mouvement enregistré avec succès.');
    }
}
