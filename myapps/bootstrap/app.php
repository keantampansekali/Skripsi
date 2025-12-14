<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle 419 CSRF token expired error
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->is('login') || $request->routeIs('login')) {
                return redirect()->route('login')
                    ->withErrors(['csrf' => 'Session expired. Silakan refresh halaman dan coba lagi.'])
                    ->withInput($request->except('password', '_token'));
            }
            
            return response()->view('errors.419', [], 419);
        });
    })->create();
