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
                // Return empty result - frontend will show simple prompt
                return response()->json([
                    'type' => 'empty',
                    'data' => [],
                    'message' => 'Ask me anything!'
                ]);
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

            // 🆕 FIXED: If no children, return the answer directly from database
            if ($children->isEmpty()) {
                // If it has an answer in the database, use that
                if ($currentQuestion->answer_text) {
                    return response()->json([
                        'type' => 'final',
                        'data' => [['answer' => $currentQuestion->answer_text]],
                        'level' => $currentQuestion->LEVEL
                    ]);
                }
                
                // If Level 3 but no answer, return a helpful default message
                if ($currentQuestion->LEVEL == 3) {
                    Log::info('Level 3 question with no answer: ' . $currentQuestion->question_text);
                    return response()->json([
                        'type' => 'final',
                        'data' => [[
                            'answer' => "I don't have detailed information about '{$currentQuestion->question_text}' in my knowledge base yet. Would you like me to create a support ticket for HR to provide you with specific information about this topic?"
                        ]],
                        'level' => $currentQuestion->LEVEL,
                        'suggest_escalation' => true
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
            $fulfillmentText = $result->getFulfillmentText();
            
            // If Dialogflow returns a generic fallback response, provide a better message
            if (empty($fulfillmentText) || 
                stripos($fulfillmentText, 'having trouble') !== false || 
                stripos($fulfillmentText, "I'm having trouble") !== false ||
                stripos($fulfillmentText, 'guide you through') !== false ||
                $confidence < 0.5) {
                $fulfillmentText = "I don't have specific information about '{$questionText}' right now. Would you like me to escalate this to HR so they can provide you with detailed information?";
            }

            // 🧾 Save to query log for analytics
            $this->saveGuidedQuery($questionText, $fulfillmentText, $confidence, $employeeNum);

            // --- Persist guided user selection and bot reply into chat_messages so they appear in history ---
            try {
                // Try to find an existing conversation by session_id or create one for this user/session
                $conversation = null;
                if (class_exists('\App\\Models\\Conversation')) {
                    $conversation = \App\Models\Conversation::where('session_id', $sessionId)
                        ->orderBy('created_at', 'desc')
                        ->first();
                    $userId = Auth::id();

                    if ($conversation && empty($conversation->user_id) && $userId) {
                        $conversation->user_id = $userId;
                        $conversation->save();
                    }

                    if (!$conversation) {
                        $conversation = \App\Models\Conversation::create([
                            'user_id' => $userId,
                            'session_id' => $sessionId,
                            'first_message' => $questionText,
                            'title' => null,
                        ]);
                    } else {
                        if (empty($conversation->first_message)) {
                            $conversation->first_message = $questionText;
                            if (empty($conversation->title)) {
                                $conversation->title = now()->toDateString() . ' - ' . Str::limit($questionText, 80);
                            }
                            $conversation->save();
                        }
                    }

                    // Save the user's guided selection as an employee message
                    if (class_exists('\App\\Models\\ChatMessage')) {
                        \App\Models\ChatMessage::create([
                            'ticket_no' => null,
                            'sender' => 'employee',
                            'message' => $questionText,
                            'conversation_id' => $conversation->id,
                        ]);

                        // Save the bot reply
                        \App\Models\ChatMessage::create([
                            'ticket_no' => null,
                            'sender' => 'bot',
                            'message' => $fulfillmentText,
                            'conversation_id' => $conversation->id,
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to persist guided conversation messages: ' . $e->getMessage());
            }

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
                    'answer' => "I don't have detailed information about '{$question->question_text}' in my knowledge base yet. Would you like me to create a support ticket so our HR team can provide you with accurate information?"
                ]],
                'level' => 3,
                'handled_by' => 'Fallback',
                'suggest_escalation' => true
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