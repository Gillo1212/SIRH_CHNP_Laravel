<?php

namespace App\Http\Controllers\DRH;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ValidationDRHController extends Controller
{
    private function wip(string $title)
    {
        return view('layouts.partials.wip', ['pageTitle' => $title]);
    }

    public function decisions()
    {
        return $this->wip('Décisions à signer');
    }

    public function signer($id)
    {
        return back()->with('success', 'Décision signée avec succès.');
    }

    public function mouvements()
    {
        return $this->wip('Mouvements stratégiques — Validation DRH');
    }

    public function validerMouvement($id)
    {
        return back()->with('success', 'Mouvement validé avec succès.');
    }

    public function pecExceptionnelles()
    {
        return $this->wip('PEC Exceptionnelles — Validation DRH');
    }

    public function validerPEC($id)
    {
        return back()->with('success', 'PEC validée avec succès.');
    }
}
