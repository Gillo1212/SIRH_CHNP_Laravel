<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RapportRHController extends Controller
{
    private function wip(string $title)
    {
        return view('layouts.partials.wip', ['pageTitle' => $title]);
    }

    public function index() { return $this->wip('Rapports RH'); }
    public function mensuel() { return $this->wip('Rapport mensuel'); }
    public function effectifs() { return $this->wip('Rapport effectifs'); }
    public function stats() { return $this->wip('Statistiques RH'); }
    public function export() { return $this->wip('Export Excel / PDF'); }
}
