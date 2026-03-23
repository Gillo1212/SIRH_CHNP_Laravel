<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PlanningRHController extends Controller
{
    private function wip(string $title)
    {
        return view('layouts.partials.wip', ['pageTitle' => $title]);
    }

    public function index() { return $this->wip('Tous les plannings'); }
    public function pending() { return $this->wip('Plannings à valider'); }
    public function show($id) { return $this->wip('Détail planning'); }
    public function valider($id) { return back()->with('success', 'Planning validé.'); }
}
