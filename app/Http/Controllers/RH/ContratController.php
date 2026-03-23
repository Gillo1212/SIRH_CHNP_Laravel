<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContratController extends Controller
{
    private function wip(string $title)
    {
        return view('layouts.partials.wip', ['pageTitle' => $title]);
    }

    public function index() { return $this->wip('Liste des contrats'); }
    public function create() { return $this->wip('Nouveau contrat'); }
    public function store(Request $request) { return redirect()->route('rh.contrats.index')->with('success', 'Contrat créé.'); }
    public function expiring() { return $this->wip('Contrats expirants'); }
    public function show($id) { return $this->wip('Détail contrat'); }
    public function edit($id) { return $this->wip('Modifier contrat'); }
    public function update(Request $request, $id) { return redirect()->route('rh.contrats.index')->with('success', 'Contrat mis à jour.'); }
}
