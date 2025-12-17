<?php

namespace App\Services;

use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryInput;
use Google\Cloud\Dialogflow\V2\IntentsClient;
use Illuminate\Support\Facades\Log;

class DialogflowService
{
    protected SessionsClient $sessionsClient;
    protected ?IntentsClient $intentsClient = null; // Make it nullable
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
             * IMPORTANT:
             * Do NOT pass credentials manually.
             * Google SDK automatically reads GOOGLE_APPLICATION_CREDENTIALS
             */
            $this->sessionsClient = new SessionsClient();
            
            // Initialize intents client only if needed for listIntents
            // Don't initialize it here, initialize it in listIntents() method if needed

            Log::info('✅ DialogflowService initialized successfully');

        } catch (\Throwable $e) {
            Log::error('❌ DialogflowService initialization failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new \Exception('DialogflowService failed to initialize');
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
            
            // SIMPLIFIED: Return as array instead of object
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
            
            // Return fallback response as array
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
            
            // Return as simple array
            return [
                'fulfillmentText' => $fulfillmentText,
                'intentDetectionConfidence' => $confidence,
                'intent' => [
                    'displayName' => $intentName
                ]
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to create fallback response: ' . $e->getMessage());
            
            // Ultimate simple fallback as array
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
     * List intents (for admin panel)
     */
    public function listIntents()
    {
        try {
            Log::info('Fetching intents from Dialogflow...');
            
            // Initialize intents client only when needed
            if (!$this->intentsClient) {
                $this->intentsClient = new IntentsClient();
            }
            
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
