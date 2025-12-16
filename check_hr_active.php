<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "USERS TABLE STRUCTURE:\n";
echo str_repeat("=", 60) . "\n";
$columns = DB::select('SHOW COLUMNS FROM users');
foreach ($columns as $col) {
    echo sprintf("%-20s %-30s\n", $col->Field, $col->Type);
}

echo "\n\nHR ACCOUNTS ANALYSIS:\n";
echo str_repeat("=", 60) . "\n\n";

// Try to find the correct column name
$sample = DB::table('users')->first();
echo "Sample user columns:\n";
foreach ($sample as $key => $value) {
    echo "  $key: $value\n";
}

