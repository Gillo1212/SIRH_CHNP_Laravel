<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasAgentProfile
{
    /**
     * Permet aux employés hospitaliers (Agent, Manager, AgentRH, DRH)
     * d'accéder aux routes self-service /agent/*.
     * L'AdminSystème est exclu car il n'est pas nécessairement un agent de l'hôpital.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // AdminSystème : pas d'accès self-service agent
        if ($user->hasRole('AdminSystème')) {
            abort(403, 'Accès réservé aux agents de l\'hôpital.');
        }

        // Doit avoir un profil agent lié
        if (! $user->agent) {
            abort(403, 'Aucun profil agent associé à votre compte. Contactez le service RH.');
        }

        return $next($request);
    }
}
