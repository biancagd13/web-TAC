<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // 1. Mantenemos tu excepción de CSRF para la API
        $middleware->validateCsrfTokens(except: [
            'api/*' 
        ]);

        // 2. NUEVA IMPLEMENTACIÓN: Habilita el estado de la API para Sanctum.
        // Esto permite que la App maneje sesiones por Token sin conflictos.
        $middleware->statefulApi();

        // 3. Mantenemos tus alias de Middleware originales (Rol)
        $middleware->alias([
            'rol' => \App\Http\Middleware\CheckRol::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();