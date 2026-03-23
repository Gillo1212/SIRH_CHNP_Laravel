<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PECAgentController extends Controller
{
    private function wip(string $title)
    {
        return view('layouts.partials.wip', ['pageTitle' => $title]);
    }

    public function index() { return $this->wip('Mes demandes de prise en charge'); }
    public function create(Request $request) { return $this->wip('Nouvelle demande de prise en charge'); }
    public function store(Request $request) { return redirect()->route('agent.pec.index')->with('success', 'Demande envoyée.'); }
    public function show($id) { return $this->wip('Détail prise en charge'); }
    public function download($id) { return $this->wip('Télécharger attestation'); }
}
