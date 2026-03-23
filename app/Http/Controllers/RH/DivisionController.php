<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    private function wip(string $title)
    {
        return view('layouts.partials.wip', ['pageTitle' => $title]);
    }

    public function index() { return $this->wip('Liste des divisions'); }
    public function create() { return $this->wip('Nouvelle division'); }
    public function store(Request $request) { return redirect()->route('rh.divisions.index')->with('success', 'Division créée.'); }
    public function edit($id) { return $this->wip('Modifier division'); }
    public function update(Request $request, $id) { return redirect()->route('rh.divisions.index')->with('success', 'Division mise à jour.'); }
}
