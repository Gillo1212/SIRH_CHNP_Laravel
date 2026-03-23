<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest; // Notre version personnalisée
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Redirection selon le rôle
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $redirectUrl = match(true) {
            $user->hasRole('AdminSystème') => '/admin/dashboard',
            $user->hasRole('DRH')         => '/drh/dashboard',
            $user->hasRole('AgentRH')     => '/rh/dashboard',
            $user->hasRole('Manager')     => '/manager/dashboard',
            $user->hasRole('Agent')       => '/agent/dashboard',
            default                       => '/profile',
        };

        return redirect()->intended($redirectUrl);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Logger la déconnexion
        activity()
            ->causedBy(Auth::user())
            ->log('Déconnexion');

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
