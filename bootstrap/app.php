<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Nette\Schema\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectTo(
            guests: '/erp/login',
            users: '/erp',
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->render(function (Throwable $e, Request $request) {
            if ($e instanceof AuthenticationException || $e instanceof ValidationException) {
                return null;
            }
            if (config('app.debug') && !$request->has('show_technical_details')) {
                return response()->view('errors.custom_error', [
                    'exception' => $e,
                ], 500);
            }
            return null; 
        });
    })->create();