<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            \Illuminate\Support\Facades\Route::middleware('api')
                ->group(base_path('routes/api.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminOnly::class,
            'api.key' => \App\Http\Middleware\ApiKeyMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->is('pengusulan/*') || $request->is('lke/*')) {
                $status = 500;
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                    $status = $e->getStatusCode();
                } elseif ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                    $status = 404;
                } elseif ($e instanceof \Illuminate\Validation\ValidationException) {
                    $status = 422;
                } elseif ($e instanceof \Illuminate\Auth\Access\AuthorizationException || $e instanceof \Illuminate\Auth\AuthenticationException) {
                    $status = 401;
                }

                $code = 'INTERNAL_SERVER_ERROR';
                $message = 'Error di sisi instansi';

                switch ($status) {
                    case 401:
                        $code = 'UNAUTHORIZED';
                        $message = 'API Key tidak valid atau tidak dikirimkan';
                        break;
                    case 403:
                        $code = 'FORBIDDEN';
                        $message = 'API Key valid tapi tidak berhak akses resource ini';
                        break;
                    case 404:
                        $code = 'DATA_NOT_FOUND';
                        $message = 'Data yang diminta tidak ditemukan';
                        break;
                    case 405:
                        $code = 'METHOD_NOT_ALLOWED';
                        $message = 'Metode request tidak valid';
                        break;
                    case 422:
                        $code = 'UNPROCESSABLE_ENTITY';
                        $message = 'Data request tidak valid';
                        break;
                    case 429:
                        $code = 'TOO_MANY_REQUESTS';
                        $message = 'Rate limit terlampaui';
                        break;
                }

                return response()->json([
                    'error' => true,
                    'code' => $code,
                    'message' => $message,
                ], $status);
            }
        });
    })->create();
