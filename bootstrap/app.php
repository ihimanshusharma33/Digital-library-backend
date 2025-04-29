<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\JwtVerifySelf;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register your middleware aliases here
        $middleware->alias([
            'jwt.auth' => JwtMiddleware::class,
            'jwt.staff' => JwtMiddleware::class.':staff',
            'jwt.admin' => JwtMiddleware::class.':admin',
            'jwt.verify.self' => JwtVerifySelf::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
