<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Mouvement;
use Illuminate\Http\Request;

class DocumentAdminController extends Controller
{
    public function index()
    {
        return view('rh.documents-admin.index');
    }

    public function attestation(Agent $agent)
    {
        // Génère une attestation de travail PDF via DomPDF
        return view('rh.documents-admin.attestation', compact('agent'));
    }

    public function certificat(Agent $agent)
    {
        return view('rh.documents-admin.certificat', compact('agent'));
    }

    public function decisionAffectation(Mouvement $mouvement)
    {
        return view('rh.documents-admin.decision-affectation', compact('mouvement'));
    }

    public function ordreMission(Agent $agent)
    {
        return view('rh.documents-admin.ordre-mission', compact('agent'));
    }
}
