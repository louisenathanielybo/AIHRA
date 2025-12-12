<?php

require __DIR__ . '/vendor/autoload.php';

use App\Services\DialogflowService;

try {
    echo "Testing Dialogflow with SSL fix...\n\n";
    
    $dialogflow = new DialogflowService(__DIR__ . '/aihra-key.json');
    $result = $dialogflow->detectIntent("What are the regular working hours?", "test-session-123");
    
    echo "✅ Success!\n";
    echo "Response: " . $result->getFulfillmentText() . "\n";
    echo "Confidence: " . $result->getIntentDetectionConfidence() . "\n";
    
    $dialogflow->close();
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
