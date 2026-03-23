<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GEDController extends Controller
{
    private function wip(string $title)
    {
        return view('layouts.partials.wip', ['pageTitle' => $title]);
    }

    public function index() { return $this->wip('GED — Tous les documents'); }
    public function create() { return $this->wip('Uploader un document'); }
    public function store(Request $request) { return redirect()->route('rh.documents.index')->with('success', 'Document uploadé.'); }
    public function search(Request $request) { return $this->wip('Recherche documents'); }
    public function show($id) { return $this->wip('Détail document'); }
}
