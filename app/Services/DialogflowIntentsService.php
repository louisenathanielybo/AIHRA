<?php

namespace App\Services;

use Google\Cloud\Dialogflow\V2\IntentsClient;
use Google\Cloud\Dialogflow\V2\Intent;
use Google\Cloud\Dialogflow\V2\Intent\TrainingPhrase;
use Google\Cloud\Dialogflow\V2\Intent\TrainingPhrase\Part;
use Google\Cloud\Dialogflow\V2\Intent\Message;
use Google\Cloud\Dialogflow\V2\Intent\Message\Text;
use Google\Cloud\Dialogflow\V2\Intent\Parameter;
use Google\Cloud\Dialogflow\V2\Context;
use Google\Cloud\Dialogflow\V2\IntentView;
use Google\ApiCore\ApiException;
use Google\ApiCore\ValidationException;
use Illuminate\Support\Facades\Log;

class DialogflowIntentService
{
    protected $intentsClient;
    protected $projectId;
    protected $languageCode;

    public function __construct()
    {
        $this->projectId = 'aihra-472311'; // Your project ID
        $this->languageCode = 'en';
        
        try {
            // Initialize the IntentsClient with your credentials
            $this->intentsClient = new IntentsClient([
                'credentials' => base_path('aihra-key.json'),
            ]);
        } catch (ValidationException $e) {
            Log::error('Failed to initialize Dialogflow IntentsClient: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get all intents from Dialogflow
     */
    public function listIntents()
    {
        try {
            $parent = $this->intentsClient->agentName($this->projectId);
            
            // List all intents with full view
            $response = $this->intentsClient->listIntents($parent, [
                'intentView' => IntentView::INTENT_VIEW_FULL,
                'languageCode' => $this->languageCode
            ]);
            
            $intents = [];
            foreach ($response as $intent) {
                $intents[] = $this->formatIntent($intent);
            }
            
            return $intents;
            
        } catch (ApiException $e) {
            Log::error('Dialogflow list intents error: ' . $e->getMessage());
            throw new \Exception('Failed to list intents: ' . $e->getMessage());
        }
    }

    /**
     * Get specific intent by ID
     */
    public function getIntent($intentId)
    {
        try {
            $intentName = $this->intentsClient->intentName($this->projectId, $intentId);
            
            // Get intent with full view
            $intent = $this->intentsClient->getIntent($intentName, [
                'intentView' => IntentView::INTENT_VIEW_FULL,
                'languageCode' => $this->languageCode
            ]);
            
            return $this->formatIntent($intent);
            
        } catch (ApiException $e) {
            Log::error('Dialogflow get intent error: ' . $e->getMessage());
            
            // If intent not found by ID, try to find by display name
            if ($e->getStatus() === 'NOT_FOUND') {
                // Try to find intent by iterating through all intents
                $allIntents = $this->listIntents();
                foreach ($allIntents as $intent) {
                    if ($intent['id'] === $intentId || $intent['display_name'] === $intentId) {
                        return $intent;
                    }
                }
            }
            
            throw new \Exception('Failed to get intent: ' . $e->getMessage());
        }
    }

    /**
     * Create new intent
     */
    public function createIntent(array $data)
    {
        try {
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
                if (!empty(trim($phrase))) {
                    $part = new Part();
                    $part->setText(trim($phrase));
                    
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
                if (!empty(trim($response))) {
                    $text = new Text();
                    $text->setText([trim($response)]);
                    
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
            
            // Set webhook state if provided
            if (!empty($data['webhook_state'])) {
                $intent->setWebhookState($data['webhook_state']);
            }
            
            // Set ML enabled if provided
            if (isset($data['ml_enabled'])) {
                $intent->setMlEnabled((bool)$data['ml_enabled']);
            }
            
            // Create the intent in Dialogflow
            $response = $this->intentsClient->createIntent($parent, $intent);
            
            Log::info('Created new Dialogflow intent: ' . $data['display_name']);
            
            return $this->formatIntent($response);
            
        } catch (ApiException $e) {
            Log::error('Dialogflow create intent error: ' . $e->getMessage());
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
                    if (!empty(trim($phrase))) {
                        $part = new Part();
                        $part->setText(trim($phrase));
                        
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
                    if (!empty(trim($response))) {
                        $text = new Text();
                        $text->setText([trim($response)]);
                        
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
            
            Log::info('Updated Dialogflow intent: ' . $intent->getDisplayName());
            
            return $this->formatIntent($response);
            
        } catch (ApiException $e) {
            Log::error('Dialogflow update intent error: ' . $e->getMessage());
            throw new \Exception('Failed to update intent: ' . $e->getMessage());
        }
    }

    /**
     * Delete intent
     */
    public function deleteIntent($intentId)
    {
        try {
            // Don't delete default fallback intents
            if (strpos($intentId, '00000000-0000-0000-0000-000000000000') !== false) {
                throw new \Exception('Cannot delete default fallback intent');
            }
            
            $intentName = $this->intentsClient->intentName($this->projectId, $intentId);
            
            // Get intent first to check if it's a fallback
            try {
                $intent = $this->intentsClient->getIntent($intentName);
                if ($intent->getIsFallback()) {
                    throw new \Exception('Cannot delete fallback intent');
                }
            } catch (ApiException $e) {
                // If we can't get the intent, still try to delete (might be already deleted)
            }
            
            // Delete the intent
            $this->intentsClient->deleteIntent($intentName);
            
            Log::info('Deleted Dialogflow intent: ' . $intentId);
            
            return true;
            
        } catch (ApiException $e) {
            Log::error('Dialogflow delete intent error: ' . $e->getMessage());
            
            // Check if intent was already deleted
            if ($e->getStatus() === 'NOT_FOUND') {
                Log::warning('Intent not found, may have been already deleted: ' . $intentId);
                return true;
            }
            
            throw new \Exception('Failed to delete intent: ' . $e->getMessage());
        }
    }

    /**
     * Test intent matching
     */
    public function testIntent($queryText, $sessionId = null)
    {
        try {
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
     * Format intent data for response
     */
    private function formatIntent(Intent $intent)
    {
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
        
        // Extract parameters
        $parameters = [];
        foreach ($intent->getParameters() as $param) {
            $parameters[] = [
                'name' => $param->getDisplayName(),
                'entity_type' => $param->getEntityTypeDisplayName(),
                'value' => $param->getValue(),
                'mandatory' => $param->getMandatory(),
            ];
        }
        
        // Extract input contexts
        $inputContexts = [];
        foreach ($intent->getInputContextNames() as $context) {
            $inputContexts[] = $context;
        }
        
        // Extract output contexts
        $outputContexts = [];
        foreach ($intent->getOutputContexts() as $context) {
            $outputContexts[] = $context->getName();
        }
        
        // Extract intent ID from name
        $nameParts = explode('/', $intent->getName());
        $intentId = end($nameParts);
        
        return [
            'id' => $intentId,
            'display_name' => $intent->getDisplayName(),
            'training_phrases' => $trainingPhrases,
            'responses' => $responses,
            'webhook_state' => $intent->getWebhookState(),
            'input_contexts' => $inputContexts,
            'output_contexts' => $outputContexts,
            'parameters' => $parameters,
            'priority' => $intent->getPriority(),
            'is_fallback' => $intent->getIsFallback(),
            'ml_enabled' => $intent->getMlEnabled(),
            'created_at' => $intent->getCreateTime() ? $intent->getCreateTime()->toDateTime()->format('Y-m-d H:i:s') : null,
            'updated_at' => $intent->getUpdateTime() ? $intent->getUpdateTime()->toDateTime()->format('Y-m-d H:i:s') : null,
        ];
    }

    /**
     * Search intents by name or content
     */
    public function searchIntents($searchTerm)
    {
        try {
            $allIntents = $this->listIntents();
            
            if (empty($searchTerm)) {
                return $allIntents;
            }
            
            $searchTerm = strtolower($searchTerm);
            $filteredIntents = [];
            
            foreach ($allIntents as $intent) {
                // Search in display name
                if (stripos($intent['display_name'], $searchTerm) !== false) {
                    $filteredIntents[] = $intent;
                    continue;
                }
                
                // Search in training phrases
                foreach ($intent['training_phrases'] as $phrase) {
                    if (stripos($phrase, $searchTerm) !== false) {
                        $filteredIntents[] = $intent;
                        continue 2;
                    }
                }
                
                // Search in responses
                foreach ($intent['responses'] as $response) {
                    if (stripos($response, $searchTerm) !== false) {
                        $filteredIntents[] = $intent;
                        continue 2;
                    }
                }
            }
            
            return $filteredIntents;
            
        } catch (\Exception $e) {
            Log::error('Search intents error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get intent statistics
     */
    public function getStats()
    {
        try {
            $intents = $this->listIntents();
            
            $totalPhrases = 0;
            $totalResponses = 0;
            $webhookEnabled = 0;
            $fallbackIntents = 0;
            
            foreach ($intents as $intent) {
                $totalPhrases += count($intent['training_phrases']);
                $totalResponses += count($intent['responses']);
                
                if ($intent['webhook_state'] === 'WEBHOOK_STATE_ENABLED') {
                    $webhookEnabled++;
                }
                
                if ($intent['is_fallback']) {
                    $fallbackIntents++;
                }
            }
            
            return [
                'total_intents' => count($intents),
                'total_training_phrases' => $totalPhrases,
                'total_responses' => $totalResponses,
                'webhook_enabled' => $webhookEnabled,
                'fallback_intents' => $fallbackIntents,
                'average_phrases_per_intent' => count($intents) > 0 ? round($totalPhrases / count($intents), 1) : 0,
                'average_responses_per_intent' => count($intents) > 0 ? round($totalResponses / count($intents), 1) : 0,
            ];
            
        } catch (\Exception $e) {
            Log::error('Get intent stats error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Bulk import intents
     */
    public function importIntents(array $intentsData, $overwriteExisting = false)
    {
        try {
            $imported = 0;
            $errors = [];
            
            foreach ($intentsData as $index => $intentData) {
                try {
                    // Validate intent data
                    if (empty($intentData['display_name'])) {
                        throw new \Exception('Display name is required');
                    }
                    
                    if (empty($intentData['training_phrases']) || !is_array($intentData['training_phrases'])) {
                        throw new \Exception('Training phrases are required');
                    }
                    
                    if (empty($intentData['responses']) || !is_array($intentData['responses'])) {
                        throw new \Exception('Responses are required');
                    }
                    
                    // Check if intent already exists
                    $existingIntents = $this->listIntents();
                    $exists = false;
                    $existingIntentId = null;
                    
                    foreach ($existingIntents as $existingIntent) {
                        if ($existingIntent['display_name'] === $intentData['display_name']) {
                            $exists = true;
                            $existingIntentId = $existingIntent['id'];
                            break;
                        }
                    }
                    
                    if ($exists && $overwriteExisting) {
                        // Update existing intent
                        $this->updateIntent($existingIntentId, $intentData);
                        $imported++;
                    } elseif (!$exists) {
                        // Create new intent
                        $this->createIntent($intentData);
                        $imported++;
                    } else {
                        // Skip existing intent
                        $errors[] = [
                            'intent' => $intentData['display_name'],
                            'error' => 'Intent already exists (skipped)'
                        ];
                    }
                    
                } catch (\Exception $e) {
                    $errors[] = [
                        'intent' => $intentData['display_name'] ?? "Intent #{$index}",
                        'error' => $e->getMessage()
                    ];
                }
            }
            
            return [
                'imported' => $imported,
                'errors' => $errors
            ];
            
        } catch (\Exception $e) {
            Log::error('Import intents error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Export intents to array
     */
    public function exportIntents()
    {
        try {
            return $this->listIntents();
        } catch (\Exception $e) {
            Log::error('Export intents error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Clean up client connection
     */
    public function __destruct()
    {
        if ($this->intentsClient) {
            $this->intentsClient->close();
        }
    }
}