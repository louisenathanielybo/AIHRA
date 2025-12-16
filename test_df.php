<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Dialogflow service...\n";

try {
    $df = new \App\Services\DialogflowService();
    echo "Service initialized successfully.\n";
    
    $result = $df->detectIntent('hello', 'test-session-123');
    echo "Intent: " . ($result->getIntent() ? $result->getIntent()->getDisplayName() : 'none') . "\n";
    echo "Text: " . $result->getFulfillmentText() . "\n";
    echo "Confidence: " . $result->getIntentDetectionConfidence() . "\n";
    $df->close();
    echo "\nDialogflow is working!\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
