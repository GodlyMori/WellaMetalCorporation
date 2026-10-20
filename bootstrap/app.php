<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        __DIR__.'/../app/Console/Commands',
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson() || $request->hasHeader('X-Livewire'),
        );
        $exceptions->report(function (Throwable $e) {
            try {
                $payload = [
                    'timestamp' => now()->toIso8601String(),
                    'message' => $e->getMessage(),
                    'exception' => get_class($e),
                    'file' => $e->getFile().':'.$e->getLine(),
                    'url' => request()?->fullUrl(),
                    'method' => request()?->method(),
                    'ip' => request()?->ip(),
                ];
                @file_put_contents(storage_path('logs/last_500.json'), json_encode($payload, JSON_PRETTY_PRINT));
            } catch (Throwable) {
            }
        });
    })->create();

// Dynamic storage directory redirection for Vercel serverless environment
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || env('VERCEL')) {
    $app->useStoragePath('/tmp/storage');
}

return $app;
