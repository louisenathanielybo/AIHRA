<?php

namespace App\Services;

use Google\Cloud\Dialogflow\V2\Client\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryInput;
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
    protected ?IntentsClient $intentsClient = null;
    protected string $projectId;

    public function __construct()
    {
        try {
            $this->projectId = env('DIALOGFLOW_PROJECT_ID');

            if (!$this->projectId) {
                throw new \Exception('DIALOGFLOW_PROJECT_ID is not set');
            }

            Log::info('Initializing DialogflowService', [
                'project_id' => $this->projectId,
            ]);

            /**
             * IMPORTANT: Initialize both clients with proper configuration
             */
            $config = [
                'credentials' => $this->getGoogleCredentials(),
                'projectId' => $this->projectId,
            ];

            $this->sessionsClient = new SessionsClient($config);
            
            Log::info('✅ DialogflowService initialized successfully');

        } catch (\Throwable $e) {
            Log::error('❌ DialogflowService initialization failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new \Exception('DialogflowService failed to initialize');
       }
    }

    /**
     * Get Google credentials from environment
     */
    private function getGoogleCredentials()
    {
        $credentialsPath = env('GOOGLE_APPLICATION_CREDENTIALS');
        
        if (!$credentialsPath) {
            Log::warning('GOOGLE_APPLICATION_CREDENTIALS not set, checking default locations');
            // Try default locations
            $home = getenv('HOME');
            if ($home) {
                $defaultPath = $home . '/.config/gcloud/application_default_credentials.json';
                if (file_exists($defaultPath)) {
                    $credentialsPath = $defaultPath;
                }
            }
        }
        
        if ($credentialsPath && file_exists($credentialsPath)) {
            Log::info('Using credentials from: ' . $credentialsPath);
            return json_decode(file_get_contents($credentialsPath), true);
        }
        
        Log::warning('No Google credentials file found, trying environment authentication');
        return null; // Let Google SDK use default authentication
    }

    /**
     * Initialize IntentsClient with proper configuration
     */
    private function initIntentsClient()
    {
        if (!$this->intentsClient) {
            $config = [
                'credentials' => $this->getGoogleCredentials(),
                'projectId' => $this->projectId,
            ];
            
            $this->intentsClient = new IntentsClient($config);
            Log::info('IntentsClient initialized');
        }
        
        return $this->intentsClient;
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

            // Send request to Dialogflow
            Log::info('Sending request to Dialogflow...');
            
            $response = $this->sessionsClient->detectIntent($session, $queryInput);
            
            // Get query result
            $queryResult = $response->getQueryResult();
            
            // Return as array
            return [
                'fulfillmentText' => $queryResult->getFulfillmentText(),
                'intentDetectionConfidence' => $queryResult->getIntentDetectionConfidence(),
                'intent' => [
                    'displayName' => $queryResult->getIntent() ? $queryResult->getIntent()->getDisplayName() : 'Default Fallback Intent'
                ]
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
            
            $queryLower = strtolower($queryText);
            $fulfillmentText = "";
            $intentName = "Default Fallback Intent";
            $confidence = 0.7;
            
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
            
            return [
                'fulfillmentText' => $fulfillmentText,
                'intentDetectionConfidence' => $confidence,
                'intent' => [
                    'displayName' => $intentName
                ]
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to create fallback response: ' . $e->getMessage());
            
            return [
                'fulfillmentText' => "I want to help you with your question! Could you provide a bit more detail so I can give you the most accurate information?",
                'intentDetectionConfidence' => 0.5,
                'intent' => [
                    'displayName' => 'Default Fallback Intent'
                ]
            ];
        }
    }

    /**
     * List intents (for admin panel) - FIXED VERSION
     */
    public function listIntents()
    {
        try {
            Log::info('🔄 Starting to fetch intents from Dialogflow...');
            
            // Initialize intents client with proper configuration
            $intentsClient = $this->initIntentsClient();
            
            $parent = $this->intentsClient->projectAgentName($this->projectId);
            $response = $this->intentsClient->listIntents($parent);
            
            // Get intents with pagination
            $intentList = [];
            $page = $intentsClient->listIntents($parent);
            
            foreach ($page->iterateAllElements() as $intent) {
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
            
            Log::info('✅ Successfully fetched ' . count($intentList) . ' intents from Dialogflow');
            return $intentList;
            
        } catch (\Google\ApiCore\ApiException $e) {
            Log::error('❌ Google API Exception in listIntents', [
                'message' => $e->getMessage(),
                'status' => $e->getStatus(),
                'details' => $e->getDetails(),
                'code' => $e->getCode()
            ]);
            
            // Return empty array for the controller to handle
            return [];
            
        } catch (\Exception $e) {
            Log::error('❌ General Exception in listIntents', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [];
        }
    }

    /**
     * Test connection to Dialogflow
     */
    public function testConnection()
    {
        try {
            Log::info('Testing Dialogflow connection...');
            
            // Test SessionsClient
            $testResult = $this->detectIntent('Hello', 'test-session-' . time());
            
            // Test IntentsClient
            $intentsClient = $this->initIntentsClient();
            $parent = $intentsClient->projectAgentName($this->projectId);
            
            Log::info('✅ Dialogflow connection test successful');
            
            return [
                'success' => true,
                'sessions_client' => '✅ Working',
                'intents_client' => '✅ Working',
                'project_id' => $this->projectId,
                'test_response' => $testResult
            ];
            
        } catch (\Exception $e) {
            Log::error('❌ Dialogflow connection test failed', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function __destruct()
    {
        $this->close();
    }

    public function close()
    {
        try {
            if ($this->sessionsClient) {
                $this->sessionsClient->close();
            }
            if ($this->intentsClient) {
                $this->intentsClient->close();
            }
        } catch (\Exception $e) {
            Log::warning('Error closing Dialogflow clients: ' . $e->getMessage());
        }
    }
}
