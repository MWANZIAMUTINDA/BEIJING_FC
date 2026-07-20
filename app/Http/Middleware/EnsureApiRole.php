<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureApiRole
{
    /**
     * Enforce role-based access on API routes.
     * Returns a JSON 403 response instead of a redirect.
     *
     * Usage: Route::middleware(['api.role:admin,coach'])
     */
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthenticated. Please provide a valid API token.',
            ], 401);
        }

        if (!$user->hasRole($roles)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Forbidden. You do not have permission to perform this action.',
                'required_roles' => $roles,
                'your_role'      => $user->role,
            ], 403);
        }

        return $next($request);
    }
}
