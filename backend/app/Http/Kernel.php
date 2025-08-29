<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\HandlePostTooLargeException::class,
            // ❌ REMOVE THIS LINE:
            // 'prevent-back-history' => \App\Http\Middleware\PreventBackHistory::class,
        ],

        'api' => [
            \App\Http\Middleware\Cors::class,
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Fruitcake\Cors\HandleCors::class, // Make sure this line exists
        ],
    ];

    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'prevent-back-history' => \App\Http\Middleware\PreventBackHistory::class,
        'rolemanager' => \App\Http\Middleware\RoleManager::class,
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'client' => \App\Http\Middleware\Client::class,
        'role' => \App\Http\Middleware\RoleMiddleware::class,
        'cors' => \App\Http\Middleware\Cors::class,
    ];
}
