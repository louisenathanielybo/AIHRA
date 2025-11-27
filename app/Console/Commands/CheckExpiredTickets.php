<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TicketExpirationService;

class CheckExpiredTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and mark expired tickets based on SLA response and resolution deadlines';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired tickets...');
        
        $service = new TicketExpirationService();
        $result = $service->checkExpiredTickets();
        
        if ($result['success']) {
            $this->info("✅ Expiration check completed");
            $this->info("📊 Expired tickets found: {$result['expired_count']}");
            
            // Show statistics
            $stats = $service->getExpirationStats();
            $this->newLine();
            $this->info("📈 Ticket Statistics:");
            $this->line("   Total expired: {$stats['expired_total']}");
            $this->line("   Expired & still open: {$stats['expired_open']}");
            $this->line("   Expiring in next hour: {$stats['expiring_next_hour']}");
            $this->line("   Urgent tickets not responded: {$stats['urgent_not_responded']}");
            
            return Command::SUCCESS;
        } else {
            $this->error("❌ Expiration check failed: {$result['error']}");
            return Command::FAILURE;
        }
    }
}
