<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DemandeDocController extends Controller
{
    private function wip(string $title)
    {
        return view('layouts.partials.wip', ['pageTitle' => $title]);
    }

    public function index() { return $this->wip('Demandes de documents — Historique'); }
    public function pending() { return $this->wip('Demandes de documents en attente'); }
    public function show($id) { return $this->wip('Détail demande de document'); }
    public function traiter($id) { return back()->with('success', 'Demande traitée avec succès.'); }
    public function rejeter($id) { return back()->with('success', 'Demande rejetée.'); }
}
