<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use App\Models\Query; // import your Query model
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\App;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {

        if (App::environment('production')) {
        URL::forceScheme('https');
    }
        // Optional: your DB::listen temporarily for debugging (remove later)
        DB::listen(function ($query) {
            $trace = collect(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS))
                        ->pluck('file')
                        ->filter()
                        ->take(10)
                        ->implode("\n");

            Log::info('SQL: '.$query->sql, [
                'bindings' => $query->bindings,
                'trace'    => $trace
            ]);
        });

        // <-- ADD THIS: ensure employeeNum is valid on every Query create
        Query::creating(function (Query $model) {
            // treat both null/empty and literal 0 as "not valid"
            $emp = $model->employeeNum ?? null;
            if (empty($emp) || (string)$emp === '0') {
                if (Auth::check()) {
                    // use logged-in user's employeeNum (your schema uses employeeNum as PK)
                    $model->employeeNum = Auth::user()->employeeNum;
                } else {
                    // fallback: use a reserved system id (must exist in users table)
                    $model->employeeNum = $employeeNum; // or 'GUEST' or 'BOT' — create this user (see Option B)
                }
            }
        });
    }
}
