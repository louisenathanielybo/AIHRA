<?php

namespace App\Services;

use App\Models\HrInbox;
use Illuminate\Support\Facades\Log;

class TicketExpirationService
{
    /**
     * Check and mark expired tickets based on response deadlines
     * This should be run periodically (e.g., every 15 minutes via scheduled task)
     */
    public function checkExpiredTickets(): array
    {
        $now = now();
        $expiredCount = 0;
        
        try {
            // Find tickets that have passed their response deadline
            // and haven't been responded to yet (responded_at is null)
            $expiredTickets = HrInbox::where('is_expired', false)
                ->whereNull('responded_at')
                ->where('response_deadline', '<', $now)
                ->whereIn('status', ['Open', 'Waiting for HR'])
                ->get();

            foreach ($expiredTickets as $ticket) {
                $ticket->is_expired = true;
                $ticket->save();
                $expiredCount++;
                
                Log::info("Ticket expired", [
                    'ticket_no' => $ticket->ticket_no,
                    'priority' => $ticket->priority,
                    'response_deadline' => $ticket->response_deadline,
                    'created_at' => $ticket->created_at
                ]);
            }

            // Also check for tickets that have been responded to but not resolved
            // within their resolution deadline
            $overdueResolution = HrInbox::where('is_expired', false)
                ->whereNotNull('responded_at')
                ->whereNull('resolved_at')
                ->where('resolution_deadline', '<', $now)
                ->whereNotIn('status', ['Resolved'])
                ->get();

            foreach ($overdueResolution as $ticket) {
                $ticket->is_expired = true;
                $ticket->save();
                $expiredCount++;
                
                Log::info("Ticket resolution overdue", [
                    'ticket_no' => $ticket->ticket_no,
                    'priority' => $ticket->priority,
                    'resolution_deadline' => $ticket->resolution_deadline,
                    'responded_at' => $ticket->responded_at
                ]);
            }

            Log::info("Expiration check completed", [
                'expired_count' => $expiredCount,
                'checked_at' => $now
            ]);

            return [
                'success' => true,
                'expired_count' => $expiredCount,
                'checked_at' => $now
            ];

        } catch (\Exception $e) {
            Log::error("Ticket expiration check failed: " . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get tickets expiring soon (within the next hour)
     */
    public function getTicketsExpiringSoon(): array
    {
        $now = now();
        $oneHourFromNow = $now->copy()->addHour();
        
        $expiringSoon = HrInbox::where('is_expired', false)
            ->whereNull('responded_at')
            ->whereBetween('response_deadline', [$now, $oneHourFromNow])
            ->whereIn('status', ['Open', 'Waiting for HR'])
            ->orderBy('response_deadline', 'asc')
            ->get();

        return $expiringSoon->map(function ($ticket) {
            return [
                'ticket_no' => $ticket->ticket_no,
                'priority' => $ticket->priority,
                'from_user' => $ticket->from_user,
                'response_deadline' => $ticket->response_deadline,
                'time_remaining' => $ticket->response_deadline->diffForHumans(),
                'message' => substr($ticket->message, 0, 100)
            ];
        })->toArray();
    }

    /**
     * Get statistics about expired and expiring tickets
     */
    public function getExpirationStats(): array
    {
        $now = now();
        
        return [
            'expired_total' => HrInbox::where('is_expired', true)->count(),
            'expired_open' => HrInbox::where('is_expired', true)
                ->whereIn('status', ['Open', 'Replied', 'Waiting for HR'])
                ->count(),
            'expiring_next_hour' => HrInbox::where('is_expired', false)
                ->whereNull('responded_at')
                ->where('response_deadline', '>=', $now)
                ->where('response_deadline', '<=', $now->copy()->addHour())
                ->count(),
            'urgent_not_responded' => HrInbox::where('priority', 'urgent')
                ->whereNull('responded_at')
                ->whereIn('status', ['Open', 'Waiting for HR'])
                ->count(),
        ];
    }
}
