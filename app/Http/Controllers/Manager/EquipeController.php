<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EquipeController extends Controller
{
    private function wip(string $title)
    {
        return view('layouts.partials.wip', ['pageTitle' => $title]);
    }

    public function index() { return $this->wip('Liste de mon équipe'); }
    public function dossiers() { return $this->wip('Dossiers des agents — Lecture seule'); }
    public function show($id) { return $this->wip('Dossier agent'); }
}
