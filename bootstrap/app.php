<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

        $middleware->redirectGuestsTo(function (Request $request) {
            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (\Illuminate\Http\Exceptions\PostTooLargeException $e, Request $request) {
            $msg = 'Ukuran file melebihi batas maksimal yang diperbolehkan server (maksimal 20 MB). Silakan gunakan file dengan ukuran lebih kecil.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $msg, 'errors' => ['file_proposal' => [$msg]]], 422);
            }
            return redirect()->back()->withErrors([
                'file_proposal' => $msg,
                'berkas' => $msg,
            ])->withInput();
        });
    })->create();
