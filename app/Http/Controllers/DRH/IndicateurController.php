<?php

namespace App\Http\Controllers\DRH;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndicateurController extends Controller
{
    private function wip(string $title)
    {
        return view('layouts.partials.wip', ['pageTitle' => $title]);
    }

    public function effectifs()
    {
        return $this->wip('Indicateur — Effectifs');
    }

    public function turnover()
    {
        return $this->wip('Indicateur — Turnover');
    }

    public function absenteisme()
    {
        return $this->wip('Indicateur — Absentéisme');
    }

    public function pyramideAges()
    {
        return $this->wip('Indicateur — Pyramide des âges');
    }
}
