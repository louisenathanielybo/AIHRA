<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$nullScoreQueries = DB::table('queries')->whereNull('confidenceScore')->pluck('queryID');
echo 'Found ' . $nullScoreQueries->count() . ' queries with NULL confidenceScore' . PHP_EOL;

// Delete related flagged responses first
$deletedFlags = DB::table('flaggedresponse')->whereIn('queryID', $nullScoreQueries)->delete();
echo 'Deleted ' . $deletedFlags . ' related flagged responses' . PHP_EOL;

// Now delete the queries
$deletedQueries = DB::table('queries')->whereNull('confidenceScore')->delete();
echo 'Deleted ' . $deletedQueries . ' queries with NULL confidenceScore' . PHP_EOL;

echo 'Remaining queries: ' . DB::table('queries')->count() . PHP_EOL;
