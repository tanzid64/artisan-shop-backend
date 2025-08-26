<?php

use App\Http\Middleware\CORSMiddleware;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            // Admin Routes
            Route::middleware(['api', 'auth:sanctum', 'role:admin'])
                ->prefix('api/admin')
                ->as('admin.')
                ->group(base_path('routes/admin.php'));

            // Vendor Routes
            Route::middleware(['api', 'auth:sanctum', 'role:vendor'])
                ->prefix('api/vendor')
                ->as('vendor.')
                ->group(base_path('routes/vendor.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
        // Cors Middleware for all api request
        $middleware->group('api', [CORSMiddleware::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
