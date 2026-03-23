<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PlanningManagerController extends Controller
{
    private function wip(string $title)
    {
        return view('layouts.partials.wip', ['pageTitle' => $title]);
    }

    public function index() { return $this->wip('Planning en cours'); }
    public function create() { return $this->wip('Créer un planning mensuel'); }
    public function store(Request $request) { return redirect()->route('manager.planning.index')->with('success', 'Planning créé.'); }
    public function show($id) { return $this->wip('Détail planning'); }
    public function transmettre($id) { return back()->with('success', 'Planning transmis à RH.'); }
}
