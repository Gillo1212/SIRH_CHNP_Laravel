<?php

namespace App\Http\Controllers\DRH;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DecisionController extends Controller
{
    public function index()
    {
        // Liste des décisions RH en attente de signature DRH
        return view('drh.decisions.index');
    }

    public function signer(int $id)
    {
        // Signature électronique d'une décision RH
        // À implémenter avec le modèle Decision
        return redirect()->route('drh.decisions.index')->with('success', 'Décision signée avec succès.');
    }
}
