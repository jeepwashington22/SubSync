<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // When the POST body exceeds PHP's post_max_size, PHP empties the
        // entire request (including the CSRF token) before Laravel runs.
        // Render a friendly redirect back with an error instead of a 419/500.
        $exceptions->render(function (PostTooLargeException $e, Request $request) {
            if ($request->is('profile*')) {
                return redirect()
                    ->route('profile.edit')
                    ->with('error', 'The photo is too large. Please choose an image under 10MB.');
            }

            return null;
        });
    })->create();
