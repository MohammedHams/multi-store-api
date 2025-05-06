<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Exceptions\UnauthorizedAccessException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register your middleware with an alias
        $middleware->alias([
            'user.type' => \App\Http\Middleware\CheckUserType::class,
        ]);

        // Or if you want to add it to a middleware group:
        // $middleware->appendToGroup('web', \App\Http\Middleware\CheckUserType::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // You can register exception handling here if needed
        $exceptions->renderable(function (UnauthorizedAccessException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 403);
        });
    })
    ->create();
