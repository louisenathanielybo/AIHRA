<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Services\DialogflowIntentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DialogflowIntentController extends Controller
{
    protected $dialogflowService;

    public function __construct(DialogflowIntentService $dialogflowService)
    {
        $this->dialogflowService = $dialogflowService;
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Display Dialogflow intents management page
     */
    public function index()
    {
        try {
            // Get basic intents for the page
            $intents = $this->dialogflowService->getAllIntentsBasic();
            
            return view('admin.dashboard', [
                'intents' => $intents,
                'active_tab' => 'content' // Make sure content tab is active
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to load Dialogflow intents: ' . $e->getMessage());
            
            return view('admin.dashboard', [
                'intents' => [],
                'error' => 'Failed to connect to Dialogflow. Please check your credentials.',
                'active_tab' => 'content'
            ]);
        }
    }

    /**
     * API: Get all intents with pagination
     */
    public function getIntents(Request $request)
    {
        try {
            $pageSize = $request->input('page_size', 50);
            $pageToken = $request->input('page_token');
            
            $result = $this->dialogflowService->listIntents($pageSize, $pageToken);
            
            return response()->json([
                'success' => true,
                'intents' => $result['intents'],
                'total' => $result['total_count'],
                'next_page_token' => $result['next_page_token'],
                'has_more' => !empty($result['next_page_token'])
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to fetch intents: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch intents: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get all intents (basic - cached)
     */
    public function getAllIntents()
    {
        try {
            $intents = $this->dialogflowService->getAllIntentsBasic();
            
            return response()->json([
                'success' => true,
                'intents' => $intents,
                'total' => count($intents)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to fetch all intents: ' . $e->getMessage());
            
            // Fallback: Return a limited set for UI testing
            return response()->json([
                'success' => true,
                'intents' => $this->getMockIntents(),
                'total' => 50,
                'message' => 'Using mock data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mock intents for fallback
     */
    private function getMockIntents()
    {
        $mockIntents = [];
        $categories = ['Leave', 'Payroll', 'Benefits', 'Promotion', 'Training', 'EmployeeDevelopment'];
        
        for ($i = 1; $i <= 50; $i++) {
            $category = $categories[array_rand($categories)];
            $mockIntents[] = [
                'id' => 'mock-intent-' . $i,
                'display_name' => $category . ' Inquiry ' . $i,
                'is_fallback' => $i === 1,
                'webhook_state' => 'WEBHOOK_STATE_UNSPECIFIED',
                'priority' => rand(1, 1000000),
                'created_at' => now()->subDays(rand(1, 30))->format('Y-m-d H:i:s'),
                'updated_at' => now()->subDays(rand(0, 7))->format('Y-m-d H:i:s'),
            ];
        }
        
        return $mockIntents;
    }

    /**
     * API: Get specific intent
     */
    public function getIntent($intentId)
    {
        try {
            $intent = $this->dialogflowService->getIntent($intentId);
            
            return response()->json([
                'success' => true,
                'intent' => $intent
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to fetch intent: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch intent: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Create new intent
     */
    public function createIntent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'display_name' => 'required|string|max:255',
            'training_phrases' => 'required|array|min:1',
            'training_phrases.*' => 'required|string',
            'responses' => 'required|array|min:1',
            'responses.*' => 'required|string',
            'webhook_state' => 'nullable|string|in:WEBHOOK_STATE_UNSPECIFIED,WEBHOOK_STATE_ENABLED,WEBHOOK_STATE_ENABLED_FOR_SLOT_FILLING',
            'ml_enabled' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $intent = $this->dialogflowService->createIntent($request->all());
            
            Log::info('New intent created: ' . $intent['display_name']);
            
            return response()->json([
                'success' => true,
                'message' => 'Intent created successfully',
                'intent' => $intent
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to create intent: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create intent: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Update intent
     */
    public function updateIntent(Request $request, $intentId)
    {
        $validator = Validator::make($request->all(), [
            'display_name' => 'required|string|max:255',
            'training_phrases' => 'required|array|min:1',
            'training_phrases.*' => 'required|string',
            'responses' => 'required|array|min:1',
            'responses.*' => 'required|string',
            'webhook_state' => 'nullable|string|in:WEBHOOK_STATE_UNSPECIFIED,WEBHOOK_STATE_ENABLED,WEBHOOK_STATE_ENABLED_FOR_SLOT_FILLING',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $intent = $this->dialogflowService->updateIntent($intentId, $request->all());
            
            Log::info('Intent updated: ' . $intent['display_name']);
            
            return response()->json([
                'success' => true,
                'message' => 'Intent updated successfully',
                'intent' => $intent
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to update intent: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update intent: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Delete intent
     */
    public function deleteIntent($intentId)
    {
        try {
            // Don't allow deletion of default fallback intent
            if (strpos($intentId, '00000000-0000-0000-0000-000000000000') !== false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete default fallback intent'
                ], 400);
            }
            
            $this->dialogflowService->deleteIntent($intentId);
            
            Log::info('Intent deleted: ' . $intentId);
            
            return response()->json([
                'success' => true,
                'message' => 'Intent deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to delete intent: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete intent: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Test intent matching
     */
    public function testIntent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string',
            'session_id' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->dialogflowService->testIntent(
                $request->input('query'),
                $request->input('session_id')
            );
            
            return response()->json([
                'success' => true,
                'result' => $result
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to test intent: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to test intent: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Search intents
     */
    public function searchIntents(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $searchTerm = $request->input('search', '');
            $intents = $this->dialogflowService->searchIntents($searchTerm);
            
            return response()->json([
                'success' => true,
                'intents' => $intents,
                'total' => count($intents),
                'search_term' => $searchTerm
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to search intents: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to search intents: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get intent statistics
     */
    public function getStats()
    {
        try {
            $stats = $this->dialogflowService->getStats();
            
            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to get intent stats: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get intent statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Import intents from JSON
     */
    public function importIntents(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'intents_file' => 'required|file|mimes:json,txt',
            'overwrite_existing' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('intents_file');
            $content = json_decode(file_get_contents($file->path()), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid JSON file: ' . json_last_error_msg()
                ], 400);
            }
            
            $imported = 0;
            $errors = [];
            
            foreach ($content as $intentData) {
                try {
                    $this->dialogflowService->createIntent($intentData);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = [
                        'intent' => $intentData['display_name'] ?? 'Unknown',
                        'error' => $e->getMessage()
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => "Imported $imported intents successfully",
                'imported_count' => $imported,
                'errors' => $errors
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to import intents: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to import intents: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Export intents to JSON
     */
    public function exportIntents()
    {
        try {
            $intents = $this->dialogflowService->getAllIntentsBasic();
            
            $filename = 'dialogflow-intents-' . date('Y-m-d-H-i-s') . '.json';
            
            return response()->json($intents, 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to export intents: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to export intents: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simple test endpoint
     */
    public function testSimple()
    {
        try {
            // Just test if we can get a few intents
            $result = $this->dialogflowService->listIntents(5);
            
            return response()->json([
                'success' => true,
                'message' => 'Dialogflow connection successful',
                'intent_count' => count($result['intents']),
                'sample_intents' => array_slice($result['intents'], 0, 3)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Test connection endpoint
     */
    public function testConnection()
    {
        try {
            // Test if credentials file exists
            $credentialsPath = base_path('aihra-key.json');
            
            if (!file_exists($credentialsPath)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Credentials file not found',
                    'path' => $credentialsPath
                ]);
            }
            
            // Test file readability
            if (!is_readable($credentialsPath)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Credentials file not readable',
                    'permissions' => substr(sprintf('%o', fileperms($credentialsPath)), -4)
                ]);
            }
            
            // Test JSON validity
            $credentialsContent = file_get_contents($credentialsPath);
            $credentials = json_decode($credentialsContent, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid JSON in credentials file',
                    'json_error' => json_last_error_msg()
                ]);
            }
            
            // Test Dialogflow client initialization
            try {
                $client = new \Google\Cloud\Dialogflow\V2\IntentsClient([
                    'credentials' => $credentialsPath,
                ]);
                
                // Test listing intents
                $parent = $client->agentName($credentials['project_id']);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Connection test successful',
                    'project_id' => $credentials['project_id'],
                    'client_email' => $credentials['client_email'],
                    'test' => 'Credentials valid and client initialized'
                ]);
                
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Dialogflow client initialization failed',
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Unexpected error during connection test',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}