<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Checking Dialogflow Intents ===\n\n";

try {
    $df = new \App\Services\DialogflowService();
    echo "✓ DialogflowService initialized\n\n";
    
    echo "Fetching intents from Dialogflow...\n";
    $intents = $df->listIntents();
    
    if (empty($intents)) {
        echo "No intents found or using mock data.\n";
    } else {
        echo "Found " . count($intents) . " intents:\n\n";
        foreach ($intents as $i => $intent) {
            $name = $intent['displayName'] ?? $intent['name'] ?? 'Unknown';
            echo ($i + 1) . ". $name\n";
            
            // Show training phrases if available
            if (!empty($intent['trainingPhrases'])) {
                echo "   Training phrases: " . count($intent['trainingPhrases']) . "\n";
            }
        }
    }
    
    $df->close();
} catch (\Google\ApiCore\ApiException $e) {
    echo "✗ Dialogflow API Error!\n";
    echo "Status: " . $e->getStatus() . "\n";
    echo "Message: " . $e->getMessage() . "\n";
} catch (\Throwable $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== Done ===\n";
