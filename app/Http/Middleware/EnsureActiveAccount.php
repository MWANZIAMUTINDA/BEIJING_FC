<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureActiveAccount
{
    /**
     * Redirect deactivated users out of authenticated web sessions immediately.
     * This prevents mid-session access after an admin disables an account.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (auth()->check() && !auth()->user()->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Your account has been deactivated. Please contact an administrator.');
        }

        return $next($request);
    }
}
