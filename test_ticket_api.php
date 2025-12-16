<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

// Get the most recently resolved ticket that was marked as "Employee Development"
$ticket = DB::table('hr_inbox')
    ->where('category', 'Employee Development')
    ->where('status', 'Resolved')
    ->orderBy('resolved_at', 'desc')
    ->first();

if ($ticket) {
    echo "Testing ticket API for: {$ticket->ticket_no}\n";
    echo str_repeat("=", 60) . "\n\n";
    
    echo "DATABASE RAW DATA:\n";
    echo "Ticket No: {$ticket->ticket_no}\n";
    echo "Category: " . ($ticket->category ?? 'NULL') . "\n";
    echo "Intent: " . ($ticket->intent ?? 'NULL') . "\n";
    echo "Status: {$ticket->status}\n";
    echo "Message: " . substr($ticket->message, 0, 100) . "...\n";
    echo "Resolved At: {$ticket->resolved_at}\n";
    echo "Resolved By: " . ($ticket->resolved_by ?? 'NULL') . "\n";
    
    echo "\n" . str_repeat("=", 60) . "\n\n";
    
    // Simulate what the API returns
    $ticketData = [
        'ticket_no' => $ticket->ticket_no ?? 'Unknown',
        'from_user' => $ticket->from_user ?? 'Unknown',
        'message' => $ticket->message ?? 'No message',
        'priority' => $ticket->priority ?? 'medium',
        'status' => $ticket->status ?? 'Open',
        'category' => $ticket->category ?? 'General',
        'intent' => $ticket->intent ?? 'N/A',
        'confidence' => $ticket->confidence ?? 0.0,
        'created_at' => $ticket->created_at,
        'updated_at' => $ticket->updated_at,
        'resolved_by' => $ticket->resolved_by ?? null,
    ];
    
    echo "API RESPONSE SIMULATION:\n";
    echo json_encode(['success' => true, 'ticket' => $ticketData], JSON_PRETTY_PRINT);
    
    echo "\n\n" . str_repeat("=", 60) . "\n";
    echo "If the category shows correctly above, the issue is browser cache.\n";
    echo "Try: Ctrl+Shift+R (hard refresh) or clear browser cache.\n";
} else {
    echo "No tickets found with category 'Employee Development'\n";
}
