<?php

namespace App\Http\Middleware;

use App\Models\Service;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsureMajorService — Confidentialité CID
 * Vérifie que le major accède uniquement aux ressources de son service.
 */
class EnsureMajorService
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->hasRole('Major')) {
            abort(403, 'Accès réservé aux majors de service.');
        }

        $service = Service::where('id_agent_major', $user->id)->first();

        if (!$service) {
            return redirect()->route('major.dashboard')
                ->with('error', 'Votre compte Major n\'est pas assigné à un service. Contactez l\'administration.');
        }

        $request->merge(['_major_service' => $service]);
        $request->attributes->set('major_service', $service);

        return $next($request);
    }
}
