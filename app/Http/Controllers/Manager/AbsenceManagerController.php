<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AbsenceManagerController extends Controller
{
    private function wip(string $title)
    {
        return view('layouts.partials.wip', ['pageTitle' => $title]);
    }

    public function index() { return $this->wip('Historique absences équipe'); }
    public function create() { return $this->wip('Enregistrer une absence'); }
    public function store(Request $request) { return redirect()->route('manager.absences.index')->with('success', 'Absence enregistrée.'); }
}
