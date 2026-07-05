<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRole
{
    /**
     * Handle an incoming request.
     * Usage: Route::middleware(['role:admin,treasurer'])
     */
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        if (! auth()->user()->hasRole($roles)) {
            abort(403, 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}
