<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "HR ACCOUNTS ANALYSIS:\n";
echo str_repeat("=", 60) . "\n\n";

$allHR = DB::table('users')->where('role', 'HR')->get(['employeeNum', 'status', 'is_archived']);
echo "Total HR Accounts: " . $allHR->count() . "\n\n";

foreach ($allHR as $hr) {
    $archived = $hr->is_archived ? 'ARCHIVED' : 'Not Archived';
    echo sprintf("%-15s - Status: %-12s - %s\n", $hr->employeeNum, $hr->status, $archived);
}

echo "\n" . str_repeat("=", 60) . "\n";

$activeHR = DB::table('users')
    ->where('role', 'HR')
    ->where('status', 'Active')
    ->where('is_archived', 0)
    ->pluck('employeeNum')
    ->toArray();

echo "\nActive HR Accounts (for assignment): " . count($activeHR) . "\n";
if (!empty($activeHR)) {
    echo "IDs: " . implode(', ', $activeHR) . "\n";
} else {
    echo "⚠️  WARNING: No active HR accounts found!\n";
    echo "    Tickets will be created as 'Unassigned'\n";
}
