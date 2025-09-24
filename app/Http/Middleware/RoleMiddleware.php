<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = strtolower(trim(Auth::user()->role ?? ''));

        if ($userRole !== strtolower($role)) {
            // Optional logging for debugging
            \Log::warning('Role mismatch', [
                'expected' => $role,
                'actual'   => Auth::user()->role,
                'user_id'  => Auth::id()
            ]);

            \Log::info('Middleware role check', [
    'expected' => $role,
    'actual' => Auth::user()->role,
    'user_id' => Auth::id()
            ]);

            
            return redirect()->route('login')->with('error', 'Unauthorized access');
        }

        return $next($request);
    }

    
}
