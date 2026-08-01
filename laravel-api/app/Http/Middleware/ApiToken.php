<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        $expectedToken = config('app.api_token')
            ?? env('BOT_API_TOKEN')
            ?? \App\Models\SiteSetting::get('bot_api_token');

        if (empty($expectedToken) || $token !== $expectedToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
