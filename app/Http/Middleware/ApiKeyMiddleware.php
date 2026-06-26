<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expectedKey = '6a60009b36df646dc553e7c5bc5ab904';
        
        $apiKey = $request->bearerToken();

        if ($apiKey !== $expectedKey) {
            return response()->json([
                'error' => true,
                'code' => 'UNAUTHORIZED',
                'message' => 'API Key tidak valid atau tidak dikirimkan'
            ], 401);
        }

        return $next($request);
    }
}
