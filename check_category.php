<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

// Check table structure
echo "HR_INBOX TABLE STRUCTURE:\n";
echo str_repeat("=", 50) . "\n";
$columns = DB::select('SHOW COLUMNS FROM hr_inbox');
foreach ($columns as $col) {
    echo sprintf("%-20s %-30s\n", $col->Field, $col->Type);
}

echo "\n\nRECENT RESOLVED TICKETS WITH CATEGORIES:\n";
echo str_repeat("=", 50) . "\n";
$tickets = DB::table('hr_inbox')
    ->where('status', 'Resolved')
    ->orderBy('resolved_at', 'desc')
    ->limit(10)
    ->get(['ticket_no', 'message', 'category', 'status', 'resolved_at']);

foreach ($tickets as $ticket) {
    echo "Ticket: {$ticket->ticket_no}\n";
    echo "Category: " . ($ticket->category ?? 'NULL') . "\n";
    echo "Status: {$ticket->status}\n";
    echo "Resolved: {$ticket->resolved_at}\n";
    echo str_repeat("-", 50) . "\n";
}
