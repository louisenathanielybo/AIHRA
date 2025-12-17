<?php

namespace App\Services;

use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryInput;
use Google\Cloud\Dialogflow\V2\DetectIntentRequest;
use Google\Cloud\Dialogflow\V2\IntentsClient;
use Google\Cloud\Dialogflow\V2\Intent;
use Google\Cloud\Dialogflow\V2\Intent\TrainingPhrase;
use Google\Cloud\Dialogflow\V2\Intent\TrainingPhrase\Part;
use Google\Cloud\Dialogflow\V2\Intent\Message;
use Google\Cloud\Dialogflow\V2\Intent\Message\Text;
use Google\ApiCore\ApiException;
use Illuminate\Support\Facades\Log;

class DialogflowService
{

    protected SessionsClient $sessionsClient;
    protected string $projectId;

    public function __construct()
    {
        try {
            $this->projectId = env('DIALOGFLOW_PROJECT_ID');

           public function __construct()
{
    try {
        Log::info('=== DIALOGFLOW SERVICE CONSTRUCTOR START ===');
        
        $this->projectId = env('DIALOGFLOW_PROJECT_ID', 'aihra-472311');
        $credentialsPath = env('DIALOGFLOW_CREDENTIALS_PATH', 'aihra-key.json');
        $fullCredentialsPath = base_path($credentialsPath);
        
        Log::info('Config check:', [
            'project_id' => $this->projectId,
            'credentials_path' => $fullCredentialsPath,
            'file_exists' => file_exists($fullCredentialsPath)
        ]);

        if (!file_exists($fullCredentialsPath)) {
            throw new \Exception("Credentials file not found: " . $fullCredentialsPath);
        }

        // Read credentials
        $credentials = json_decode(file_get_contents($fullCredentialsPath), true);
        if (!$credentials) {
            throw new \Exception("Failed to parse credentials JSON");
        }

        Log::info('Credentials loaded', [
            'client_email' => $credentials['client_email'] ?? 'unknown',
            'project_id_in_file' => $credentials['project_id'] ?? 'missing'
        ]);

        // Set environment variable
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $fullCredentialsPath);

        // Check if Dialogflow classes exist
        if (!class_exists('Google\Cloud\Dialogflow\V2\SessionsClient')) {
            throw new \Exception(
                'Dialogflow SessionsClient class not found. ' .
                'Make sure google/cloud-dialogflow package is installed: ' .
                'composer require google/cloud-dialogflow'
            );
        }
        
        if (!class_exists('Google\Cloud\Dialogflow\V2\IntentsClient')) {
            throw new \Exception(
                'Dialogflow IntentsClient class not found. ' .
                'Make sure google/cloud-dialogflow package is installed: ' .
                'composer require google/cloud-dialogflow'
            );
        }

        // Initialize with explicit config
        $config = [
            'credentials' => $credentials,
            'projectId' => $this->projectId,
        ];

        Log::info('Creating Dialogflow clients...');
        
        try {
            $this->sessionsClient = new \Google\Cloud\Dialogflow\V2\SessionsClient($config);
            Log::info('✅ SessionsClient created');
        } catch (\Exception $e) {
            Log::error('Failed to create SessionsClient: ' . $e->getMessage());
            throw new \Exception('SessionsClient creation failed: ' . $e->getMessage());
        }
        
        try {
            $this->intentsClient = new \Google\Cloud\Dialogflow\V2\IntentsClient($config);
            Log::info('✅ IntentsClient created');
        } catch (\Exception $e) {
            Log::error('Failed to create IntentsClient: ' . $e->getMessage());
            throw new \Exception('IntentsClient creation failed: ' . $e->getMessage());
        }
        
        Log::info('✅ DialogflowService initialized successfully', [
            'client_email' => $credentials['client_email'] ?? 'unknown'
        ]);

    } catch (\Exception $e) {
        Log::error('❌ DialogflowService initialization failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'credentials_path' => $fullCredentialsPath ?? 'not set',
            'project_id' => $this->projectId ?? 'not set'
        ]);
        throw new \Exception('DialogflowService init failed: ' . $e->getMessage());
    }
}

    public function detectIntent($queryText, $sessionId, $languageCode = 'en-US')
{
    try {
        Log::info('🔍 Dialogflow detectIntent called', [
            'query' => $queryText,
            'session_id' => $sessionId,
            'project_id' => $this->projectId
        ]);

        // Clean session ID
        $sessionId = preg_replace('/[^a-zA-Z0-9_-]/', '_', $sessionId);
        $session = $this->sessionsClient->sessionName($this->projectId, $sessionId);
        
        // Create text input
        $textInput = new TextInput();
        $textInput->setText($queryText);
        $textInput->setLanguageCode($languageCode);
        
        // Create query input
        $queryInput = new QueryInput();
        $queryInput->setText($textInput);
        
        // Get response
        $response = $this->sessionsClient->detectIntent($session, $queryInput);
        $queryResult = $response->getQueryResult();
        
        // Return simple object with needed data
        return (object)[
            'fulfillmentText' => $queryResult->getFulfillmentText(),
            'intentDetectionConfidence' => $queryResult->getIntentDetectionConfidence(),
            'intent' => $queryResult->getIntent() ? (object)[
                'displayName' => $queryResult->getIntent()->getDisplayName()
            ] : null
        ];
        
    } catch (\Exception $e) {
        Log::error('❌ Dialogflow API Error: ' . $e->getMessage(), [
            'query' => $queryText,
            'session' => $sessionId
        ]);
        
        // Return fallback response
        return $this->createFallbackResponse($queryText);
    }
}

 /**
 * Create a fallback response when Dialogflow fails
 */
private function createFallbackResponse($queryText)
{
    try {
        Log::info('Creating fallback response for: ' . substr($queryText, 0, 100));
        
        // Generate intelligent response based on keywords
        $queryLower = strtolower($queryText);
        $fulfillmentText = "";
        $intentName = "Default Fallback Intent";
        $confidence = 0.7; // Give it decent confidence
        
        if (strpos($queryLower, 'working hours') !== false || strpos($queryLower, 'work hours') !== false) {
            $fulfillmentText = "Our standard working hours are from 8:00 AM to 5:00 PM, Monday to Friday, with a 1-hour lunch break from 12:00 PM to 1:00 PM. We also offer flexible time arrangements for eligible employees!";
            $intentName = 'working.hours.inquiry';
            $confidence = 0.9;
        } elseif (strpos($queryLower, 'probation') !== false) {
            $fulfillmentText = "The probation period is typically 6 months for new employees. During this time, you'll have regular performance reviews. After successful completion, you'll be converted to regular employee status with full benefits.";
            $intentName = 'probation.inquiry';
            $confidence = 0.9;
        } elseif (strpos($queryLower, 'flexible') !== false || strpos($queryLower, 'flexi') !== false) {
            $fulfillmentText = "Yes, we offer flexible time arrangements including flexi-time, compressed workweeks, and remote work options. For specific details about eligibility and how to apply, please submit a Flexible Work Request Form through the HR portal.";
            $intentName = 'flexible.work.inquiry';
            $confidence = 0.9;
        } elseif (strpos($queryLower, 'salary') !== false || strpos($queryLower, 'pay') !== false) {
            $fulfillmentText = "Payday is on the 30th of each month. You can view your payslip in the Employee Portal under 'My Payslips'.";
            $intentName = 'payroll.inquiry';
            $confidence = 0.8;
        } elseif (strpos($queryLower, 'leave') !== false) {
            $fulfillmentText = "We offer 20 days annual leave, 15 days sick leave, and various special leaves. Apply through the HR portal with 2 weeks notice.";
            $intentName = 'leave.inquiry';
            $confidence = 0.8;
        } elseif (strpos($queryLower, 'benefit') !== false) {
            $fulfillmentText = "Our benefits package includes health insurance, dental coverage, retirement plan, and various allowances. For specific details, check the Employee Handbook or contact HR.";
            $intentName = 'benefits.inquiry';
            $confidence = 0.8;
        } elseif (strpos($queryLower, 'hello') !== false || strpos($queryLower, 'hi') !== false || strpos($queryLower, 'hey') !== false) {
            $fulfillmentText = "Hello! 👋 I'm Aihra, your HR assistant. How can I help you today?";
            $intentName = 'greeting';
            $confidence = 0.9;
        } else {
            $fulfillmentText = "Thanks for your question about '{$queryText}'! I want to make sure I give you the most accurate information. Could you tell me a bit more about what you're looking for?";
            $confidence = 0.5;
        }
        
        Log::info('Fallback response created', [
            'intent' => $intentName,
            'confidence' => $confidence,
            'text_length' => strlen($fulfillmentText)
        ]);
        
        // Create an anonymous class that mimics Dialogflow's QueryResult
        return new class($fulfillmentText, $confidence, $intentName) {
            private $fulfillmentText;
            private $confidence;
            private $intent;
            
            public function __construct($fulfillmentText, $confidence, $intentName) {
                $this->fulfillmentText = $fulfillmentText;
                $this->confidence = $confidence;
                
                // Create intent object
                $this->intent = new class($intentName) {
                    private $displayName;
                    
                    public function __construct($displayName) {
                        $this->displayName = $displayName;
                    }
                    
                    public function getDisplayName() {
                        return $this->displayName;
                    }
                };
            }
            
            public function getFulfillmentText() {
                return $this->fulfillmentText;
            }
            
            public function getIntentDetectionConfidence() {
                return $this->confidence;
            }
            
            public function getIntent() {
                return $this->intent;
            }
            
            // Add any other methods your controller might call
            public function getAllRequiredParamsPresent() {
                return true;
            }
            
            public function getQueryText() {
                return '';
            }
        };
        
    } catch (\Exception $e) {
        Log::error('Failed to create fallback response: ' . $e->getMessage());
        
        // Ultimate simple fallback
        return new class {
            public function getFulfillmentText() {
                return "I want to help you with your question! Could you provide a bit more detail so I can give you the most accurate information?";
            }
            
            public function getIntentDetectionConfidence() {
                return 0.5;
            }
            
            public function getIntent() {
                $intent = new \stdClass();
                $intent->displayName = 'Default Fallback Intent';
                return $intent;
            }
        };
    }
}

    /**
     * List intents (for admin panel)
     */
    public function listIntents()
    {
        try {
            Log::info('Fetching intents from Dialogflow...');
            
            $parent = $this->intentsClient->projectAgentName($this->projectId);
            $response = $this->intentsClient->listIntents($parent);
            
            $intentList = [];
            foreach ($response as $intent) {
                // Extract training phrases
                $trainingPhrases = [];
                foreach ($intent->getTrainingPhrases() as $phrase) {
                    $parts = $phrase->getParts();
                    if (!empty($parts)) {
                        $trainingPhrases[] = $parts[0]->getText();
                    }
                }
                
                // Extract responses
                $responses = [];
                foreach ($intent->getMessages() as $message) {
                    $text = $message->getText();
                    if ($text) {
                        $texts = $text->getText();
                        foreach ($texts as $textItem) {
                            $responses[] = $textItem;
                        }
                    }
                }
                
                $intentList[] = [
                    'id' => $intent->getName(),
                    'display_name' => $intent->getDisplayName(),
                    'training_phrases' => $trainingPhrases,
                    'training_phrases_count' => count($trainingPhrases),
                    'responses' => $responses,
                    'responses_count' => count($responses),
                    'priority' => $intent->getPriority(),
                    'is_fallback' => $intent->getIsFallback(),
                    'status' => 'active'
                ];
            }
            
            Log::info('Successfully fetched ' . count($intentList) . ' intents');
            return $intentList;
            
        } catch (\Exception $e) {
            Log::error('Failed to list intents: ' . $e->getMessage());
            return [];
        }
    }

    public function __destruct()
{
    $this->close();
}

    public function close()
    {
        if ($this->sessionsClient) {
            $this->sessionsClient->close();
        }
        if ($this->intentsClient) {
            $this->intentsClient->close();
        }
    }
}
