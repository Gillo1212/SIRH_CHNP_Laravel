<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Rediriger vers le dashboard approprié selon le rôle
     */
    public function index()
    {
        $user = auth()->user();

        // Redirection selon le rôle (priorité : Admin > RH > Manager > Agent)
        if ($user->hasRole('AdminSystème')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('DRH')) {
            return redirect()->route('drh.dashboard');
        }

        if ($user->hasRole('AgentRH')) {
            return redirect()->route('rh.dashboard');
        }

        if ($user->hasRole('Manager')) {
            return redirect()->route('manager.dashboard');
        }

        // Par défaut : Agent (si l'utilisateur a le rôle Agent)
        if ($user->hasRole('Agent')) {
            return redirect()->route('agent.dashboard');
        }

        // Aucun rôle reconnu : rediriger vers le profil
        return redirect()->route('profile.edit')
            ->with('warning', 'Votre compte n\'a pas encore de rôle attribué. Contactez l\'administrateur.');
    }
}