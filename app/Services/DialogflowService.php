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
    protected $sessionsClient;
    protected $intentsClient;
    protected $projectId;

    public function __construct()
    {
        try {
            $this->projectId = env('DIALOGFLOW_PROJECT_ID', 'aihra-472311');
            $credentialsPath = env('DIALOGFLOW_CREDENTIALS_PATH', 'aihra-key.json');
            $fullCredentialsPath = base_path($credentialsPath);
            
            Log::info('DialogflowService initializing', [
                'project_id' => $this->projectId,
                'credentials_path' => $fullCredentialsPath
            ]);

            if (!file_exists($fullCredentialsPath)) {
                throw new \Exception("Credentials file not found: " . $fullCredentialsPath);
            }

            // Read credentials
            $credentials = json_decode(file_get_contents($fullCredentialsPath), true);
            if (!$credentials) {
                throw new \Exception("Failed to parse credentials JSON");
            }

            // Set environment variable
            putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $fullCredentialsPath);

            // Initialize with explicit config
            $config = [
                'credentials' => $credentials,
                'projectId' => $this->projectId,
            ];

            Log::info('Creating Dialogflow clients...');
            $this->sessionsClient = new SessionsClient($config);
            $this->intentsClient = new IntentsClient($config);
            
            Log::info('✅ DialogflowService initialized successfully', [
                'client_email' => $credentials['client_email'] ?? 'unknown'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ DialogflowService initialization failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('DialogflowService init failed: ' . $e->getMessage());
        }
    }

    public function detectIntent($queryText, $sessionId)
    {
        try {
            Log::info('🔍 Dialogflow detectIntent called', [
                'query' => substr($queryText, 0, 100),
                'session_id' => $sessionId
            ]);

            if (empty($queryText)) {
                throw new \Exception('Empty query text');
            }

            // Clean session ID
            $sessionId = preg_replace('/[^a-zA-Z0-9_-]/', '_', $sessionId);
            if (empty($sessionId)) {
                $sessionId = 'session_' . time();
            }

            // Create session name
            $session = $this->sessionsClient->sessionName($this->projectId, $sessionId);
            
            Log::info('Session created', ['session' => $session]);

            // Create text input
            $textInput = new TextInput();
            $textInput->setText($queryText);
            $textInput->setLanguageCode('en');

            // Create query input
            $queryInput = new QueryInput();
            $queryInput->setText($textInput);

            // Send request to Dialogflow
            Log::info('Sending request to Dialogflow...');
            
            $response = $this->sessionsClient->detectIntent($session, $queryInput);
            
            // Get query result
            $queryResult = $response->getQueryResult();
            
            if (!$queryResult) {
                throw new \Exception('No query result received');
            }

            Log::info('✅ Dialogflow response received', [
                'intent' => $queryResult->getIntent() ? $queryResult->getIntent()->getDisplayName() : 'None',
                'confidence' => $queryResult->getIntentDetectionConfidence(),
                'has_text' => !empty($queryResult->getFulfillmentText())
            ]);

            return $queryResult;

        } catch (\Exception $e) {
            Log::error('❌ Dialogflow detectIntent failed', [
                'error' => $e->getMessage(),
                'class' => get_class($e),
                'trace' => $e->getTraceAsString(),
                'query' => $queryText
            ]);
            
            // Create a fallback response object
            return $this->createFallbackResponse($queryText);
        }
    }

    /**
     * Create a fallback response when Dialogflow fails
     */
    private function createFallbackResponse($queryText)
    {
        // Create a mock result object
        $mockResult = new \stdClass();
        
        // Create mock intent
        $mockIntent = new \stdClass();
        $mockIntent->displayName = 'Default Fallback Intent';
        
        // Set properties
        $mockResult->intent = $mockIntent;
        $mockResult->intentDetectionConfidence = 0.0;
        
        // Generate intelligent response
        $queryLower = strtolower($queryText);
        
        if (strpos($queryLower, 'probation') !== false) {
            $mockResult->fulfillmentText = "The probation period is typically 6 months for new employees. During this time, you'll have regular performance reviews. After successful completion, you'll be converted to regular employee status with full benefits.";
        } elseif (strpos($queryLower, 'hello') !== false || strpos($queryLower, 'hi') !== false) {
            $mockResult->fulfillmentText = "Hello! 👋 I'm Aihra, your HR assistant. How can I help you today?";
        } elseif (strpos($queryLower, 'flexible') !== false) {
            $mockResult->fulfillmentText = "Yes, we offer flexible time arrangements including flexi-time, compressed workweeks, and remote work options. For specific details about eligibility and how to apply, please submit a Flexible Work Request Form through the HR portal.";
        } else {
            $mockResult->fulfillmentText = "I want to help you with your question about '{$queryText}'! Could you provide a bit more detail so I can give you the most accurate information?";
        }
        
        return $mockResult;
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
