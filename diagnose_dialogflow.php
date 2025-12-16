<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Dialogflow Diagnostic Test ===\n\n";

// 1. Check credentials file
$keyPath = base_path('aihra-key.json');
echo "1. Checking credentials file...\n";
if (file_exists($keyPath)) {
    echo "   ✓ File exists at: $keyPath\n";
    $creds = json_decode(file_get_contents($keyPath), true);
    if ($creds) {
        echo "   ✓ Valid JSON\n";
        echo "   - Project ID: " . ($creds['project_id'] ?? 'MISSING') . "\n";
        echo "   - Client Email: " . ($creds['client_email'] ?? 'MISSING') . "\n";
        echo "   - Private Key ID: " . ($creds['private_key_id'] ?? 'MISSING') . "\n";
        echo "   - Has Private Key: " . (isset($creds['private_key']) ? 'YES' : 'NO') . "\n";
    } else {
        echo "   ✗ Invalid JSON!\n";
    }
} else {
    echo "   ✗ File NOT found!\n";
}

// 2. Check environment variables
echo "\n2. Checking environment variables...\n";
echo "   - DIALOGFLOW_PROJECT_ID: " . (env('DIALOGFLOW_PROJECT_ID') ?: 'NOT SET (using default)') . "\n";
echo "   - DIALOGFLOW_CREDENTIALS_PATH: " . (env('DIALOGFLOW_CREDENTIALS_PATH') ?: 'NOT SET (using default)') . "\n";

// 3. Try to create the service
echo "\n3. Attempting to create DialogflowService...\n";
try {
    $df = new \App\Services\DialogflowService();
    echo "   ✓ Service created successfully\n";
} catch (\Throwable $e) {
    echo "   ✗ Failed to create service: " . $e->getMessage() . "\n";
    exit(1);
}

// 4. Try to call detectIntent
echo "\n4. Testing detectIntent API call...\n";
try {
    $result = $df->detectIntent('hello', 'test-session-' . time());
    echo "   ✓ API call successful!\n";
    echo "   - Intent: " . ($result->getIntent() ? $result->getIntent()->getDisplayName() : 'none') . "\n";
    echo "   - Response: " . $result->getFulfillmentText() . "\n";
    echo "   - Confidence: " . $result->getIntentDetectionConfidence() . "\n";
    $df->close();
} catch (\Google\ApiCore\ApiException $e) {
    echo "   ✗ API Exception!\n";
    echo "   - Status: " . $e->getStatus() . "\n";
    echo "   - Message: " . $e->getMessage() . "\n";
    
    // Check specific error codes
    $errorMsg = $e->getMessage();
    if (strpos($errorMsg, 'UNAUTHENTICATED') !== false) {
        echo "\n   ⚠️ DIAGNOSIS: Authentication failed.\n";
        echo "   Possible causes:\n";
        echo "   1. The service account key was revoked/deleted in Google Cloud\n";
        echo "   2. The Dialogflow API is not enabled for project aihra-472311\n";
        echo "   3. The service account doesn't have Dialogflow API permissions\n";
        echo "\n   To fix:\n";
        echo "   - Go to: https://console.cloud.google.com/apis/library/dialogflow.googleapis.com?project=aihra-472311\n";
        echo "   - Make sure Dialogflow API is ENABLED\n";
        echo "   - Go to: https://console.cloud.google.com/iam-admin/serviceaccounts?project=aihra-472311\n";
        echo "   - Check if 'aihra-dialogflow@aihra-472311.iam.gserviceaccount.com' exists and has keys\n";
        echo "   - Make sure it has 'Dialogflow API Client' or 'Dialogflow API Admin' role\n";
    } elseif (strpos($errorMsg, 'PERMISSION_DENIED') !== false) {
        echo "\n   ⚠️ DIAGNOSIS: Permission denied.\n";
        echo "   The service account exists but lacks Dialogflow permissions.\n";
        echo "   Add the 'Dialogflow API Client' role to the service account.\n";
    } elseif (strpos($errorMsg, 'NOT_FOUND') !== false) {
        echo "\n   ⚠️ DIAGNOSIS: Dialogflow agent not found.\n";
        echo "   Make sure you have created a Dialogflow agent for project aihra-472311.\n";
    }
} catch (\Throwable $e) {
    echo "   ✗ General error: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== End of Diagnostic ===\n";
