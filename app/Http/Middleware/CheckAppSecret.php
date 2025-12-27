<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAppSecret
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $validSecret = env('APP_MOBILE_SECRET', 'my_super_secret_key_123');

        if ($request->header('X-App-Key') !== $validSecret) {
            return response()->json(['message' => 'Unauthorized App Client'], 403);
        }

        return $next($request);
    }
}
