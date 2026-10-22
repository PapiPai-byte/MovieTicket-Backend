<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== UserRole::ADMIN) {
            return response()->json([
                'success' => false,
                'message' => 'Admin privileges required to access this resource.',
                'data' => null,
                'errors' => null,
            ], 403);
        }

        return $next($request);
    }
}

