<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\Cors;
use App\Providers\Filament\AdminPanelProvider;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // CORS middleware is applied in API routes, not globally


    })
    ->withProviders([
        AdminPanelProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
