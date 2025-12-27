<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            // 1. Check if Banned
            if ($user->status === 'banned') {
                // Revoke tokens so they can't try again immediately
                $user->tokens()->delete();
                return response()->json(['message' => 'Your account has been banned.'], 403);
            }

            // 2. Check if Soft Deleted (Optional, usually handled by Auth, but good for safety)
            if ($user->deleted_at !== null) {
                return response()->json(['message' => 'Account not found.'], 401);
            }
        }

        return $next($request);
    }
}
