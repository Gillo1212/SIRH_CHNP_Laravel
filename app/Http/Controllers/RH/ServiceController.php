<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    private function wip(string $title)
    {
        return view('layouts.partials.wip', ['pageTitle' => $title]);
    }

    public function index() { return $this->wip('Liste des services'); }
    public function create() { return $this->wip('Nouveau service'); }
    public function store(Request $request) { return redirect()->route('rh.services.index')->with('success', 'Service créé.'); }
    public function edit($id) { return $this->wip('Modifier service'); }
    public function update(Request $request, $id) { return redirect()->route('rh.services.index')->with('success', 'Service mis à jour.'); }
    public function assignerManager($id) { return back()->with('success', 'Manager assigné.'); }
}
