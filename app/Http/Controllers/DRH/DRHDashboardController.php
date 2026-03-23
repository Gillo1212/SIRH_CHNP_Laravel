<?php

namespace App\Http\Controllers\DRH;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DRHDashboardController extends Controller
{
    public function index()
    {
        return view('drh.dashboard');
    }

    public function kpis()
    {
        // Vue KPIs stratégiques détaillés
        return view('drh.kpis');
    }

    public function budget()
    {
        // Vue suivi budgétaire RH
        return view('drh.budget');
    }
}
