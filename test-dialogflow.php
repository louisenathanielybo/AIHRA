<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\DialogflowService;

try {
    echo "Testing Dialogflow connection...\n";
    
    $df = new DialogflowService();
    $result = $df->detectIntent('What are the regular working hours?', 'test-session-' . time());
    
    echo "Success!\n";
    echo "Fulfillment Text: " . $result->getFulfillmentText() . "\n";
    echo "Intent: " . ($result->getIntent() ? $result->getIntent()->getDisplayName() : 'None') . "\n";
    echo "Confidence: " . $result->getIntentDetectionConfidence() . "\n";
    
    $df->close();
    
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
