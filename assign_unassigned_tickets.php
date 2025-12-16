<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "UPDATING UNASSIGNED TICKETS:\n";
echo str_repeat("=", 60) . "\n\n";

// Get active HR
$activeHR = DB::table('users')
    ->where('role', 'HR')
    ->where('status', 'Active')
    ->where('is_archived', 0)
    ->pluck('employeeNum')
    ->toArray();

if (empty($activeHR)) {
    echo "No active HR accounts found. Cannot assign tickets.\n";
    exit;
}

echo "Active HR: " . implode(', ', $activeHR) . "\n\n";

// Find unassigned open tickets
$unassignedTickets = DB::table('hr_inbox')
    ->whereNull('assigned_to')
    ->whereIn('status', ['Open', 'Replied'])
    ->get();

echo "Found " . $unassignedTickets->count() . " unassigned tickets\n\n";

foreach ($unassignedTickets as $ticket) {
    $assignTo = $activeHR[array_rand($activeHR)];
    
    DB::table('hr_inbox')
        ->where('ticket_no', $ticket->ticket_no)
        ->update(['assigned_to' => $assignTo]);
    
    echo "✓ Assigned {$ticket->ticket_no} to {$assignTo}\n";
}

echo "\nDone!\n";
