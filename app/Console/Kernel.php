<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Check for expired tickets every 15 minutes
        $schedule->call(function () {
            $service = new \App\Services\TicketExpirationService();
            $service->checkExpiredTickets();
        })->everyFifteenMinutes();

        // Delete expired announcements daily at midnight
        $schedule->call(function () {
            \Illuminate\Support\Facades\DB::table('announcements')
                ->where('expiry_date', '<', now()->toDateString())
                ->delete();
        })->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
