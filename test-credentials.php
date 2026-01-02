<?php

require __DIR__ . '/vendor/autoload.php';

use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryInput;

echo "========================================\n";
echo "DIALOGFLOW CONNECTION TEST\n";
echo "========================================\n\n";

// Load credentials
$credentialsFile = __DIR__ . '/aihra-key.json';
echo "1. Loading credentials from: $credentialsFile\n";

if (!file_exists($credentialsFile)) {
    die("❌ File not found!\n");
}

$credentials = json_decode(file_get_contents($credentialsFile), true);

if (!$credentials) {
    die("❌ Failed to parse JSON!\n");
}

echo "✅ Credentials loaded\n";
echo "   Project ID: " . ($credentials['project_id'] ?? 'NOT FOUND') . "\n";
echo "   Client Email: " . ($credentials['client_email'] ?? 'NOT FOUND') . "\n\n";

// Set environment variable
putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $credentialsFile);

echo "2. Testing SessionsClient creation...\n";

try {
    // Method 1: Try with key file path
    echo "   Trying method 1: keyFilePath...\n";
    $client1 = new SessionsClient([
        'keyFilePath' => $credentialsFile,
        'projectId' => $credentials['project_id']
    ]);
    echo "   ✅ Method 1: Success!\n";
    $client1->close();
    
} catch (Exception $e) {
    echo "   ❌ Method 1 failed: " . $e->getMessage() . "\n";
    
    // Method 2: Try with credentials array
    echo "   Trying method 2: credentials array...\n";
    try {
        $client2 = new SessionsClient([
            'credentials' => $credentials,
            'projectId' => $credentials['project_id']
        ]);
        echo "   ✅ Method 2: Success!\n";
        
        // Test the connection
        echo "\n3. Testing Dialogflow API call...\n";
        
        $sessionId = 'test-session-' . time();
        $session = $client2->sessionName($credentials['project_id'], $sessionId);
        
        $textInput = new TextInput();
        $textInput->setText("What are the flexible time arrangements?");
        $textInput->setLanguageCode('en');
        
        $queryInput = new QueryInput();
        $queryInput->setText($textInput);
        
        echo "   Sending query: 'What are the flexible time arrangements?'\n";
        
        $response = $client2->detectIntent($session, $queryInput);
        $queryResult = $response->getQueryResult();
        
        echo "\n✅ SUCCESS! Dialogflow is working!\n\n";
        echo "Response:\n";
        echo "----------------------------------------\n";
        echo $queryResult->getFulfillmentText() . "\n";
        echo "----------------------------------------\n\n";
        echo "Confidence: " . $queryResult->getIntentDetectionConfidence() . "\n";
        echo "Intent: " . ($queryResult->getIntent() ? $queryResult->getIntent()->getDisplayName() : 'None') . "\n";
        
        $client2->close();
        
    } catch (Exception $e2) {
        echo "   ❌ Method 2 also failed: " . $e2->getMessage() . "\n";
        echo "   Error details: " . get_class($e2) . "\n";
        
        if ($e2 instanceof \Google\ApiCore\ApiException) {
            echo "   Status: " . $e2->getStatus() . "\n";
            echo "   Details: " . json_encode($e2->getDetails()) . "\n";
        }
    }
}

echo "\n========================================\n";
echo "TEST COMPLETE\n";
echo "========================================\n";