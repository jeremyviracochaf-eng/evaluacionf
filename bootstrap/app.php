<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Crear y configurar la aplicación

return Application::configure(basePath: dirname(__DIR__))

    // Definir las rutas
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    // Configurar middleware
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web([
            // middleware para web
        ]);

        $middleware->api([
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->alias([
            'is_admin' => \App\Http\Middleware\IsAdmin::class,
        ]);

        // Permitir CORS
        $middleware->trustProxies(at: '*');

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
