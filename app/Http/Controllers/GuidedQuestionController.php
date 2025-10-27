<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GuidedQuestion;
use Illuminate\Support\Facades\Log;
use App\Services\DialogflowService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class GuidedQuestionController extends Controller
{
    // ✅ Load Level 1 questions (main categories)
    public function index()
    {
        try {
            $questions = GuidedQuestion::where('LEVEL', 1)
                ->get(['gq_id', 'question_text', 'answer_text', 'LEVEL']);
                
            if ($questions->isEmpty()) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'No questions available.'
                ], 404);
            }
            
            return response()->json([
                'type' => 'level1',
                'data' => $questions,
                'message' => 'Hello! Choose a topic:'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error loading guided questions: ' . $e->getMessage());
            
            return response()->json([
                'type' => 'error',
                'message' => 'Unable to load questions. Please try again.'
            ], 500);
        }
    }

    // ✅ Load questions by parent ID (next levels)
    public function show($id)
    {
        try {
            $currentQuestion = GuidedQuestion::find($id);
            
            if (!$currentQuestion) {
                return response()->json([
                    'type' => 'final',
                    'data' => [[
                        'answer' => "Topic not found. Please try another selection."
                    ]]
                ]);
            }

            $children = GuidedQuestion::where('parent_id', $id)
                ->get(['gq_id', 'question_text', 'answer_text', 'LEVEL']);

            // 🆕 FIXED: If no children and this is Level 3, send to Dialogflow
            if ($children->isEmpty()) {
                if ($currentQuestion->LEVEL == 3) {
                    Log::info('Level 3 question reached, sending to Dialogflow: ' . $currentQuestion->question_text);
                    return $this->handleLevel3Question($currentQuestion);
                }
                
                // If it's not Level 3 but has an answer, return the answer
                if ($currentQuestion->answer_text) {
                    return response()->json([
                        'type' => 'final',
                        'data' => [['answer' => $currentQuestion->answer_text]],
                        'level' => $currentQuestion->LEVEL
                    ]);
                }
                
                // If no children, no answer, and not Level 3
                return response()->json([
                    'type' => 'final',
                    'data' => [[
                        'answer' => "Please try asking your question directly in the chat or contact HR for assistance."
                    ]]
                ]);
            }

            // If there are children, show next level questions
            $nextLevel = $currentQuestion->LEVEL + 1;
            $message = $this->getLevelMessage($nextLevel);
            
            return response()->json([
                'type' => 'level' . $nextLevel,
                'data' => $children,
                'message' => $message,
                'current_level' => $currentQuestion->LEVEL
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error loading guided question details: ' . $e->getMessage());
            
            return response()->json([
                'type' => 'error',
                'message' => 'Error loading question details. Please try again.'
            ], 500);
        }
    }

    /**
     * Handle Level 3 questions by sending to Dialogflow
     */
    private function handleLevel3Question($question)
    {
        try {
            $questionText = $question->question_text;
            $employeeNum = Auth::check() ? Auth::user()->employeeNum : 0;
            
            Log::info('Sending Level 3 question to Dialogflow: ' . $questionText);

            // 🎯 Initialize Dialogflow
            $sessionId = session()->getId() ?? Str::random(10);
            $dialogflow = new DialogflowService();
            $result = $dialogflow->detectIntent($questionText, $sessionId);
            $dialogflow->close();

            // Extract Dialogflow results
            $confidence = $result->getIntentDetectionConfidence();
            $fulfillmentText = $result->getFulfillmentText() ?? "I understand you're asking about '{$questionText}'. For detailed information about this, please contact the HR department.";

            // 🧾 Save to query log for analytics
            $this->saveGuidedQuery($questionText, $fulfillmentText, $confidence, $employeeNum);

            // ✅ Return Dialogflow's answer
            return response()->json([
                'type' => 'final',
                'data' => [['answer' => $fulfillmentText]],
                'level' => 3,
                'confidence' => $confidence,
                'handled_by' => 'Dialogflow'
            ]);

        } catch (\Exception $e) {
            Log::error('Dialogflow error in guided questions: ' . $e->getMessage());

            // Fallback if Dialogflow fails
            return response()->json([
                'type' => 'final',
                'data' => [[
                    'answer' => "I'm having trouble retrieving information about '{$question->question_text}' right now. Please try asking this question directly in the chat."
                ]],
                'level' => 3,
                'handled_by' => 'Fallback'
            ]);
        }
    }

    /**
     * Save guided query to database
     */
    private function saveGuidedQuery($question, $response, $confidence, $employeeNum)
    {
        try {
            if (class_exists('App\Models\Query')) {
                \App\Models\Query::create([
                    'queryID' => Str::uuid(),
                    'employeeNum' => $employeeNum,
                    'question' => $question,
                    'response' => $response,
                    'confidenceScore' => $confidence,
                    'queryType' => 'Guided',
                    'questionTime' => now(),
                    'responseTime' => now(),
                    'isEscalated' => false,
                    'handledBy' => 'Bot',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to save guided query: ' . $e->getMessage());
        }
    }

    /**
     * Get appropriate message for each level
     */
    private function getLevelMessage($level)
    {
        $messages = [
            1 => 'Hello! Choose a topic:',
            2 => 'Please choose a question:',
            3 => 'Select specific question:'
        ];

        return $messages[$level] ?? 'Please choose one:';
    }
}