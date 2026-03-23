<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AbsenceRHController extends Controller
{
    private function wip(string $title)
    {
        return view('layouts.partials.wip', ['pageTitle' => $title]);
    }

    public function index() { return $this->wip('Historique des absences'); }
    public function create() { return $this->wip('Enregistrer une absence'); }
    public function store(Request $request) { return redirect()->route('rh.absences.index')->with('success', 'Absence enregistrée.'); }
    public function show($id) { return $this->wip('Détail absence'); }
}
