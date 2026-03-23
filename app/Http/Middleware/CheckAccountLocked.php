<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CheckAccountLocked
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            
            if ($user->estVerouille()) {
                Auth::logout();
                
                return redirect()->route('login')
                    ->withErrors(['email' => 'Votre compte a été verouillé. Contactez l\'administrateur.']);
            }
        }

        return $next($request);
    }
}
