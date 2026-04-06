<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
        $middleware->alias([
            'check.account.locked'  => \App\Http\Middleware\CheckAccountLocked::class,
            'manager.service'       => \App\Http\Middleware\EnsureManagerService::class,
            'major.service'         => \App\Http\Middleware\EnsureMajorService::class,
            'agent.profile'         => \App\Http\Middleware\EnsureHasAgentProfile::class,
            'role'                  => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'            => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission'    => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Spatie Permission : afficher 403 au lieu de rediriger vers /login
        $exceptions->render(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Accès refusé. Vous n\'avez pas les permissions nécessaires.'], 403);
            }
            return response()->view('errors.403', [
                'message' => 'Vous n\'avez pas les permissions nécessaires pour accéder à cette page.',
            ], 403);
        });
    })->create();