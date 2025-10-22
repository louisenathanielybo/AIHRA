<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
{
    if (!Auth::check()) {
        \Log::warning('Auth check failed: user not logged in');
        return redirect()->route('login');
    }

    $user = Auth::user();
    $expected = strtolower($role);
    $actual = strtolower(trim($user->role ?? ''));

    if ($actual !== $expected) {
        \Log::warning('Role mismatch detected', [
            'expected' => $expected,
            'actual' => $actual,
            'employeeNum' => $user->employeeNum ?? 'N/A',
            'id' => $user->id ?? 'N/A',
        ]);

        // Instead of sending to login, just block access gracefully
        return redirect()->back()->with('error', 'You are not authorized to access this section.');
    }

    return $next($request);
}

}
