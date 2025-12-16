<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TrackLastSeen
{
    /**
     * Handle an incoming request.
     * Updates the user's last_seen_at timestamp on each request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // Update last_seen_at timestamp for the authenticated user
            DB::table('users')
                ->where('employeeNum', Auth::user()->employeeNum)
                ->update(['last_seen_at' => now()]);
        }

        return $next($request);
    }

    /**
     * Check if a user is considered "online" (active within the last X minutes)
     *
     * @param string $employeeNum
     * @param int $minutes - Minutes threshold to consider user as online (default: 5)
     * @return bool
     */
    public static function isUserOnline(string $employeeNum, int $minutes = 5): bool
    {
        $user = DB::table('users')
            ->where('employeeNum', $employeeNum)
            ->first(['last_seen_at']);

        if (!$user || !$user->last_seen_at) {
            return false;
        }

        $lastSeen = \Carbon\Carbon::parse($user->last_seen_at);
        return $lastSeen->greaterThan(now()->subMinutes($minutes));
    }
}
