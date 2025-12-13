<?php

namespace App\Services;

<<<<<<< HEAD
use Google\Cloud\Dialogflow\V2\IntentsClient;
use Google\Cloud\Dialogflow\V2\Intent;
use Google\Cloud\Dialogflow\V2\Intent\TrainingPhrase;
use Google\Cloud\Dialogflow\V2\Intent\TrainingPhrase\Part;
use Google\Cloud\Dialogflow\V2\Intent\Message;
use Google\Cloud\Dialogflow\V2\Intent\Message\Text;
use Google\Cloud\Dialogflow\V2\IntentView;
use Google\ApiCore\ApiException;
use Google\ApiCore\ValidationException;
use Illuminate\Support\Facades\Log;

class DialogflowIntentService
{
    protected $intentsClient;
    protected $projectId;
    protected $languageCode;
    protected $credentialsPath;
=======
use Google\Cloud\Dialogflow\V2\Client\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryInput;
use Google\Cloud\Dialogflow\V2\DetectIntentRequest;

class DialogflowService
{
    protected $sessionsClient;
    protected $projectId;
>>>>>>> 21f0fed8913e61a3dc40934bf89c506deb9e72b9

    public function __construct()
    {
        $this->projectId = 'aihra-472311';
<<<<<<< HEAD
        $this->languageCode = 'en';
        $this->credentialsPath = base_path('aihra-key.json');
        
        $this->validateCredentials();
        $this->initializeClient();
    }

    /**
     * Validate credentials file
     */
    private function validateCredentials()
    {
        // Check if file exists
        if (!file_exists($this->credentialsPath)) {
            throw new \Exception('Dialogflow credentials file not found at: ' . $this->credentialsPath);
        }
        
        // Check if file is readable
        if (!is_readable($this->credentialsPath)) {
            throw new \Exception('Dialogflow credentials file is not readable. Check file permissions.');
        }
        
        // Validate JSON format
        $content = file_get_contents($this->credentialsPath);
        $credentials = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON format in credentials file: ' . json_last_error_msg());
        }
        
        // Check required fields
        $requiredFields = ['type', 'project_id', 'private_key', 'client_email'];
        foreach ($requiredFields as $field) {
            if (empty($credentials[$field])) {
                throw new \Exception("Missing required field in credentials: {$field}");
            }
        }
        
        // Verify project ID matches
        if ($credentials['project_id'] !== $this->projectId) {
            Log::warning('Credentials project_id (' . $credentials['project_id'] . ') does not match configured project_id (' . $this->projectId . ')');
            // Update project ID from credentials
            $this->projectId = $credentials['project_id'];
        }
        
        Log::info('Dialogflow credentials validated successfully');
        return true;
    }

    /**
     * Initialize Dialogflow client
     */
    private function initializeClient()
    {
        try {
            Log::info('Initializing Dialogflow IntentsClient...');
            
            $this->intentsClient = new IntentsClient([
                'credentials' => $this->credentialsPath,
                'transport' => 'grpc', // Use gRPC transport
            ]);
            
            Log::info('Dialogflow IntentsClient initialized successfully');
            
        } catch (ValidationException $e) {
            Log::error('Dialogflow client validation error: ' . $e->getMessage());
            throw new \Exception('Dialogflow client configuration error: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Failed to initialize Dialogflow client: ' . $e->getMessage());
            throw new \Exception('Failed to initialize Dialogflow client: ' . $e->getMessage());
        }
    }

    /**
     * Get all intents from Dialogflow
     */
    public function listIntents()
    {
        try {
            Log::info('Fetching intents from Dialogflow...', ['project_id' => $this->projectId]);
            
            // Get agent name
            $parent = $this->intentsClient->agentName($this->projectId);
            Log::debug('Agent parent path: ' . $parent);
            
            // List intents with basic view (faster)
            $response = $this->intentsClient->listIntents($parent, [
                'intentView' => IntentView::INTENT_VIEW_BASIC,
                'languageCode' => $this->languageCode
            ]);
            
            $intents = [];
            $count = 0;
            
            foreach ($response as $intent) {
                $intents[] = $this->formatIntentBasic($intent);
                $count++;
            }
            
            Log::info("Retrieved {$count} intents from Dialogflow");
            
            // If we need full details, fetch them individually
            $intentsWithDetails = [];
            foreach ($intents as $intent) {
                try {
                    $fullIntent = $this->getIntent($intent['id']);
                    $intentsWithDetails[] = $fullIntent;
                } catch (\Exception $e) {
                    Log::warning('Failed to get full details for intent ' . $intent['id'] . ': ' . $e->getMessage());
                    $intentsWithDetails[] = $intent;
                }
            }
            
            return $intentsWithDetails;
            
        } catch (ApiException $e) {
            Log::error('Dialogflow API error: ' . $e->getMessage(), [
                'status' => $e->getStatus(),
                'details' => $e->getDetails(),
                'metadata' => $e->getMetadata()
            ]);
            
            // Provide more specific error messages
            switch ($e->getStatus()) {
                case 'PERMISSION_DENIED':
                    throw new \Exception('Permission denied. Check if your service account has Dialogflow API access.');
                case 'NOT_FOUND':
                    throw new \Exception('Project not found. Check your project ID.');
                case 'UNAUTHENTICATED':
                    throw new \Exception('Authentication failed. Check your credentials.');
                default:
                    throw new \Exception('Dialogflow API error: ' . $e->getMessage());
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to list intents: ' . $e->getMessage());
            throw new \Exception('Failed to list intents: ' . $e->getMessage());
        }
    }

    /**
     * Get specific intent by ID
     */
    public function getIntent($intentId)
    {
        try {
            Log::debug('Getting intent details', ['intent_id' => $intentId]);
            
            // Construct intent name
            $intentName = $this->intentsClient->intentName($this->projectId, $intentId);
            
            // Get intent with full view
            $intent = $this->intentsClient->getIntent($intentName, [
                'intentView' => IntentView::INTENT_VIEW_FULL,
                'languageCode' => $this->languageCode
            ]);
            
            return $this->formatIntent($intent);
            
        } catch (ApiException $e) {
            Log::error('Failed to get intent: ' . $e->getMessage(), ['intent_id' => $intentId]);
            
            // Check if it's a display name instead of ID
            if ($e->getStatus() === 'NOT_FOUND') {
                // Try to find by display name
                $allIntents = $this->listIntents();
                foreach ($allIntents as $intent) {
                    if ($intent['display_name'] === $intentId) {
                        return $intent;
                    }
                }
                throw new \Exception("Intent not found: {$intentId}");
            }
            
            throw new \Exception('Failed to get intent: ' . $e->getMessage());
        }
    }

    /**
     * Format intent with basic information
     */
    private function formatIntentBasic(Intent $intent)
    {
        // Extract intent ID from name
        $nameParts = explode('/', $intent->getName());
        $intentId = end($nameParts);
        
        return [
            'id' => $intentId,
            'display_name' => $intent->getDisplayName(),
            'is_fallback' => $intent->getIsFallback(),
            'webhook_state' => $intent->getWebhookState(),
            'priority' => $intent->getPriority(),
            'created_at' => $intent->getCreateTime() ? $intent->getCreateTime()->toDateTime()->format('Y-m-d H:i:s') : null,
            'updated_at' => $intent->getUpdateTime() ? $intent->getUpdateTime()->toDateTime()->format('Y-m-d H:i:s') : null,
        ];
    }

    /**
     * Format intent with full information
     */
    private function formatIntent(Intent $intent)
    {
        // Extract intent ID from name
        $nameParts = explode('/', $intent->getName());
        $intentId = end($nameParts);
        
        // Extract training phrases
        $trainingPhrases = [];
        foreach ($intent->getTrainingPhrases() as $phrase) {
            $text = '';
            foreach ($phrase->getParts() as $part) {
                $text .= $part->getText();
            }
            if (!empty(trim($text))) {
                $trainingPhrases[] = trim($text);
            }
        }
        
        // Extract responses
        $responses = [];
        foreach ($intent->getMessages() as $message) {
            if ($message->getText()) {
                $texts = $message->getText()->getText();
                foreach ($texts as $text) {
                    if (!empty(trim($text))) {
                        $responses[] = trim($text);
                    }
                }
            }
        }
        
        return [
            'id' => $intentId,
            'display_name' => $intent->getDisplayName(),
            'training_phrases' => $trainingPhrases,
            'responses' => $responses,
            'webhook_state' => $intent->getWebhookState(),
            'priority' => $intent->getPriority(),
            'is_fallback' => $intent->getIsFallback(),
            'ml_enabled' => $intent->getMlEnabled(),
            'created_at' => $intent->getCreateTime() ? $intent->getCreateTime()->toDateTime()->format('Y-m-d H:i:s') : null,
            'updated_at' => $intent->getUpdateTime() ? $intent->getUpdateTime()->toDateTime()->format('Y-m-d H:i:s') : null,
        ];
    }

    /**
     * Create new intent
     */
    public function createIntent(array $data)
    {
        try {
            Log::info('Creating new intent', ['display_name' => $data['display_name']]);
            
            $parent = $this->intentsClient->agentName($this->projectId);
            
            // Validate required fields
            if (empty($data['display_name'])) {
                throw new \Exception('Display name is required');
            }
            
            if (empty($data['training_phrases']) || !is_array($data['training_phrases'])) {
                throw new \Exception('At least one training phrase is required');
            }
            
            if (empty($data['responses']) || !is_array($data['responses'])) {
                throw new \Exception('At least one response is required');
            }
            
            // Create training phrases
            $trainingPhrases = [];
            foreach ($data['training_phrases'] as $phrase) {
                $phrase = trim($phrase);
                if (!empty($phrase)) {
                    $part = new Part();
                    $part->setText($phrase);
                    
                    $trainingPhrase = new TrainingPhrase();
                    $trainingPhrase->setParts([$part]);
                    $trainingPhrase->setType(TrainingPhrase\Type::EXAMPLE);
                    
                    $trainingPhrases[] = $trainingPhrase;
                }
            }
            
            if (empty($trainingPhrases)) {
                throw new \Exception('No valid training phrases provided');
            }
            
            // Create response messages
            $messages = [];
            foreach ($data['responses'] as $response) {
                $response = trim($response);
                if (!empty($response)) {
                    $text = new Text();
                    $text->setText([$response]);
                    
                    $message = new Message();
                    $message->setText($text);
                    
                    $messages[] = $message;
                }
            }
            
            if (empty($messages)) {
                throw new \Exception('No valid responses provided');
            }
            
            // Create the intent
            $intent = new Intent();
            $intent->setDisplayName($data['display_name']);
            $intent->setTrainingPhrases($trainingPhrases);
            $intent->setMessages($messages);
            
            // Set optional fields
            if (!empty($data['webhook_state'])) {
                $intent->setWebhookState($data['webhook_state']);
            }
            
            if (isset($data['ml_enabled'])) {
                $intent->setMlEnabled((bool)$data['ml_enabled']);
            }
            
            if (isset($data['priority']) && is_numeric($data['priority'])) {
                $intent->setPriority((int)$data['priority']);
            }
            
            // Create the intent in Dialogflow
            $response = $this->intentsClient->createIntent($parent, $intent);
            
            Log::info('Successfully created intent: ' . $data['display_name']);
            
            return $this->formatIntent($response);
            
        } catch (ApiException $e) {
            Log::error('Dialogflow create intent API error: ' . $e->getMessage());
            throw new \Exception('Failed to create intent: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Create intent error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update existing intent
     */
    public function updateIntent($intentId, array $data)
    {
        try {
            Log::info('Updating intent', ['intent_id' => $intentId, 'display_name' => $data['display_name'] ?? 'unknown']);
            
            $intentName = $this->intentsClient->intentName($this->projectId, $intentId);
            
            // Get existing intent first
            $intent = $this->intentsClient->getIntent($intentName, [
                'intentView' => IntentView::INTENT_VIEW_FULL,
                'languageCode' => $this->languageCode
            ]);
            
            // Update display name if provided
            if (!empty($data['display_name'])) {
                $intent->setDisplayName($data['display_name']);
            }
            
            // Update training phrases if provided
            if (!empty($data['training_phrases']) && is_array($data['training_phrases'])) {
                $trainingPhrases = [];
                foreach ($data['training_phrases'] as $phrase) {
                    $phrase = trim($phrase);
                    if (!empty($phrase)) {
                        $part = new Part();
                        $part->setText($phrase);
                        
                        $trainingPhrase = new TrainingPhrase();
                        $trainingPhrase->setParts([$part]);
                        $trainingPhrase->setType(TrainingPhrase\Type::EXAMPLE);
                        
                        $trainingPhrases[] = $trainingPhrase;
                    }
                }
                
                if (!empty($trainingPhrases)) {
                    $intent->setTrainingPhrases($trainingPhrases);
                }
            }
            
            // Update responses if provided
            if (!empty($data['responses']) && is_array($data['responses'])) {
                $messages = [];
                foreach ($data['responses'] as $response) {
                    $response = trim($response);
                    if (!empty($response)) {
                        $text = new Text();
                        $text->setText([$response]);
                        
                        $message = new Message();
                        $message->setText($text);
                        
                        $messages[] = $message;
                    }
                }
                
                if (!empty($messages)) {
                    $intent->setMessages($messages);
                }
            }
            
            // Update webhook state if provided
            if (!empty($data['webhook_state'])) {
                $intent->setWebhookState($data['webhook_state']);
            }
            
            // Update the intent in Dialogflow
            $response = $this->intentsClient->updateIntent($intent, [
                'languageCode' => $this->languageCode,
                'intentView' => IntentView::INTENT_VIEW_FULL
            ]);
            
            Log::info('Successfully updated intent: ' . $intent->getDisplayName());
            
            return $this->formatIntent($response);
            
        } catch (ApiException $e) {
            Log::error('Dialogflow update intent API error: ' . $e->getMessage());
            throw new \Exception('Failed to update intent: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Update intent error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete intent
     */
    public function deleteIntent($intentId)
    {
        try {
            Log::info('Deleting intent', ['intent_id' => $intentId]);
            
            // Don't delete default fallback intents
            if (strpos($intentId, '00000000-0000-0000-0000-000000000000') !== false) {
                throw new \Exception('Cannot delete default fallback intent');
            }
            
            $intentName = $this->intentsClient->intentName($this->projectId, $intentId);
            
            // Delete the intent
            $this->intentsClient->deleteIntent($intentName);
            
            Log::info('Successfully deleted intent: ' . $intentId);
            
            return true;
            
        } catch (ApiException $e) {
            Log::error('Dialogflow delete intent API error: ' . $e->getMessage());
            
            // Check if intent was already deleted
            if ($e->getStatus() === 'NOT_FOUND') {
                Log::warning('Intent not found, may have been already deleted: ' . $intentId);
                return true;
            }
            
            throw new \Exception('Failed to delete intent: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Delete intent error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Test intent matching
     */
    public function testIntent($queryText, $sessionId = null)
    {
        try {
            Log::debug('Testing intent matching', ['query' => $queryText]);
            
            // Use the existing DialogflowService for intent detection
            $dialogflow = new DialogflowService();
            $result = $dialogflow->detectIntent($queryText, $sessionId ?? 'test-session-' . time());
            $dialogflow->close();
            
            return [
                'query' => $queryText,
                'matched_intent' => $result->getIntent() ? $result->getIntent()->getDisplayName() : null,
                'confidence' => $result->getIntentDetectionConfidence() ?? 0.0,
                'response' => $result->getFulfillmentText() ?? 'No response',
                'all_required_params_present' => $result->getAllRequiredParamsPresent() ?? false,
            ];
            
        } catch (\Exception $e) {
            Log::error('Dialogflow test intent error: ' . $e->getMessage());
            throw new \Exception('Failed to test intent: ' . $e->getMessage());
        }
    }

    /**
     * Get intent statistics
     */
    public function getStats()
    {
        try {
            $intents = $this->listIntents();
            
            $stats = [
                'total_intents' => count($intents),
                'total_training_phrases' => 0,
                'total_responses' => 0,
                'webhook_enabled' => 0,
                'fallback_intents' => 0,
                'ml_enabled' => 0,
            ];
            
            foreach ($intents as $intent) {
                $stats['total_training_phrases'] += count($intent['training_phrases']);
                $stats['total_responses'] += count($intent['responses']);
                
                if ($intent['webhook_state'] === 'WEBHOOK_STATE_ENABLED') {
                    $stats['webhook_enabled']++;
                }
                
                if ($intent['is_fallback']) {
                    $stats['fallback_intents']++;
                }
                
                if ($intent['ml_enabled']) {
                    $stats['ml_enabled']++;
                }
            }
            
            $stats['average_phrases_per_intent'] = $stats['total_intents'] > 0 
                ? round($stats['total_training_phrases'] / $stats['total_intents'], 1) 
                : 0;
                
            $stats['average_responses_per_intent'] = $stats['total_intents'] > 0 
                ? round($stats['total_responses'] / $stats['total_intents'], 1) 
                : 0;
            
            return $stats;
            
        } catch (\Exception $e) {
            Log::error('Get intent stats error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Clean up client connection
     */
    public function __destruct()
    {
        if ($this->intentsClient) {
            try {
                $this->intentsClient->close();
                Log::debug('Dialogflow IntentsClient closed');
            } catch (\Exception $e) {
                Log::error('Error closing Dialogflow client: ' . $e->getMessage());
            }
        }
    }
}
=======
        $this->sessionsClient = new SessionsClient([
            'credentials' => base_path('aihra-key.json'),
        ]);
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

    public function close()
    {
        $this->sessionsClient->close();
    }
}
>>>>>>> 21f0fed8913e61a3dc40934bf89c506deb9e72b9
