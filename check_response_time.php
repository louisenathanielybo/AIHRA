<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$query = DB::table('queries')->orderBy('queryID', 'desc')->first();

if ($query) {
    echo "Latest Query:\n";
    echo "Question Time: " . $query->questionTime . "\n";
    echo "Response Time: " . $query->responseTime . "\n";
    
    $result = DB::table('queries')
        ->select(DB::raw('TIMESTAMPDIFF(MICROSECOND, questionTime, IFNULL(responseTime, questionTime)) / 1000000.0 as response_time_seconds'))
        ->where('queryID', $query->queryID)
        ->first();
    
    echo "Calculated Response Time: " . $result->response_time_seconds . " seconds\n";
    echo "Formatted: " . number_format($result->response_time_seconds, 2) . "s\n";
} else {
    echo "No queries found\n";
}
