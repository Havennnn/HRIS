<?php

namespace App\Http\Middleware\Api;

use App\Traits\BuildsApiResponses;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidApiKey
{
    use BuildsApiResponses;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->query('api_key') ?? $request->header('X-Api-Key');

        if ($apiKey !== config('app.api_key')) {
            return $this->unauthorizedResponse('Unauthorized or invalid key detected. Failed to access content.');
        }

        return $next($request);
    }
}
