<?php

namespace App\Http\Middleware;

use App\Models\Service;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsureManagerService — Confidentialité CID
 * Vérifie que le manager accède uniquement aux ressources de son service.
 */
class EnsureManagerService
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->hasRole('Manager')) {
            abort(403, 'Accès réservé aux managers.');
        }

        // Le manager doit être assigné à un service
        $service = Service::where('id_agent_manager', $user->id)->first();

        if (!$service) {
            return redirect()->route('manager.dashboard')
                ->with('error', 'Votre compte Manager n\'est pas assigné à un service. Contactez l\'administration.');
        }

        // Partager le service dans la requête pour éviter les requêtes redondantes
        $request->merge(['_manager_service' => $service]);
        $request->attributes->set('manager_service', $service);

        return $next($request);
    }
}
