<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api/v1',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->prepend(ForceJsonResponse::class);

        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $e, Request $request) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first() ?: 'The given data was invalid.',
                'errors' => $e->errors(),
            ], 422);
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please provide a valid bearer token.',
                'errors' => null,
            ], 401);
        });

        $exceptions->render(function (AuthorizationException $e, Request $request) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'This action is unauthorized.',
                'errors' => null,
            ], 403);
        });

        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            $modelName = class_basename($e->getModel());
            return response()->json([
                'success' => false,
                'message' => "Requested {$modelName} not found.",
                'errors' => null,
            ], 404);
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            return response()->json([
                'success' => false,
                'message' => 'The requested endpoint or resource was not found.',
                'errors' => null,
            ], 404);
        });

        $exceptions->render(function (HttpExceptionInterface $e, Request $request) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'HTTP error occurred.',
                'errors' => null,
            ], $e->getStatusCode());
        });

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $status = 500;
                $message = config('app.debug') ? $e->getMessage() : 'An unexpected error occurred. Please try again later.';

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'errors' => config('app.debug') ? [
                        'exception' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ] : null,
                ], $status);
            }
        });
    })->create();

