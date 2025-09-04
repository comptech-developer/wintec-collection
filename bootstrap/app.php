<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Http\JsonResponse;
use App\Notifications\SlackErrorNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
         api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
         $middleware->group('api', [
        \App\Http\Middleware\LogApiRequests::class,
        \App\Http\Middleware\AccessTokenMiddleware::class,
    ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
       $exceptions->renderable(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return new JsonResponse([
                    'status'  => 'error',
                    'message' => 'HTTP method not allowed for this endpoint'
                ], 405);
            }
        });
      $exceptions->report(function (Throwable $exception) {
        // Only send in production
        if (app()->environment(['production','local'])) {
            try {
                Notification::route('slack', env('SLACK_WEBHOOK_URL'))
                            ->notify(new SlackErrorNotification($exception));
            } catch (\Exception $ex) {
                // Fail silently if Slack notification fails
                Log::error('Failed to send exception to Slack: ' . $ex->getMessage());
            }
        }
    });
    $exceptions->render(function (Throwable $exception) {
    $request = request(); // get the current request manually

    if ($request->wantsJson() || str($request->path())->startsWith('api')) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'code' => $exception->getCode() ?: 500
            ], $exception->getCode() ?: 500);
        }

        // fallback to default HTML
        throw $exception;
    });
    

    })->create();
