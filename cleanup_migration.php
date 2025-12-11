<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::statement('ALTER TABLE feedback DROP COLUMN feedbackID_new');
    echo "Dropped feedback.feedbackID_new\n";
} catch (Exception $e) {
    echo "feedback.feedbackID_new doesn't exist or already dropped\n";
}

try {
    DB::statement('ALTER TABLE flaggedresponse DROP COLUMN flaggedID_new');
    echo "Dropped flaggedresponse.flaggedID_new\n";
} catch (Exception $e) {
    echo "flaggedresponse.flaggedID_new doesn't exist or already dropped\n";
}

echo "Cleanup complete!\n";
