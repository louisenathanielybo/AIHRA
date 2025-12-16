<?php

namespace App\Services;

use Google\Cloud\Dialogflow\V2\Client\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryInput;
use Google\Cloud\Dialogflow\V2\DetectIntentRequest;
use Google\Cloud\Dialogflow\V2\Client\IntentsClient;
use Google\Cloud\Dialogflow\V2\Intent;
use Google\Cloud\Dialogflow\V2\Intent\TrainingPhrase;
use Google\Cloud\Dialogflow\V2\Intent\TrainingPhrase\Part;
use Google\Cloud\Dialogflow\V2\Intent\Message;
use Google\Cloud\Dialogflow\V2\Intent\Message\Text;
use Google\ApiCore\ApiException;
use Google\Cloud\Dialogflow\V2\ListIntentsRequest;

class DialogflowService
{
    protected $sessionsClient;
    protected $intentsClient;
    protected $projectId;

    public function __construct()
    {
        // Get from environment variables
        $this->projectId = env('DIALOGFLOW_PROJECT_ID', 'aihra-472311');
        
        // Check if credentials file exists
        $credentialsPath = env('DIALOGFLOW_CREDENTIALS_PATH', 'aihra-key.json');
        $fullCredentialsPath = base_path($credentialsPath);
        
        if (!file_exists($fullCredentialsPath)) {
            \Log::error('Dialogflow credentials file not found: ' . $fullCredentialsPath);
            throw new \Exception('Dialogflow credentials file not found at: ' . $credentialsPath);
        }
        
        // Read the JSON credentials file content
        $credentialsContent = json_decode(file_get_contents($fullCredentialsPath), true);
        
        \Log::info('DialogflowService initialized', [
            'project_id' => $this->projectId,
            'credentials_path' => $fullCredentialsPath,
            'file_exists' => file_exists($fullCredentialsPath),
            'client_email' => $credentialsContent['client_email'] ?? 'unknown'
        ]);
        
        try {
            // Pass credentials as array directly to the clients
            $this->sessionsClient = new SessionsClient([
                'credentials' => $credentialsContent
            ]);
            $this->intentsClient = new IntentsClient([
                'credentials' => $credentialsContent
            ]);
            \Log::info('Dialogflow clients initialized successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to initialize Dialogflow clients: ' . $e->getMessage());
            throw $e;
        }
    }

    public function detectIntent($queryText, $sessionId)
    {
        $session = $this->sessionsClient->sessionName($this->projectId, $sessionId);

        $textInput = new TextInput();
        $textInput->setText($queryText);
        $textInput->setLanguageCode('en');

        $queryInput = new QueryInput();
        $queryInput->setText($textInput);

        $request = new DetectIntentRequest();
        $request->setSession($session);
        $request->setQueryInput($queryInput);

        $response = $this->sessionsClient->detectIntent($request);

        return $response->getQueryResult();
    }

    /**
     * List all intents from Dialogflow
     */
    public function listIntents()
{
    try {
        \Log::info('DialogflowService: Starting listIntents()', [
            'projectId' => $this->projectId
        ]);
        
        // Check if intentsClient is initialized
        if (!$this->intentsClient) {
            \Log::error('DialogflowService: intentsClient not initialized');
            return $this->getMockIntents(); // Return mock data
        }
        
        $parent = $this->intentsClient->projectAgentName($this->projectId);
        
        \Log::info('DialogflowService: Fetching intents from parent: ' . $parent);
        
        // Create ListIntentsRequest object for v2.2.1
        $request = new ListIntentsRequest();
        $request->setParent($parent);
        
        // List intents
        $intents = $this->intentsClient->listIntents($request);
        
        $intentList = [];
        foreach ($intents as $intent) {
            $intentList[] = $this->formatIntent($intent);
        }
        
        \Log::info('DialogflowService: Successfully fetched ' . count($intentList) . ' intents');
        
        return $intentList;
        
    } catch (\Google\ApiCore\ApiException $e) {
        // CORRECT EXCEPTION HANDLING FOR v2.2.1
        \Log::error('Dialogflow API Exception in listIntents: ' . $e->getMessage(), [
            'status' => $e->getStatus(), // This should work
            'code' => $e->getCode(),
            // Use getMetadata() instead of getDetails()
            'metadata' => method_exists($e, 'getMetadata') ? $e->getMetadata() : null,
        ]);
        
        // For additional debugging, you can get the failure info
        if (method_exists($e, 'getFailureInfo')) {
            $failureInfo = $e->getFailureInfo();
            \Log::debug('Failure info:', (array) $failureInfo);
        }
        
        // Return mock data for development
        return $this->getMockIntents();
        
    } catch (\Exception $e) {
        \Log::error('General Exception in listIntents: ' . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        
        // Return mock data for development
        return $this->getMockIntents();
    }
}

    /**
     * Return mock intents for development/testing
     */
    private function getMockIntents()
    {
        return [
            [
                'id' => 'projects/aihra-472311/agent/intents/1',
                'intent_name' => 'leave.policy.inquiry',
                'display_name' => 'Leave Policy Inquiry',
                'training_phrases' => ['How do I apply for leave?', 'What is the leave policy?', 'How many leave days do I have?'],
                'training_phrases_count' => 3,
                'responses' => ['You can apply for leave through the HR portal.', 'The leave policy allows for 20 days annual leave.'],
                'responses_count' => 2,
                'priority' => 500000,
                'is_fallback' => false,
                'status' => 'active',
                'created_at' => now()->subDays(5)->toDateTimeString(),
                'updated_at' => now()->subDays(1)->toDateTimeString(),
            ],
            [
                'id' => 'projects/aihra-472311/agent/intents/2',
                'intent_name' => 'benefits.information',
                'display_name' => 'Benefits Information',
                'training_phrases' => ['What benefits do I get?', 'Tell me about health insurance', 'What are the employee benefits?'],
                'training_phrases_count' => 3,
                'responses' => ['Employees receive health insurance, dental coverage, and retirement benefits.', 'Health insurance coverage starts after 90 days of employment.'],
                'responses_count' => 2,
                'priority' => 500000,
                'is_fallback' => false,
                'status' => 'active',
                'created_at' => now()->subDays(10)->toDateTimeString(),
                'updated_at' => now()->subDays(2)->toDateTimeString(),
            ],
            [
                'id' => 'projects/aihra-472311/agent/intents/3',
                'intent_name' => 'salary.inquiry',
                'display_name' => 'Salary Inquiry',
                'training_phrases' => ['When is payday?', 'How do I view my payslip?', 'What is the salary schedule?'],
                'training_phrases_count' => 3,
                'responses' => ['Payday is every 15th and 30th of the month.', 'You can view your payslip in the employee portal.'],
                'responses_count' => 2,
                'priority' => 500000,
                'is_fallback' => false,
                'status' => 'active',
                'created_at' => now()->subDays(7)->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ],
        ];
    }

    /**
     * Get a specific intent by name
     */
    public function getIntent($intentName)
    {
        try {
            $intent = $this->intentsClient->getIntent($intentName);
            return $this->formatIntent($intent);
        } catch (ApiException $e) {
            \Log::error('Failed to get intent: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a new intent in Dialogflow
     */
    public function createIntent(array $data)
    {
        try {
            $parent = $this->intentsClient->projectAgentName($this->projectId);
            
            // Create intent object
            $intent = new Intent();
            $intent->setDisplayName($data['display_name']);
            
            // Set training phrases if provided
            if (!empty($data['training_phrases'])) {
                $trainingPhrases = [];
                foreach ($data['training_phrases'] as $phrase) {
                    $trainingPhrase = new TrainingPhrase();
                    $part = new Part();
                    $part->setText(trim($phrase));
                    $trainingPhrase->setParts([$part]);
                    $trainingPhrases[] = $trainingPhrase;
                }
                $intent->setTrainingPhrases($trainingPhrases);
            }
            
            // Set responses if provided
            if (!empty($data['responses'])) {
                $messages = [];
                foreach ($data['responses'] as $response) {
                    $message = new Message();
                    $text = new Text();
                    $text->setText([trim($response)]);
                    $message->setText($text);
                    $messages[] = $message;
                }
                $intent->setMessages($messages);
            }
            
            // Set priority if provided
            if (!empty($data['priority'])) {
                $intent->setPriority((int)$data['priority']);
            }
            
            $createdIntent = $this->intentsClient->createIntent($parent, $intent);
            
            return [
                'success' => true,
                'intent' => $this->formatIntent($createdIntent)
            ];
            
        } catch (ApiException $e) {
            \Log::error('Failed to create intent: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Update an existing intent
     */
    public function updateIntent($intentName, array $data)
    {
        try {
            // Get existing intent
            $existingIntent = $this->intentsClient->getIntent($intentName);
            
            // Update fields
            if (!empty($data['display_name'])) {
                $existingIntent->setDisplayName($data['display_name']);
            }
            
            // Update training phrases if provided
            if (!empty($data['training_phrases'])) {
                $trainingPhrases = [];
                foreach ($data['training_phrases'] as $phrase) {
                    $trainingPhrase = new TrainingPhrase();
                    $part = new Part();
                    $part->setText(trim($phrase));
                    $trainingPhrase->setParts([$part]);
                    $trainingPhrases[] = $trainingPhrase;
                }
                $existingIntent->setTrainingPhrases($trainingPhrases);
            }
            
            // Update responses if provided
            if (!empty($data['responses'])) {
                $messages = [];
                foreach ($data['responses'] as $response) {
                    $message = new Message();
                    $text = new Text();
                    $text->setText([trim($response)]);
                    $message->setText($text);
                    $messages[] = $message;
                }
                $existingIntent->setMessages($messages);
            }
            
            // Update priority if provided
            if (!empty($data['priority'])) {
                $existingIntent->setPriority((int)$data['priority']);
            }
            
            $updatedIntent = $this->intentsClient->updateIntent($existingIntent, ['updateMask' => '*']);
            
            return [
                'success' => true,
                'intent' => $this->formatIntent($updatedIntent)
            ];
            
        } catch (ApiException $e) {
            \Log::error('Failed to update intent: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Delete an intent
     */
    public function deleteIntent($intentName)
    {
        try {
            $this->intentsClient->deleteIntent($intentName);
            return ['success' => true];
        } catch (ApiException $e) {
            \Log::error('Failed to delete intent: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Format intent for response
     */
    private function formatIntent($intent)
    {
        $trainingPhrases = [];
        foreach ($intent->getTrainingPhrases() as $phrase) {
            $parts = $phrase->getParts();
            if (!empty($parts)) {
                $trainingPhrases[] = $parts[0]->getText();
            }
        }
        
        $responses = [];
        foreach ($intent->getMessages() as $message) {
            $text = $message->getText();
            if ($text) {
                $responses = array_merge($responses, $text->getText());
            }
        }
        
        return [
            'id' => $intent->getName(),
            'intent_name' => $intent->getName(),
            'display_name' => $intent->getDisplayName(),
            'training_phrases' => $trainingPhrases,
            'training_phrases_count' => count($trainingPhrases),
            'responses' => $responses,
            'responses_count' => count($responses),
            'priority' => $intent->getPriority() ?: 500000,
            'is_fallback' => $intent->getIsFallback(),
            'status' => 'active' // Dialogflow doesn't have inactive status, all are active
        ];
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
