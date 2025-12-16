<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Check distinct handledBy values
echo "Distinct handledBy values in queries table:\n";
$values = DB::table('queries')->select('handledBy')->distinct()->pluck('handledBy');
print_r($values->toArray());

// Check counts
echo "\nTotal queries: " . DB::table('queries')->count() . "\n";
echo "Queries with handledBy='Bot': " . DB::table('queries')->where('handledBy', 'Bot')->count() . "\n";
echo "Queries with handledBy='bot': " . DB::table('queries')->where('handledBy', 'bot')->count() . "\n";
echo "Total hr_inbox: " . DB::table('hr_inbox')->count() . "\n";

// Test the union query
$queryInteractions = DB::table('queries')
    ->where('handledBy', 'Bot')
    ->select(
        DB::raw("DATE_FORMAT(questionTime, '%Y-%m-%d') as query_date"),
        DB::raw('TIMESTAMPDIFF(MICROSECOND, questionTime, IFNULL(responseTime, questionTime)) / 1000000.0 as response_time_seconds'),
        DB::raw('CASE WHEN responseTime IS NOT NULL AND responseTime != questionTime THEN 1 ELSE 0 END as has_valid_response_time'),
        DB::raw("'query' as interaction_type")
    );

$ticketInteractions = DB::table('hr_inbox')
    ->select(
        DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as query_date"),
        DB::raw('0 as response_time_seconds'),
        DB::raw('0 as has_valid_response_time'),
        DB::raw("'ticket' as interaction_type")
    );

$allInteractionsData = $queryInteractions->unionAll($ticketInteractions)->get();

echo "\nUNION Total Count: " . count($allInteractionsData) . "\n";
echo "First 3 items:\n";
foreach ($allInteractionsData->take(3) as $item) {
    echo json_encode($item) . "\n";
}
echo "\nLast 3 items:\n";
foreach ($allInteractionsData->slice(-3) as $item) {
    echo json_encode($item) . "\n";
}
