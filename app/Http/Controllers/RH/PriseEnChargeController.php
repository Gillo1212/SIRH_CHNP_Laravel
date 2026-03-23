<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Models\PriseEnCharge;
use Illuminate\Http\Request;

class PriseEnChargeController extends Controller
{
    public function index()
    {
        $prises = PriseEnCharge::with('agent')->latest()->paginate(15);
        return view('rh.prises-en-charge.index', compact('prises'));
    }

    public function create()
    {
        return view('rh.prises-en-charge.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'agent_id'       => 'required|exists:agents,id',
            'beneficiaire'   => 'required|string|in:Agent,Conjoint,Enfant',
            'type_prise'     => 'required|string',
            'date_debut'     => 'required|date',
            'date_fin'       => 'nullable|date|after_or_equal:date_debut',
            'description'    => 'nullable|string|max:1000',
        ]);

        PriseEnCharge::create($validated);

        return redirect()->route('pec.index')->with('success', 'Prise en charge enregistrée.');
    }

    public function show(int $id)
    {
        $prise = PriseEnCharge::with('agent')->findOrFail($id);
        return view('rh.prises-en-charge.show', compact('prise'));
    }
}
