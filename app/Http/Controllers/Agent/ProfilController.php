<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    public function index(Request $request)
    {
        $user  = $request->user();
        $agent = $user->agent;

        if (!$agent) {
            return redirect()->route('agent.dashboard')
                ->with('error', 'Aucun dossier agent associé à votre compte.');
        }

        $agent->load(['service', 'division', 'enfants', 'conjoints', 'contratActif']);

        return view('agent.profil', compact('agent'));
    }

    public function famille(Request $request)
    {
        $user  = $request->user();
        $agent = $user->agent;

        if (!$agent) {
            return redirect()->route('agent.dashboard')
                ->with('error', 'Aucun dossier agent associé à votre compte.');
        }

        $agent->load(['enfants', 'conjoints']);

        return view('agent.famille', compact('agent'));
    }
}
