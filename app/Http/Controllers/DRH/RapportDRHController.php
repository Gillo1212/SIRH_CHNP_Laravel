<?php

namespace App\Http\Controllers\DRH;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RapportDRHController extends Controller
{
    public function bilanSocial()
    {
        // Génération du bilan social annuel
        return view('drh.rapports.bilan-social');
    }
}
