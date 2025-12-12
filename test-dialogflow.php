<?php

require __DIR__.'/vendor/autoload.php';

use Google\Cloud\Dialogflow\V2\IntentsClient;

echo "=============================================\n";
echo "  DIALOGFLOW CONNECTION TEST SCRIPT\n";
echo "=============================================\n\n";

// Test 1: Check credentials file
$credentialsPath = __DIR__.'/aihra-key.json';
echo "1. Checking credentials file...\n";
echo "   Looking for: $credentialsPath\n";

if (!file_exists($credentialsPath)) {
    echo "   ❌ ERROR: Credentials file not found!\n";
    echo "   Make sure the file exists at: $credentialsPath\n";
    
    // Check common locations
    echo "\n   Checking other possible locations:\n";
    $possiblePaths = [
        __DIR__.'/storage/app/google-credentials.json',
        __DIR__.'/config/google-credentials.json',
        __DIR__.'/public/aihra-key.json',
    ];
    
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            echo "   ✓ Found at: $path\n";
            $credentialsPath = $path;
            break;
        }
    }
    
    if (!file_exists($credentialsPath)) {
        echo "   No credentials file found in any location.\n";
        echo "   Please download your service account key from Google Cloud Console\n";
        echo "   and save it as 'aihra-key.json' in your project root.\n";
        exit(1);
    }
} else {
    echo "   ✓ Credentials file found\n";
}

if (!is_readable($credentialsPath)) {
    echo "   ❌ ERROR: Credentials file not readable\n";
    echo "   File permissions: " . substr(sprintf('%o', fileperms($credentialsPath)), -4) . "\n";
    exit(1);
}

echo "   ✓ Credentials file is readable\n";

$credentials = json_decode(file_get_contents($credentialsPath), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "   ❌ ERROR: Invalid JSON format: " . json_last_error_msg() . "\n";
    exit(1);
}

echo "   ✓ JSON format is valid\n";
echo "   Project ID: " . ($credentials['project_id'] ?? 'Not found') . "\n";
echo "   Client Email: " . ($credentials['client_email'] ?? 'Not found') . "\n\n";

// Test 2: Check required extensions
echo "2. Checking PHP extensions...\n";
$requiredExtensions = ['json', 'openssl', 'mbstring'];
$missingExtensions = [];

foreach ($requiredExtensions as $ext) {
    if (!extension_loaded($ext)) {
        $missingExtensions[] = $ext;
    }
}

if (!empty($missingExtensions)) {
    echo "   ❌ ERROR: Missing required PHP extensions:\n";
    foreach ($missingExtensions as $ext) {
        echo "   - $ext\n";
    }
    echo "   Please enable these extensions in your php.ini file\n";
    exit(1);
}

echo "   ✓ All required PHP extensions are loaded\n";

// Check for gRPC extension (recommended but not required)
if (extension_loaded('grpc')) {
    echo "   ✓ gRPC extension is loaded (recommended for better performance)\n";
} else {
    echo "   ⚠️  gRPC extension not loaded (will use REST transport)\n";
}

echo "\n";

// Test 3: Check Google Cloud PHP SDK
echo "3. Checking Google Cloud PHP SDK...\n";
if (!class_exists('Google\Cloud\Dialogflow\V2\IntentsClient')) {
    echo "   ❌ ERROR: Dialogflow SDK not found\n";
    echo "   Please install it with: composer require google/cloud-dialogflow\n";
    exit(1);
}

echo "   ✓ Dialogflow SDK is installed\n";

// Test installed packages
$composerFile = __DIR__ . '/vendor/composer/installed.json';
if (file_exists($composerFile)) {
    $installed = json_decode(file_get_contents($composerFile), true);
    $packages = $installed['packages'] ?? $installed;
    
    $googlePackages = [];
    foreach ($packages as $package) {
        if (strpos($package['name'], 'google/') === 0) {
            $googlePackages[] = $package['name'] . ' (' . $package['version'] . ')';
        }
    }
    
    if (!empty($googlePackages)) {
        echo "   Installed Google packages:\n";
        foreach ($googlePackages as $package) {
            echo "   - $package\n";
        }
    }
}

echo "\n";

// Test 4: Try to initialize client
echo "4. Initializing Dialogflow client...\n";

try {
    // Try with gRPC first, then fall back to REST
    $transport = extension_loaded('grpc') ? 'grpc' : 'rest';
    echo "   Using transport: $transport\n";
    
    $client = new IntentsClient([
        'credentials' => $credentialsPath,
        'transport' => $transport,
    ]);
    
    echo "   ✓ Dialogflow client initialized successfully\n\n";
    
    // Test 5: Test API connection
    echo "5. Testing API connection...\n";
    $projectId = $credentials['project_id'];
    echo "   Project ID: $projectId\n";
    
    try {
        $parent = $client->agentName($projectId);
        echo "   Agent path: $parent\n";
        
        // Try to list intents with a small page size
        $response = $client->listIntents($parent, [
            'pageSize' => 3,
            'languageCode' => 'en'
        ]);
        
        $intents = [];
        foreach ($response as $intent) {
            $intents[] = $intent;
        }
        
        $count = count($intents);
        echo "   ✓ Successfully connected to Dialogflow API\n";
        echo "   Found $count intents (limited to first 3)\n\n";
        
        if ($count > 0) {
            echo "   Sample intents:\n";
            foreach ($intents as $index => $intent) {
                $nameParts = explode('/', $intent->getName());
                $intentId = end($nameParts);
                echo "   " . ($index + 1) . ". {$intent->getDisplayName()}\n";
                echo "      ID: $intentId\n";
                echo "      Fallback: " . ($intent->getIsFallback() ? 'Yes' : 'No') . "\n";
                echo "      Webhook: " . $intent->getWebhookState() . "\n\n";
            }
        }
        
    } catch (\Google\ApiCore\ApiException $e) {
        echo "   ❌ API Error: " . $e->getMessage() . "\n";
        echo "   Status: " . $e->getStatus() . "\n";
        
        // Provide helpful suggestions based on error
        switch ($e->getStatus()) {
            case 'PERMISSION_DENIED':
                echo "\n   🔧 Solution: Check if your service account has these permissions:\n";
                echo "   - dialogflow.intents.list\n";
                echo "   - dialogflow.intents.get\n";
                echo "   Go to Google Cloud Console > IAM & Admin > IAM\n";
                echo "   Find your service account and add 'Dialogflow API Client' role\n";
                break;
                
            case 'NOT_FOUND':
                echo "\n   🔧 Solution: Check your project ID\n";
                echo "   - Your credentials show project: $projectId\n";
                echo "   - Go to https://console.dialogflow.com and check your project\n";
                echo "   - Make sure Dialogflow API is enabled at:\n";
                echo "     https://console.cloud.google.com/apis/library/dialogflow.googleapis.com\n";
                break;
                
            case 'UNAUTHENTICATED':
                echo "\n   🔧 Solution: Check your credentials\n";
                echo "   - Make sure the service account key is valid\n";
                echo "   - The key might have expired or been revoked\n";
                echo "   - Generate a new key from Google Cloud Console\n";
                break;
                
            default:
                echo "\n   🔧 Check Google Cloud status: https://status.cloud.google.com/\n";
        }
        
        echo "\n   Debug details:\n";
        if ($e->getMetadata()) {
            echo "   Metadata: " . json_encode($e->getMetadata()) . "\n";
        }
        
    } catch (\Exception $e) {
        echo "   ❌ General Error: " . $e->getMessage() . "\n";
        echo "   Type: " . get_class($e) . "\n";
    }
    
    $client->close();
    echo "   ✓ Client connection closed\n";
    
} catch (\Google\ApiCore\ValidationException $e) {
    echo "   ❌ Validation Error: " . $e->getMessage() . "\n";
    echo "   This usually means there's an issue with the credentials format\n";
    
} catch (\Exception $e) {
    echo "   ❌ ERROR: Failed to initialize client: " . $e->getMessage() . "\n";
    
    // Check for specific common issues
    if (strpos($e->getMessage(), 'grpc') !== false && !extension_loaded('grpc')) {
        echo "\n   🔧 gRPC extension is required but not loaded.\n";
        echo "   Options:\n";
        echo "   1. Install gRPC extension:\n";
        echo "      - Windows: Download DLL from https://pecl.php.net/package/grpc\n";
        echo "      - Add extension=grpc.so to php.ini\n";
        echo "   2. Or use REST transport by modifying your service:\n";
        echo "      In DialogflowIntentService.php, add: 'transport' => 'rest'\n";
    }
    
    if (strpos($e->getMessage(), 'cURL error') !== false) {
        echo "\n   🔧 cURL error detected. Check your internet connection and proxy settings.\n";
    }
    
    echo "\n   Full error details:\n";
    echo "   " . $e->getTraceAsString() . "\n";
}

echo "\n=============================================\n";
echo "  TEST COMPLETE\n";
echo "=============================================\n";

// Additional diagnostic information
echo "\nAdditional Information:\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "PHP OS: " . PHP_OS . "\n";
echo "Current Directory: " . getcwd() . "\n";
echo "Memory Limit: " . ini_get('memory_limit') . "\n";
echo "Max Execution Time: " . ini_get('max_execution_time') . "s\n";

// Check if running in Laragon
if (strpos(__DIR__, 'laragon') !== false) {
    echo "\n⚠️  Running in Laragon environment\n";
    echo "Make sure Laragon has internet access through any VPN/proxy\n";
}