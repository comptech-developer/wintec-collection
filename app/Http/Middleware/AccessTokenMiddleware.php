<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AccessTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    { 

        $header = $request->header('Authorization');

    if (!$header || !str_starts_with($header, 'Bearer ')) {
        return response()->json([
            'status'  => 'fail',
            'message' => 'Missing Authorization header or wrong format.',
        ], 401);
    }

    // Get only the token (remove "Bearer ")
    $incomingToken = trim(substr($header, 7));
    $expectedToken = config('services.selcom_access_token') ?? env('ACCESS_TOKEN');
    Log::info('access token:' . $expectedToken);

    if ($incomingToken !== $expectedToken) {
        return response()->json([
            'status'  => 'fail',
            'message' => 'Unauthorized. Invalid access token.',
        ], 401);
    }

    return $next($request);
    
    }

    
}
