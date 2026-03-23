<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DocAdminAgentController extends Controller
{
    private function wip(string $title)
    {
        return view('layouts.partials.wip', ['pageTitle' => $title]);
    }

    public function index() { return $this->wip('Mes demandes de documents administratifs'); }
    public function create() { return $this->wip('Nouvelle demande de document'); }
    public function store(Request $request) { return redirect()->route('agent.docs.index')->with('success', 'Demande envoyée.'); }
    public function show($id) { return $this->wip('Détail demande de document'); }
    public function download($id) { return $this->wip('Télécharger document'); }
}
