<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

$ticketNo = 'TKT-2SSDNTXX-1765876342';

echo "TICKET ASSIGNMENT CHECK:\n";
echo str_repeat("=", 60) . "\n\n";

$ticket = DB::table('hr_inbox')->where('ticket_no', $ticketNo)->first();

if ($ticket) {
    echo "Ticket: {$ticket->ticket_no}\n";
    echo "From: {$ticket->from_user}\n";
    echo "Message: {$ticket->message}\n";
    echo "Status: {$ticket->status}\n";
    echo "Priority: {$ticket->priority}\n";
    echo "Assigned To: " . ($ticket->assigned_to ?? 'NULL') . "\n";
    echo "Created At: {$ticket->created_at}\n";
    echo "\n";
} else {
    echo "Ticket not found!\n";
}

// Check active HR accounts
echo str_repeat("=", 60) . "\n";
$activeHR = DB::table('users')
    ->where('role', 'HR')
    ->where('status', 'Active')
    ->where('is_archived', 0)
    ->pluck('employeeNum')
    ->toArray();

echo "Active HR Accounts Available: " . count($activeHR) . "\n";
if (!empty($activeHR)) {
    echo "IDs: " . implode(', ', $activeHR) . "\n";
}
