<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Query;
use App\Models\HrInbox;
use App\Models\GuidedQuestion;
use App\Models\Conversation;
use App\Models\ChatMessage;
use Illuminate\Support\Str;
use App\Services\DialogflowService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class DialogflowController extends Controller
{
    private $maxRetries = 3;

    public function webhook(Request $request)
    {
        try {
            Log::info('🔍 Dialogflow Webhook Called', ['input' => $request->all()]);

            // Capture question time at the very start for accurate response time calculation
            $questionTimeFormatted = \Carbon\Carbon::now()->format('Y-m-d H:i:s.u');
            
            // 🆕 FIXED: Handle multiple input formats
            $queryText = $this->extractQueryText($request);
            $employeeNum = Auth::check() ? Auth::user()->employeeNum : 0;

            // Track user and session for conversation history
            $userId = Auth::id();
            $sessionId = $request->input('sessionId') ?? session()->getId();
            $conversation = null;

            // If we have a non-empty incoming message, create/find conversation and store the user message
            if (!empty($queryText)) {
                try {
                    // Prefer matching by session_id so messages attach to the conversation
                    // created by the frontend when the user clicked "New" (which sets session_id).
                    $conversation = Conversation::where('session_id', $sessionId)
                        ->orderBy('created_at', 'desc')
                        ->first();

                    // If we found a conversation but it isn't linked to the authenticated
                    // user yet, and we have a user id, attach it.
                    if ($conversation && empty($conversation->user_id) && $userId) {
                        $conversation->user_id = $userId;
                        $conversation->save();
                    }

                    // If no conversation exists for this session, create one.
                    if (!$conversation) {
                        $conversation = Conversation::create([
                            'user_id' => $userId,
                            'session_id' => $sessionId,
                            'first_message' => $queryText,
                            'title' => null,
                        ]);
                    } else {
                        // If the conversation exists but has no first_message, set it now
                        if (empty($conversation->first_message) && !empty($queryText)) {
                            $conversation->first_message = $queryText;
                            if (empty($conversation->title)) {
                                $conversation->title = now()->toDateString() . ' - ' . Str::limit($queryText, 80);
                            }
                            $conversation->save();
                        }
                    }

                    ChatMessage::create([
                        'ticket_no' => null,
                        'sender' => 'employee',
                        'message' => $queryText,
                        'conversation_id' => $conversation->id
                    ]);
                } catch (\Throwable $e) {
                    Log::warning('Failed to persist conversation or incoming message: ' . $e->getMessage());
                    // Continue without blocking chatbot response
                }
            }

            if (empty($queryText)) {
                Log::warning('Empty query text received');
                return response()->json([
                    'status' => 'success',
                    'fulfillmentText' => "I didn't receive your message. Could you please try again?"
                ]);
            }

            Log::info('Processing query:', ['query' => $queryText, 'employee' => $employeeNum]);

            // 🆕 NEW: Check if this is a retry after multiple failed attempts
            $retryResponse = $this->handleRetryScenario($queryText, $employeeNum, $conversation);
            if ($retryResponse) {
                return $retryResponse;
            }

            // 💬 Handle greetings - start guided flow immediately
            if ($this->isGreeting($queryText)) {
                Log::info('Greeting detected, starting guided flow');
                // 🆕 Reset retry counter for new conversation
                $this->resetRetryCount();

                $reply = "👋 Hello! I'm here to help with HR questions. Let me guide you to the right information.";

                // Save bot reply into conversation if available
                if (!empty($conversation)) {
                    try {
                        ChatMessage::create([
                            'ticket_no' => null,
                            'sender' => 'bot',
                            'message' => $reply,
                            'conversation_id' => $conversation->id
                        ]);

                        if (empty($conversation->title)) {
                            $conversation->title = now()->toDateString() . ' - ' . Str::limit($conversation->first_message ?? $queryText, 80);
                            $conversation->save();
                        }
                    } catch (\Throwable $e) {
                        Log::warning('Failed to save bot greeting message: ' . $e->getMessage());
                    }
                }

                return response()->json([
                    'status' => 'guided_flow',
                    'fulfillmentText' => $reply,
                    'guided_flow' => true
                ]);
            }

            // 🔥 IMPROVED: Handle explicit escalation requests - ALWAYS require confirmation
            if ($this->isEscalationRequest($queryText)) {
                Log::info('Escalation request detected', ['query' => $queryText]);
                $this->resetRetryCount();

                // Check if this conversation has already been escalated
                if ($this->isAlreadyEscalated($conversation)) {
                    Log::info('Conversation already escalated, informing user');
                    $existingTicket = $this->getExistingTicket($conversation);
                    $ticketInfo = $existingTicket ? " Your existing ticket is: <strong>{$existingTicket}</strong>" : '';
                    
                    return response()->json([
                        'status' => 'already_escalated',
                        'fulfillmentText' => "I see that you've already created a support ticket for this conversation! 📋{$ticketInfo}<br><br>Our HR team is working on it. Each conversation can only be escalated once to ensure your requests are properly tracked. Is there anything else I can help you with while you wait for HR's response?",
                        'already_escalated' => true
                    ]);
                }

                // Store pending escalation and ask for confirmation
                Session::put('pending_escalation', [
                    'query' => $queryText,
                    'employeeNum' => $employeeNum,
                    'reason' => 'User requested human assistance',
                    'conversation_id' => $conversation ? $conversation->id : null,
                    'timestamp' => now()->toIso8601String()
                ]);

                return response()->json([
                    'status' => 'confirm_escalation',
                    'fulfillmentText' => "I understand you'd like to speak with our HR team! 💼 Before I connect you, please note that each conversation can only be escalated once.<br><br>Would you like me to create a support ticket for you?",
                    'needs_confirmation' => true,
                    'options' => [
                        ['text' => '✅ Yes, please create a ticket', 'action' => 'escalate'],
                        ['text' => '❌ No, I\'ll try asking differently', 'action' => 'rephrase']
                    ]
                ]);
            }

            // 💬 Handle simple conversational responses
            $conversationalResponse = $this->handleConversationalQueries($queryText);
            if ($conversationalResponse) {
                $this->resetRetryCount();

                if (!empty($conversation)) {
                    try {
                        $payload = $conversationalResponse->getData(true);
                        $botText = $payload['fulfillmentText'] ?? null;
                        if ($botText) {
                            ChatMessage::create([
                                'ticket_no' => null,
                                'sender' => 'bot',
                                'message' => $botText,
                                'conversation_id' => $conversation->id
                            ]);

                            if (empty($conversation->title)) {
                                $conversation->title = now()->toDateString() . ' - ' . Str::limit($conversation->first_message ?? $queryText, 80);
                                $conversation->save();
                            }
                        }
                    } catch (\Throwable $e) {
                        Log::warning('Failed to save conversational bot message: ' . $e->getMessage());
                    }
                }

                return $conversationalResponse;
            }

            

// 🎯 Try Dialogflow for direct questions
$sessionId = session()->getId() ?? Str::random(10);

Log::info('Calling Dialogflow service', ['sessionId' => $sessionId]);

try {
    $dialogflow = new DialogflowService();
    $result = $dialogflow->detectIntent($queryText, $sessionId);
    $dialogflow->close();

    // SAFE HANDLING: Check if we got a valid Dialogflow response
$confidence = 0.0;
$fulfillmentText = '';
$intentName = 'Default Fallback Intent';

// SAFER CHECK: Handle both real Dialogflow objects and our mock objects
try {
    // Try to extract fulfillment text
    if (is_object($result) && method_exists($result, 'getFulfillmentText')) {
        $fulfillmentText = $result->getFulfillmentText();
    } elseif (is_object($result) && property_exists($result, 'fulfillmentText')) {
        $fulfillmentText = $result->fulfillmentText;
    }
    
    // Try to extract confidence
    if (is_object($result) && method_exists($result, 'getIntentDetectionConfidence')) {
        $confidence = $result->getIntentDetectionConfidence() ?? 0.0;
    } elseif (is_object($result) && property_exists($result, 'intentDetectionConfidence')) {
        $confidence = $result->intentDetectionConfidence ?? 0.0;
    }
    
    // Try to extract intent name
    if (is_object($result) && method_exists($result, 'getIntent')) {
        $intent = $result->getIntent();
        if (is_object($intent) && method_exists($intent, 'getDisplayName')) {
            $intentName = $intent->getDisplayName();
        }
    } elseif (is_object($result) && property_exists($result, 'intent')) {
        $intentObj = $result->intent;
        if (is_object($intentObj) && property_exists($intentObj, 'displayName')) {
            $intentName = $intentObj->displayName;
        }
    }
    
    // If we still don't have a fulfillment text, create one
    if (empty($fulfillmentText)) {
        $queryLower = strtolower($queryText);
        
        if (strpos($queryLower, 'working hours') !== false || strpos($queryLower, 'work hours') !== false) {
            $fulfillmentText = "Our standard working hours are from 8:00 AM to 5:00 PM, Monday to Friday, with a 1-hour lunch break from 12:00 PM to 1:00 PM. We also offer flexible time arrangements for eligible employees!";
            $confidence = 0.9;
            $intentName = 'working.hours.inquiry';
        }
        // ... add other keyword checks if needed
    }
    
    Log::info('✅ Dialogflow Response Processed', [
        'confidence' => $confidence,
        'intent' => $intentName,
        'fulfillmentText' => substr($fulfillmentText, 0, 200),
        'result_type' => get_class($result) ?? gettype($result)
    ]);
    
} catch (\Exception $e) {
    Log::warning('Error processing Dialogflow response: ' . $e->getMessage());
    // Use keyword-based fallback
    $fulfillmentText = $this->getKeywordResponse($queryText);
    $confidence = 0.6;
}

    // 🆕 NEW: Check if this is HR-related but bot can't answer properly
    $isHRRelated = $this->isHRRelatedQuestion($queryText);
    $cantAnswer = $this->cantAnswerQuestion($confidence, $intentName, $fulfillmentText);

    if ($isHRRelated && $cantAnswer) {
        Log::info('HR-related question detected but bot cannot answer', [
            'confidence' => $confidence,
            'intent' => $intentName
        ]);
        return $this->suggestHREscalation($queryText, $employeeNum, "HR-related question with low confidence");
    }

    // 🔥 IMPROVED: Auto-escalate based on multiple factors
    // Auto-escalation removed. Escalation now always requires user confirmation after 3 strikes.

    // 🆕 NEW: Handle retry logic for unclear questions
    if ($this->shouldRetry($confidence, $intentName)) {
        // Track strikes per conversation
        $conversationId = $conversation ? $conversation->id : 'no_convo';
        $strikes = Session::get('strikes_' . $conversationId, 0) + 1;
        Session::put('strikes_' . $conversationId, $strikes);

        Log::info('Low confidence response, prompting retry', [
            'strikes' => $strikes,
            'confidence' => $confidence
        ]);

        // Store original query details for potential escalation
        Session::put('pending_escalation', [
            'query' => $queryText,
            'confidence' => $confidence,
            'intent' => $intentName,
            'timestamp' => now()->toIso8601String(),
            'conversation_id' => $conversationId
        ]);

        if ($strikes >= 3) {
            // Ask user if they want to escalate
            return $this->offerHREscalation($queryText, $employeeNum, $confidence);
        }

        $retryText = $this->getRetryMessage($strikes);

        if (!empty($conversation)) {
            try {
                ChatMessage::create([
                    'ticket_no' => null,
                    'sender' => 'bot',
                    'message' => $retryText,
                    'conversation_id' => $conversation->id
                ]);

                if (empty($conversation->title)) {
                    $conversation->title = now()->toDateString() . ' - ' . Str::limit($conversation->first_message ?? $queryText, 80);
                    $conversation->save();
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to save retry bot message: ' . $e->getMessage());
            }
        }

        return response()->json([
            'status' => 'retry',
            'fulfillmentText' => $retryText,
            'retryCount' => $strikes,
            'needs_clarification' => true
        ]);
    }

    // 🤖 If reasonable confidence, return direct answer
    if ($confidence > 0.6 || $intentName !== 'Default Fallback Intent') {
        Log::info('Returning direct answer', ['confidence' => $confidence, 'intent' => $intentName]);
        $this->resetRetryCount();

        Query::create([
            'queryID' => Str::uuid(),
            'employeeNum' => $employeeNum,
            'question' => $queryText,
            'response' => $fulfillmentText,
            'confidenceScore' => $confidence,
            'queryType' => 'Dialogflow',
            'questionTime' => $questionTimeFormatted,
            'responseTime' => \Carbon\Carbon::now()->format('Y-m-d H:i:s.u'),
            'isEscalated' => false,
            'handledBy' => 'Bot',
        ]);

        // Save bot response to conversation history if available
        if (!empty($conversation) && !empty($fulfillmentText)) {
            try {
                ChatMessage::create([
                    'ticket_no' => null,
                    'sender' => 'bot',
                    'message' => $fulfillmentText,
                    'conversation_id' => $conversation->id
                ]);

                if (empty($conversation->title)) {
                    $conversation->title = now()->toDateString() . ' - ' . Str::limit($conversation->first_message ?? $queryText, 80);
                    $conversation->save();
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to save dialogflow bot message: ' . $e->getMessage());
            }
        }

        return response()->json([
            'status' => 'success',
            'fulfillmentText' => $fulfillmentText,
            'confidence' => $confidence,
            'intent' => $intentName
        ]);
    }

} catch (\Exception $dialogflowError) {
    Log::error('❌ Dialogflow call failed completely', [
        'error' => $dialogflowError->getMessage(),
        'query' => $queryText
    ]);
    
    // Use conversational responses as fallback
    $conversationalResponse = $this->handleConversationalQueries($queryText);
    if ($conversationalResponse) {
        return $conversationalResponse;
    }
    
    // Generate a helpful response based on keywords
    $queryLower = strtolower($queryText);
    $fulfillmentText = "Thanks for your question! I want to make sure I understand correctly. Could you provide more details about '{$queryText}'?";
    
    if (strpos($queryLower, 'probation') !== false) {
        $fulfillmentText = "The probation period is typically 6 months with monthly performance reviews. After successful completion, you'll be regularized with full benefits.";
    } elseif (strpos($queryLower, 'flexible') !== false) {
        $fulfillmentText = "Yes, we offer flexible time arrangements including flexi-time, compressed workweeks, and remote work options. For specific details about eligibility and how to apply, please submit a Flexible Work Request Form through the HR portal.";
    } elseif (strpos($queryLower, 'salary') !== false) {
        $fulfillmentText = "Payday is on the 30th of each month. You can view your payslip in the Employee Portal under 'My Payslips'.";
    } elseif (strpos($queryLower, 'leave') !== false) {
        $fulfillmentText = "We offer 20 days annual leave, 15 days sick leave, and various special leaves. Apply through the HR portal with 2 weeks notice.";
    }
    
    // Return fallback response
    return response()->json([
        'status' => 'success',
        'fulfillmentText' => $fulfillmentText,
        'confidence' => 0.8,
        'intent' => 'fallback.response',
        'dialogflow_failed' => true
    ]);
}

// 🔄 Low confidence - start guided flow (only reached if no exception was thrown)
Log::info('Low confidence, starting guided flow', ['confidence' => $confidence]);
$this->resetRetryCount();
$reply = "I want to make sure I give you the right information. Let me guide you through our HR topics.";

if (!empty($conversation)) {
    try {
        ChatMessage::create([
            'ticket_no' => null,
            'sender' => 'bot',
            'message' => $reply,
            'conversation_id' => $conversation->id
        ]);

        if (empty($conversation->title)) {
            $conversation->title = now()->toDateString() . ' - ' . Str::limit($conversation->first_message ?? $queryText, 80);
            $conversation->save();
        }
    } catch (\Throwable $e) {
        Log::warning('Failed to save guided flow bot message: ' . $e->getMessage());
    }
}

return response()->json([
    'status' => 'guided_flow',
    'fulfillmentText' => $reply,
    'guided_flow' => true
]);

        } catch (\Throwable $e) {
            Log::error('❌ Dialogflow error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
                'exception_class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            // 🆕 FIXED: Better error response that doesn't break the frontend
            $fallbackReply = "Oops! Something unexpected happened on my end, and I apologize for that! 🙏 Don't worry though – I can still help! Would you like me to create a support ticket so our HR team can assist you personally? Or feel free to browse our guided topics – you might find exactly what you're looking for! 😊";
            // Ensure a conversation exists for this session so replies are persisted
            try {
                $sessionId = $request->input('sessionId') ?? session()->getId();
                $userId = Auth::id();

                $conv = null;
                if (class_exists(Conversation::class)) {
                    $conv = Conversation::where('session_id', $sessionId)
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if ($conv && empty($conv->user_id) && $userId) {
                        $conv->user_id = $userId;
                        $conv->save();
                    }

                    if (!$conv) {
                        $conv = Conversation::create([
                            'user_id' => $userId,
                            'session_id' => $sessionId,
                            'first_message' => $queryText ?? null,
                            'title' => null,
                        ]);
                    }
                }

                if ($conv) {
                    try {
                        ChatMessage::create([
                            'ticket_no' => null,
                            'sender' => 'bot',
                            'message' => $fallbackReply,
                            'conversation_id' => $conv->id
                        ]);

                        if (empty($conv->title)) {
                            $conv->title = now()->toDateString() . ' - ' . Str::limit($conv->first_message ?? $queryText, 80);
                            $conv->save();
                        }
                    } catch (\Throwable $e) {
                        Log::warning('Failed to save fallback bot message: ' . $e->getMessage());
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to ensure conversation for fallback message: ' . $e->getMessage());
            }

            return response()->json([
                'status' => 'success', // Use success to prevent frontend errors
                'fulfillmentText' => $fallbackReply,
                'fallback' => true
            ]);
        }
    }

    /**
     * 🆕 NEW: Check if question is HR-related
     */
    private function isHRRelatedQuestion(string $queryText): bool
    {
        $hrKeywords = [
            // Employment
            'hire', 'hiring', 'recruitment', 'recruit', 'job', 'position', 'role',
            'interview', 'application', 'resume', 'cv', 'candidate',
            'onboarding', 'orientation', 'probation', 'contract',
            
            // Compensation & Benefits
            'salary', 'pay', 'wage', 'compensation', 'benefit', 'insurance',
            'health insurance', 'dental', 'vision', 'retirement', 'pension',
            '401k', 'bonus', 'commission', 'raise', 'increase', 'promotion',
            
            // Time Off
            'vacation', 'holiday', 'leave', 'sick', 'time off', 'pto',
            'personal day', 'maternity', 'paternity', 'fmla',
            
            // Policies
            'policy', 'procedure', 'handbook', 'rule', 'regulation',
            'code of conduct', 'dress code', 'attendance', 'punctuality',
            
            // Performance
            'performance', 'review', 'appraisal', 'evaluation', 'kpi',
            'goal', 'objective', 'feedback', 'development', 'training',
            
            // Employee Relations
            'grievance', 'complaint', 'concern', 'issue', 'problem',
            'dispute', 'conflict', 'mediation', 'disciplinary', 'warning',
            
            // Workplace
            'workplace', 'environment', 'safety', 'harassment', 'discrimination',
            'diversity', 'inclusion', 'accommodation', 'remote', 'hybrid',
            
            // Separation
            'termination', 'fired', 'dismissal', 'resignation', 'quit',
            'exit', 'severance', 'layoff', 'redundancy'
        ];

        foreach ($hrKeywords as $keyword) {
            if (stripos($queryText, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * 🆕 NEW: Check if bot cannot answer the question properly
     */
    private function cantAnswerQuestion(float $confidence, string $intentName, string $fulfillmentText): bool
    {
        // Low confidence
        if ($confidence < 0.4) {
            return true;
        }

        // Default fallback intent
        if ($intentName === 'Default Fallback Intent') {
            return true;
        }

        // Generic or unclear responses
        $unclearResponses = [
            'i didn\'t understand',
            'can you rephrase',
            'could you clarify',
            'i\'m not sure',
            'can you try asking',
            'please rephrase',
            'not clear'
        ];

        foreach ($unclearResponses as $unclear) {
            if (stripos($fulfillmentText, $unclear) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * 🆕 NEW: Suggest HR escalation for HR-related questions bot can't answer
     */
    private function suggestHREscalation(string $queryText, $employeeNum, string $reason): \Illuminate\Http\JsonResponse
    {
        Log::info('Suggesting HR escalation', ['reason' => $reason]);

        // Persist pending escalation so an affirmative reply ("yes") from the user
        // will escalate the original query text instead of the short affirmation.
        try {
            Session::put('pending_escalation', [
                'query' => $queryText,
                'employeeNum' => $employeeNum,
                'reason' => $reason,
                'created_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::warning('Could not store pending_escalation in session: ' . $e->getMessage());
        }

        return response()->json([
            'status' => 'suggest_escalation',
            'fulfillmentText' => "I can see this is an important matter to you, and I want to make sure you get the best possible help! 💼 Our HR team has the expertise to give you a thorough and personalized response.<br><br>⚠️ Please note: Each conversation can only be escalated once. Would you like me to create a support ticket?",
            'suggest_hr' => true,
            'needs_confirmation' => true,
            'pending_escalation' => [
                'query' => $queryText,
                'employeeNum' => $employeeNum,
                'reason' => $reason
            ],
            'options' => [
                ['text' => '✅ Yes, please create a ticket', 'action' => 'escalate'],
                ['text' => '🔄 No, let me rephrase my question', 'action' => 'rephrase']
            ]
        ]);
    }

    /**
     * 🆕 NEW: Handle retry scenario (when user responds to retry prompt)
     */
    private function handleRetryScenario(string $queryText, $employeeNum, $conversation = null): ?\Illuminate\Http\JsonResponse
    {
        $retryCount = Session::get('retry_count', 0);
        
        if ($retryCount > 0) {
            // 🆕 Check if this conversation has already been escalated
            if ($this->isAlreadyEscalated($conversation)) {
                $existingTicket = $this->getExistingTicket($conversation);
                $ticketInfo = $existingTicket ? " Your existing ticket is: <strong>{$existingTicket}</strong>" : '';
                
                return response()->json([
                    'status' => 'already_escalated',
                    'fulfillmentText' => "I see that you've already created a support ticket for this conversation! 📋{$ticketInfo}<br><br>Our HR team is working on it. Each conversation can only be escalated once. Is there anything else I can help you with?",
                    'already_escalated' => true
                ]);
            }

            // If user previously confirmed escalation, but we need more details
            $pendingEscalation = Session::get('pending_escalation');
            if ($pendingEscalation && isset($pendingEscalation['awaiting_clarification']) && $pendingEscalation['awaiting_clarification'] === true) {
                // User's current message is the clarification
                $clarification = trim($queryText);
                if (mb_strlen($clarification) < 10) {
                    return response()->json([
                        'status' => 'need_more_clarity',
                        'fulfillmentText' => 'To help HR assist you better, please provide a bit more detail (at least 10 characters) about your issue.'
                    ]);
                }
                // Escalate with the clarified message
                $originalQuery = $pendingEscalation['query'] ?? '';
                $originalConfidence = $pendingEscalation['confidence'] ?? 0.0;
                Log::info('✅ Escalating with user-provided clarification', [
                    'original_query' => $originalQuery,
                    'clarification' => $clarification,
                    'original_confidence' => $originalConfidence
                ]);
                Session::forget('pending_escalation');
                return $this->escalateToHR(
                    $clarification,
                    $employeeNum,
                    "User provided clarification after escalation confirmation.",
                    $originalConfidence,
                    $conversation ? $conversation->id : null
                );
            }
            // Check if user wants to escalate
            if ($this->wantsEscalation($queryText)) {
                // Instead of escalating immediately, prompt for more details
                Session::put('pending_escalation', array_merge(Session::get('pending_escalation', []), [
                    'awaiting_clarification' => true
                ]));
                return response()->json([
                    'status' => 'ask_for_clarity',
                    'fulfillmentText' => 'Before I escalate this to the HR team, could you please provide a bit more detail about your issue?'
                ]);
            }

            // Check if user wants to rephrase
            if ($this->wantsToRephrase($queryText)) {
                $this->resetRetryCount();
                Session::forget('pending_escalation');
                return response()->json([
                    'status' => 'retry_reset',
                    'fulfillmentText' => "Okay, please ask your question in a different way and I'll do my best to help!"
                ]);
            }
        }

        return null;
    }

    /**
     * 🆕 NEW: Check if user wants escalation
     */
    private function wantsEscalation(string $queryText): bool
    {
        $escalationPatterns = [
            '/\b(yes|yeah|sure|okay|please|escalate|hr)\b/i',
            '/\bgo ahead\b/i',
            '/\bcontact hr\b/i',
            '/\bsend to hr\b/i',
            '/\bhuman help\b/i'
        ];

        foreach ($escalationPatterns as $pattern) {
            if (preg_match($pattern, $queryText)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 🆕 NEW: Check if user wants to rephrase
     */
    private function wantsToRephrase(string $queryText): bool
    {
        $rephrasePatterns = [
            '/\b(no|nope|nevermind)\b/i',
            '/\brephrase\b/i',
            '/\bdifferent\b/i',
            '/\btry again\b/i',
            '/\bnew question\b/i',
            '/\bstart over\b/i'
        ];

        foreach ($rephrasePatterns as $pattern) {
            if (preg_match($pattern, $queryText)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 🆕 NEW: Check if we should retry (low confidence responses)
     */
    private function shouldRetry(float $confidence, string $intentName): bool
    {
        if ($confidence < 0.5) {
            return true;
        }

        if ($intentName === 'Default Fallback Intent') {
            return true;
        }

        return false;
    }

    /**
     * 🆕 NEW: Get appropriate retry message based on retry count
     */
    private function getRetryMessage(int $retryCount): string
    {
        $messages = [
            1 => "Thank you for reaching out! 😊 I want to make sure I understand you correctly. Could you tell me a bit more about what you're looking for? Feel free to share any details that might help me assist you better.",
            2 => "I appreciate your patience! 🙏 I'm still trying to get a clear picture of how I can help. Would you mind rephrasing your question or giving me some additional context? I'm here to help in any way I can.",
            3 => "Thank you for sticking with me! 💪 I really want to make sure you get the help you need. Could you share a few more details about your situation? The more context you provide, the better I can assist you."
        ];

        return $messages[$retryCount] ?? $messages[1];
    }

    /**
     * 🆕 NEW: Offer HR escalation after max retries
     */
    private function offerHREscalation(string $queryText, $employeeNum, float $confidence = 0.0): \Illuminate\Http\JsonResponse
    {
        Log::info('Offering HR escalation after max retries', [
            'query' => $queryText,
            'confidence' => $confidence
        ]);

        // Store escalation details in session for when user confirms
        Session::put('pending_escalation', [
            'query' => $queryText,
            'employeeNum' => $employeeNum,
            'confidence' => $confidence,
            'reason' => 'Max retries reached',
            'timestamp' => now()->toIso8601String()
        ]);

        // Persist pending escalation so an affirmative reply ("yes") will use
        // this original query when escalating.
        try {
            Session::put('pending_escalation', [
                'query' => $queryText,
                'employeeNum' => $employeeNum,
                'reason' => 'Max retries reached',
                'created_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::warning('Could not store pending_escalation in session: ' . $e->getMessage());
        }

        return response()->json([
            'status' => 'offer_escalation',
            'fulfillmentText' => "I truly appreciate your patience with me! 🙏 It seems like your question might need a more personalized touch. Our HR team would be happy to help you directly.<br><br>⚠️ Please note: Each conversation can only be escalated once. Would you like me to create a support ticket for you?",
            'max_retries_reached' => true,
            'needs_confirmation' => true,
            'options' => [
                ['text' => '✅ Yes, please create a ticket', 'action' => 'escalate'],
                ['text' => '🔄 Let me try asking differently', 'action' => 'rephrase'],
                ['text' => '❌ Cancel and start over', 'action' => 'cancel']
            ]
        ]);
    }

    /**
     * 🆕 NEW: Increment retry count
     */
    private function incrementRetryCount(): int
    {
        $count = Session::get('retry_count', 0) + 1;
        Session::put('retry_count', $count);
        Session::put('last_retry_time', now());
        return $count;
    }

    /**
     * 🆕 NEW: Reset retry count
     */
    private function resetRetryCount(): void
    {
        Session::forget('retry_count');
        Session::forget('last_retry_time');
    }

    /**
     * 🆕 NEW: Check if a conversation has already been escalated
     */
    private function isAlreadyEscalated($conversation): bool
    {
        if (!$conversation) {
            return false;
        }

        try {
            // Check if any ticket exists for this conversation
            $existingTicket = ChatMessage::where('conversation_id', $conversation->id)
                ->whereNotNull('ticket_no')
                ->first();

            if ($existingTicket) {
                Log::info('Found existing ticket for conversation', [
                    'conversation_id' => $conversation->id,
                    'ticket_no' => $existingTicket->ticket_no
                ]);
                return true;
            }

            // Also check session flag
            $sessionKey = 'escalated_conversation_' . $conversation->id;
            if (Session::has($sessionKey)) {
                return true;
            }

            return false;
        } catch (\Throwable $e) {
            Log::warning('Error checking escalation status: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 🆕 NEW: Get existing ticket number for a conversation
     */
    private function getExistingTicket($conversation): ?string
    {
        if (!$conversation) {
            return null;
        }

        try {
            $existingMessage = ChatMessage::where('conversation_id', $conversation->id)
                ->whereNotNull('ticket_no')
                ->first();

            return $existingMessage ? $existingMessage->ticket_no : null;
        } catch (\Throwable $e) {
            Log::warning('Error getting existing ticket: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * 🆕 FIXED: Extract query text from multiple possible input formats
     */
    private function extractQueryText(Request $request): string
    {
        // Format 1: Direct message parameter (from our frontend)
        if ($request->has('message')) {
            return trim($request->input('message'));
        }

        // Format 2: Dialogflow webhook format
        if ($request->has('queryResult.queryText')) {
            return trim($request->input('queryResult.queryText'));
        }

        // Format 3: Alternative Dialogflow format
        if ($request->has('queryResult.parameters.message')) {
            return trim($request->input('queryResult.parameters.message'));
        }

        // Format 4: Raw text in request
        $text = $request->input('text') ?? $request->input('query') ?? '';
        return trim($text);
    }

    /**
     * 🆕 IMPROVED: Better greeting detection
     */
    private function isGreeting(string $queryText): bool
    {
        $greetingPatterns = [
            '/^(hi|hello|hey|greetings|good morning|good afternoon|good evening)[\s\.,!]*$/i',
            '/^start$/i',
            '/^help$/i',
            '/^\?$/',
            '/^hello there$/i',
            '/^hi there$/i'
        ];

        foreach ($greetingPatterns as $pattern) {
            if (preg_match($pattern, $queryText)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 🔥 IMPROVED: Better detection for escalation requests
     */
    private function isEscalationRequest(string $queryText): bool
    {
        $escalationPatterns = [
            '/\b(escalate|human|real person|live agent|representative|manager)\b/i',
            '/\btalk to (hr|human|person|someone|agent|manager)\b/i',
            '/\bspeak with (hr|human|person|someone|agent|manager)\b/i',
            '/\bconnect with (hr|human|person|someone|agent)\b/i',
            '/\bI want to talk to\b/i',
            '/\bI need to speak with\b/i',
            '/\bcan I speak with\b/i',
            '/\bcontact hr\b/i',
            '/\bget me hr\b/i',
            '/\bnot a bot\b/i',
            '/\bnot chatbot\b/i',
            '/\breal human\b/i',
            '/\blive person\b/i',
            '/\bhuman help\b/i'
        ];

        foreach ($escalationPatterns as $pattern) {
            if (preg_match($pattern, $queryText)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 🆕 NEW: Handle simple conversational queries without Dialogflow
     */
    private function handleConversationalQueries(string $queryText): ?\Illuminate\Http\JsonResponse
    {
        $conversationalMap = [
            'thanks' => [
                "You're so welcome! 😊 It's my pleasure to help. Is there anything else on your mind? I'm here for you!",
                "I'm glad I could help! 💙 Please don't hesitate to reach out anytime you have questions.",
                "Thank you for your kind words! 🌟 Feel free to ask me anything else – I'm always happy to assist!"
            ],
            'thank you' => [
                "You're absolutely welcome! 😊 Helping you is what I'm here for. Is there anything else I can do for you today?",
                "It's my pleasure! 💙 I hope I was able to help. Let me know if there's anything more you'd like to discuss.",
                "You're very welcome! 🌟 I'm always here if you need any more assistance. Don't be a stranger!"
            ],
            'bye' => [
                "Take care and have a wonderful day! 👋 Remember, I'm always here whenever you need help with anything.",
                "Goodbye for now! 🌟 Wishing you all the best. Feel free to come back anytime!",
                "See you next time! 👋💙 Take care of yourself, and don't hesitate to reach out if you need anything."
            ],
            'goodbye' => [
                "Goodbye! 👋 It was lovely chatting with you. Have a fantastic day ahead!",
                "Take care! 🌟 I hope everything goes smoothly for you. See you next time!",
                "Farewell for now! 💙 Remember, I'm just a message away if you ever need assistance."
            ],
            'how are you' => [
                "I'm doing wonderfully, thank you for asking! 😊 It's so thoughtful of you. How can I brighten your day today?",
                "I'm great, thanks! 💙 I'm all set and ready to help you with whatever you need. What's on your mind?",
                "Feeling fantastic and ready to assist! 🌟 How are you doing? What can I help you with today?"
            ],
            'who are you' => [
                "Hello! I'm Aihra, your friendly AI HR Assistant! 🤖💙 I'm here to help you navigate HR matters, answer your questions, and make sure you get the support you need. What can I help you with today?",
                "I'm Aihra! 🌟 Think of me as your helpful HR companion – I'm here to answer questions, provide guidance, and connect you with the right people when needed. How can I assist you?",
                "Nice to meet you! I'm Aihra, your AI-powered HR buddy! 😊 I'm here to help with any HR-related questions or concerns you might have. Feel free to ask me anything!"
            ],
            'hello' => [
                "Hello there! 👋 It's wonderful to hear from you! How can I brighten your day today?",
                "Hi! 😊 Welcome! I'm so glad you reached out. What can I help you with?",
                "Hey there! 🌟 Great to see you! I'm here and ready to help with whatever you need."
            ],
            'hi' => [
                "Hi there! 😊 Welcome! I'm here to help you with any questions or concerns you might have.",
                "Hello! 👋 It's great to hear from you! What can I assist you with today?",
                "Hey! 🌟 Thanks for reaching out! I'm all ears – what would you like to know?"
            ],
            'good morning' => [
                "Good morning! ☀️ I hope you're having a wonderful start to your day! How can I help you today?",
                "Morning! 🌅 What a great day to get things done! What can I assist you with?",
                "Good morning to you! ☀️ Ready to tackle the day together? What's on your mind?"
            ],
            'good afternoon' => [
                "Good afternoon! 🌤️ I hope your day is going well! How can I be of service?",
                "Afternoon! 😊 Great to hear from you! What can I help you with today?",
                "Good afternoon! 🌻 Hope you're having a productive day! What brings you here?"
            ],
            'good evening' => [
                "Good evening! 🌙 I hope you had a great day! How can I help you tonight?",
                "Evening! 🌆 Thanks for reaching out! What can I assist you with?",
                "Good evening to you! 🌙 I'm here if you need any help or have any questions."
            ],
        ];

        foreach ($conversationalMap as $pattern => $responses) {
            if (stripos($queryText, $pattern) !== false) {
                $response = $responses[array_rand($responses)];
                return response()->json([
                    'status' => 'success',
                    'fulfillmentText' => $response
                ]);
            }
        }

        return null;
    }

    /**
     * 🔥 IMPROVED: Better escalation logic
     */
    private function shouldEscalate(string $queryText, float $confidence, string $intentName): bool
    {
        // Always escalate if it's the default fallback intent with low confidence
        if ($intentName === 'Default Fallback Intent' && $confidence < 0.4) {
            return true;
        }

        // Escalate if confidence is very low
        if ($confidence < 0.3) {
            return true;
        }

        // Escalate for urgent/emotional keywords ONLY when it's clear user needs help
        // Removed generic words like "emergency", "problem", "issue" to avoid false positives
        // when user is asking informational questions (e.g., "what is the procedure for emergency leave?")
        $urgentKeywords = [
            'harassment', 'discriminat', 'bullying', 'unsafe', 'danger',
            'threatened', 'assault', 'abuse', 'wrongful termination'
        ];

        foreach ($urgentKeywords as $keyword) {
            if (stripos($queryText, $keyword) !== false) {
                return true;
            }
        }

        // Check for action-oriented urgent phrases (these indicate user needs immediate help)
        $urgentActionPhrases = [
            'urgent help', 'help me now', 'need help asap', 'help immediately',
            'this is urgent', 'serious issue', 'critical issue',
            'not working and', 'broken and need', 'error and can\'t'
        ];

        foreach ($urgentActionPhrases as $phrase) {
            if (stripos($queryText, $phrase) !== false) {
                return true;
            }
        }

        // Check for complaint contexts (user reporting actual problems, not asking about procedures)
        $complaintIndicators = [
            'I have a complaint', 'I\'m filing a complaint', 'want to complain about',
            'I\'m upset about', 'I\'m angry about', 'frustrated with',
            'this is wrong', 'this is unfair', 'being treated unfairly'
        ];

        foreach ($complaintIndicators as $indicator) {
            if (stripos($queryText, $indicator) !== false) {
                return true;
            }
        }

        // Escalate complex personal issues
        $personalIssues = [
            'salary', 'pay', 'raise', 'promotion', 'disciplinary', 'warning',
            'termination', 'fired', 'resign', 'quit', 'legal', 'lawyer',
            'contract', 'agreement', 'confidential', 'private', 'personal'
        ];

        $personalCount = 0;
        foreach ($personalIssues as $issue) {
            if (stripos($queryText, $issue) !== false) {
                $personalCount++;
            }
        }

        // If multiple personal issues mentioned, escalate
        if ($personalCount >= 2) {
            return true;
        }

        return false;
    }

    /**
     * 🔥 FIXED: Escalate query to HR inbox - ALWAYS creates real ticket
     */
    private function escalateToHR(string $queryText, $employeeNum, string $reason = 'User requested', float $originalConfidence = null, $conversationId = null): \Illuminate\Http\JsonResponse
    {
        $ticketNo = null;
        
        try {
            Log::info("🎯 Starting escalation process...", [
                'reason' => $reason,
                'employee' => $employeeNum,
                'query' => substr($queryText, 0, 100),
                'original_confidence' => $originalConfidence
            ]);

            // Generate unique ticket number FIRST
            $ticketNo = 'TKT-' . strtoupper(Str::random(8)) . '-' . time();
            Log::info("🎯 Generated ticket: " . $ticketNo);

            // 🎯 FIXED: Determine priority based on content AND original confidence
            $priority = $this->determinePriority($queryText, $originalConfidence);
            $category = $this->determineCategory($queryText);
            
            Log::info('🎯 Ticket Priority Determined', [
                'query' => substr($queryText, 0, 100),
                'priority' => $priority,
                'confidence' => $originalConfidence,
                'category' => $category
            ]);
            
            // Calculate response and resolution deadlines based on priority
            $deadlines = $this->calculateDeadlines($priority);

            // 🆕 CRITICAL FIX: Create HR inbox ticket with multiple fallback attempts
            $inbox = null;
            // Get random active HR staff to assign ticket (not deactivated or archived)
            $activeHR = DB::table('users')
                ->where('role', 'HR')
                ->where('status', 'Active')
                ->where('is_archived', 0)
                ->pluck('employeeNum')
                ->toArray();
            
            $assignedTo = !empty($activeHR) ? $activeHR[array_rand($activeHR)] : null;
            
            $maxAttempts = 3;
            
            for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
                try {
                    $inbox = HrInbox::create([
                        'ticket_no' => $ticketNo,
                        'from_user' => $employeeNum ?: 'GUEST',
                        'message' => $queryText,
                        'status' => 'Open',
                        'priority' => strtolower($priority),
                        'category' => $category,
                        'intent' => substr('Escalated: ' . $reason, 0, 50),
                        'confidence' => $originalConfidence ?? 0.0,
                        'assigned_to' => $assignedTo,
                        'response_deadline' => $deadlines['response_deadline'],
                        'resolution_deadline' => $deadlines['resolution_deadline'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    Log::info("✅ HR Inbox created successfully on attempt {$attempt}", ['ticket_no' => $ticketNo]);
                    
                    // Send email notification to assigned HR
                    if ($assignedTo) {
                        $this->sendTicketAssignmentEmail($assignedTo, $ticketNo, $queryText, $priority, $employeeNum);
                    }
                    
                    break; // Success, break out of retry loop
                    
                } catch (\Exception $createError) {
                    Log::warning("❌ HR Inbox creation failed on attempt {$attempt}: " . $createError->getMessage());
                    
                    if ($attempt === $maxAttempts) {
                        // Last attempt failed, try alternative creation method
                        $inbox = $this->createTicketAlternativeMethod($ticketNo, $employeeNum, $queryText, $priority, $category, $reason, $originalConfidence);
                        if ($inbox) {
                            Log::info("✅ HR Inbox created via alternative method", ['ticket_no' => $ticketNo]);
                            break;
                        }
                        throw new \Exception("Failed to create HR ticket after {$maxAttempts} attempts: " . $createError->getMessage());
                    }
                    
                    // Wait briefly before retry
                    usleep(500000); // 0.5 seconds
                }
            }

            // Create query record with proper error handling
            try {
                Query::create([
                    'queryID' => Str::uuid(),
                    'employeeNum' => $employeeNum,
                    'question' => $queryText,
                    'response' => 'Escalated to HR - ' . $ticketNo,
                    'confidenceScore' => 0.0,
                    'queryType' => 'Escalated',
                    'questionTime' => $questionTimeFormatted,
                    'responseTime' => \Carbon\Carbon::now()->format('Y-m-d H:i:s.u'),
                    'isEscalated' => true,
                    'handledBy' => 'HR',
                ]);
                Log::info("✅ Query record created successfully");
            } catch (\Exception $queryError) {
                Log::warning('Query record creation failed, but HR ticket was created', [
                    'error' => $queryError->getMessage(),
                    'ticket_no' => $ticketNo
                ]);
                // Continue even if query record fails - the main ticket is what matters
            }

            Log::info("✅ Escalation completed successfully", ['ticket_no' => $ticketNo]);

            // Get response time expectation based on priority
            $responseTimeInfo = $this->getResponseTimeInfo($priority);
            
            // Create empathetic message based on priority
            $empathyMessage = $this->getEmpathyMessage($priority);
            
            $fulfillmentMessage = "✅ {$empathyMessage}<br><br>" . 
                                   "I've connected you with our HR team who can better assist you. Your ticket number is: <strong>{$ticketNo}</strong><br><br>" . 
                                   "⏱️ You can expect a response within <strong>{$responseTimeInfo['initialResponse']}</strong>.<br><br>" . 
                                   $responseTimeInfo['closingMessage'];
            
            // Save bot response to chat history
            if ($conversationId) {
                try {
                    ChatMessage::create([
                        'ticket_no' => $ticketNo,
                        'sender' => 'bot',
                        'message' => $fulfillmentMessage,
                        'conversation_id' => $conversationId
                    ]);
                    Log::info("✅ Escalation message saved to chat history", ['conversation_id' => $conversationId]);
                    
                    // 🆕 Mark this conversation as escalated to prevent re-escalation
                    Session::put('escalated_conversation_' . $conversationId, [
                        'ticket_no' => $ticketNo,
                        'escalated_at' => now()->toIso8601String()
                    ]);
                } catch (\Exception $chatError) {
                    Log::warning('Failed to save escalation message to chat history: ' . $chatError->getMessage());
                }
            }
            
            return response()->json([
                'status' => 'escalated',
                'fulfillmentText' => $fulfillmentMessage,
                'ticket_no' => $ticketNo,
                'escalated' => true,
                'priority' => $priority,
                'response_time' => $responseTimeInfo,
                'conversation_id' => $conversationId
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Escalation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'query' => $queryText
            ]);

            // 🆕 CRITICAL FIX: ALWAYS create a ticket, even if basic methods fail
            $finalTicketNo = $this->ensureTicketCreation($ticketNo, $employeeNum, $queryText, $reason, $originalConfidence);
            
            // Get response time info (use Medium as default for fallback)
            $priority = $this->determinePriority($queryText, $originalConfidence);
            $responseTimeInfo = $this->getResponseTimeInfo($priority);
            $empathyMessage = $this->getEmpathyMessage($priority);
            
            $fulfillmentMessage = "✅ {$empathyMessage}<br><br>" . 
                                   "I've successfully created a support ticket for you. Your ticket number is: <strong>{$finalTicketNo}</strong><br><br>" . 
                                   "⏱️ You can expect a response within <strong>{$responseTimeInfo['initialResponse']}</strong>.<br><br>" . 
                                   $responseTimeInfo['closingMessage'];
            
            // Save bot response to chat history
            if ($conversationId) {
                try {
                    ChatMessage::create([
                        'ticket_no' => $finalTicketNo,
                        'sender' => 'bot',
                        'message' => $fulfillmentMessage,
                        'conversation_id' => $conversationId
                    ]);
                    Log::info("✅ Fallback escalation message saved to chat history", ['conversation_id' => $conversationId]);
                } catch (\Exception $chatError) {
                    Log::warning('Failed to save fallback escalation message: ' . $chatError->getMessage());
                }
            }
            
            return response()->json([
                'status' => 'escalated',
                'fulfillmentText' => $fulfillmentMessage,
                'ticket_no' => $finalTicketNo,
                'escalated' => true,
                'fallback_created' => true,
                'priority' => $priority,
                'response_time' => $responseTimeInfo,
                'conversation_id' => $conversationId
            ]);
        }
    }

    /**
     * 🆕 NEW: Determine ticket priority based on content and confidence
     * Priority levels: Urgent, High, Medium, Low
     * 
     * Categorization Logic:
     * - Low (>85%): General requests, policy documents, forms, basic inquiries
     * - Medium (70-85%): Leave credits, benefits policies, promotion criteria, training
     * - High (50-70%): Salary discrepancies, unpaid benefits, urgent benefit claims, promotion disputes
     * - Urgent (<50%): Harassment, discrimination, wrongful termination, legal/safety concerns
     */
    private function determinePriority(string $queryText, float $confidence = null): string
    {
        // HR-related keywords for all priorities
        $urgentKeywords = [
            'harass', 'discriminat', 'wrongful termination', 'fired unfairly',
            'legal action', 'lawyer', 'sue', 'court', 'police',
            'unsafe', 'danger', 'threat', 'violence', 'assault',
            'suicide', 'self-harm', 'abuse', 'safety concern', 'bully', 'bullied',
            'emergency', 'urgent', 'immediate attention', 'life-threatening', 'crisis', 'critical situation'
        ];
        $highPriorityKeywords = [
            'salary discrepancy', 'not paid', 'unpaid', 'missing pay', 'wrong salary',
            'benefit claim', 'urgent benefit', 'benefit denied', 'benefit issue',
            'promotion dispute', 'ranking dispute', 'demotion', 'unfair ranking',
            'compensation issue', 'payroll error'
        ];
        $mediumPriorityKeywords = [
            'leave credit', 'vacation leave', 'sick leave', 'leave balance',
            'benefit polic', 'insurance polic', 'health benefit',
            'promotion criteria', 'ranking schedule', 'promotion process',
            'training opportunit', 'employee development', 'career development',
            'performance review'
        ];
        $allKeywords = array_merge($urgentKeywords, $highPriorityKeywords, $mediumPriorityKeywords);

        // If the message does not contain any HR-related keywords, always assign Low
        $hasKeyword = false;
        foreach ($allKeywords as $keyword) {
            if (stripos($queryText, $keyword) !== false) {
                $hasKeyword = true;
                break;
            }
        }
        if (!$hasKeyword) {
            Log::info('No HR keywords detected, assigning Low priority', ['query' => substr($queryText, 0, 50)]);
            return 'Low';
        }

        // Check critical keywords first (always Urgent regardless of confidence)
        foreach ($urgentKeywords as $keyword) {
            if (stripos($queryText, $keyword) !== false) {
                Log::info('🚨 Urgent keyword detected', ['keyword' => $keyword, 'query' => substr($queryText, 0, 50)]);
                return 'Urgent';
            }
        }
        foreach ($highPriorityKeywords as $keyword) {
            if (stripos($queryText, $keyword) !== false) {
                Log::info('⚠️ High priority keyword detected', ['keyword' => $keyword, 'query' => substr($queryText, 0, 50)]);
                return 'High';
            }
        }
        foreach ($mediumPriorityKeywords as $keyword) {
            if (stripos($queryText, $keyword) !== false) {
                Log::info('📋 Medium priority keyword detected', ['keyword' => $keyword, 'query' => substr($queryText, 0, 50)]);
                return 'Medium';
            }
        }
        // Use confidence-based categorization (should not be reached if keywords are present, but fallback just in case)
        if ($confidence !== null) {
            Log::info('📊 Using confidence-based priority', ['confidence' => $confidence]);
            if ($confidence < 0.50) {
                Log::info('Priority: Urgent (confidence < 50%)');
                return 'Urgent';
            } elseif ($confidence < 0.70) {
                Log::info('Priority: High (confidence 50-70%)');
                return 'High';
            } elseif ($confidence < 0.85) {
                Log::info('Priority: Medium (confidence 70-85%)');
                return 'Medium';
            } else {
                Log::info('Priority: Low (confidence > 85%)');
                return 'Low';
            }
        }
        Log::info('Priority: Low (default - no confidence data)');
        return 'Low';
    }

    /**
     * 🆕 NEW: Get response time information based on ticket priority
     * This sets customer expectations about when they'll hear back
     */
    private function getResponseTimeInfo(string $priority): array
    {
        $timeframes = [
            'Urgent' => [
                'initialResponse' => '15-30 minutes',
                'resolution' => '2-4 hours',
                'description' => 'Critical issues requiring immediate attention',
                'closingMessage' => 'Our HR team is treating this as urgent and will reach out to you very soon. You\'re not alone in this. 💙'
            ],
            'High' => [
                'initialResponse' => '1 hour',
                'resolution' => '4-8 hours',
                'description' => 'Important issues that need prompt attention',
                'closingMessage' => 'Our HR team will prioritize your concern and get back to you shortly. We\'re here to help! 🤝'
            ],
            'Medium' => [
                'initialResponse' => '8 hours',
                'resolution' => '24-48 hours',
                'description' => 'Standard inquiries with normal processing time',
                'closingMessage' => 'Our HR team will review your query and respond within the timeframe above. Thank you for your patience! 😊'
            ],
            'Low' => [
                'initialResponse' => '24 hours',
                'resolution' => '72 hours',
                'description' => 'General inquiries with standard response time',
                'closingMessage' => 'Our HR team will get back to you as soon as possible. We appreciate your understanding! 🙏'
            ]
        ];

        return $timeframes[$priority] ?? $timeframes['Medium'];
    }

    /**
     * 🆕 NEW: Get empathetic opening message based on priority
     * Adds human touch to automated responses
     */
    private function getEmpathyMessage(string $priority): string
    {
        $messages = [
            'Urgent' => 'I understand this is urgent and important to you. Let me get you the help you need right away.',
            'High' => 'Thank you for reaching out. I can see this is important, and I want to make sure you get the right support.',
            'Medium' => 'I appreciate you sharing this with me. Let me connect you with our HR team who can help you with this.',
            'Low' => 'Thank you for your question! I\'m happy to connect you with our HR team for assistance.'
        ];

        return $messages[$priority] ?? $messages['Medium'];
    }

    /**
     * Calculate response and resolution deadlines based on priority
     */
    private function calculateDeadlines(string $priority): array
    {
        $now = now();
        
        switch ($priority) {
            case 'Urgent':
                return [
                    'response_deadline' => $now->copy()->addMinutes(30),
                    'resolution_deadline' => $now->copy()->addHours(4)
                ];
            case 'High':
                return [
                    'response_deadline' => $now->copy()->addHour(),
                    'resolution_deadline' => $now->copy()->addHours(8)
                ];
            case 'Medium':
                return [
                    'response_deadline' => $now->copy()->addHours(8),
                    'resolution_deadline' => $now->copy()->addHours(48)
                ];
            case 'Low':
            default:
                return [
                    'response_deadline' => $now->copy()->addHours(24),
                    'resolution_deadline' => $now->copy()->addHours(72)
                ];
        }
    }

    /**
     * 🆕 NEW: Determine ticket category based on content
     */
    private function determineCategory(string $queryText): string
    {
        $categories = [
            'Conditions on Employment' => [
                // Core employment terms
                'contract', 'probation', 'probationary', 'regularization', 'regular', 'permanent', 'temporary', 'casual', 'project-based', 'fixed-term', 'contractual', 'seasonal', 'apprentice', 'intern', 'internship', 'ojt', 'trainee',
                // Time and schedule
                'working hours', 'work hours', 'shift', 'schedule', 'flexi', 'flexible', 'time in', 'time out', 'clock in', 'clock out', 'biometrics', 'bundy', 'timekeeping', 'work schedule', 'night shift', 'graveyard', 'morning shift', 'mid shift', 'rotating', 'fixed schedule',
                // Attendance
                'attendance', 'absent', 'absences', 'tardiness', 'late', 'undertime', 'awol', 'no show', 'no-show', 'present', 'presence', 'punctual', 'punctuality', 'habitual', 'consecutive',
                // Overtime and rest
                'overtime', 'ot', 'rest day', 'day off', 'holiday', 'weekend', 'legal holiday', 'special holiday', 'regular holiday', 'non-working',
                // Separation
                'termination', 'terminate', 'resign', 'resignation', 'end of contract', 'dismissal', 'fired', 'fire', 'layoff', 'retrenchment', 'redundancy', 'separation', 'clearance', 'exit interview', 'exit', 'last day', 'notice period', 'immediate resignation', 'voluntary', 'involuntary', 'constructive dismissal', 'just cause', 'authorized cause',
                // Retirement
                'retirement', 'retire', 'retiring', 'pension', 'optional retirement', 'compulsory retirement', 'early retirement', 'retirement age', 'retirement plan', 'retirement benefit',
                // Policies and rules
                'leave policy', 'company policy', 'policy', 'policies', 'rules', 'rule', 'regulation', 'regulations', 'handbook', 'manual', 'code of conduct', 'guidelines', 'procedure', 'procedures', 'standard', 'standards', 'protocol', 'protocols', 'sop', 'house rules',
                // Employment details
                'condition', 'conditions', 'employment', 'employ', 'employee', 'employer', 'job offer', 'offer letter', 'appointment', 'job description', 'jd', 'scope of work', 'sow', 'terms of employment', 'employment status', 'status', 'work arrangement', 'arrangement',
                // Duties
                'duties', 'duty', 'responsibilities', 'responsibility', 'task', 'tasks', 'assignment', 'assignments', 'workload', 'deliverables', 'output', 'outputs', 'scope', 'function', 'functions', 'accountabilities',
                // Transfer and location
                'transfer', 'transfers', 'relocation', 'relocate', 'workplace', 'work from home', 'wfh', 'remote', 'hybrid', 'onsite', 'on-site', 'office', 'location', 'deployment', 'deploy', 'site', 'branch', 'area', 'region', 'provincial', 'metro', 'head office', 'main office', 'satellite', 'field', 'reassignment', 'secondment', 'detachment',
                // Compliance
                'compliance', 'labor', 'dole', 'nlrc', 'due process', 'notice', 'memo', 'memorandum', 'violation', 'infraction', 'offense', 'discipline', 'disciplinary', 'suspension', 'suspend', 'warning', 'written warning', 'verbal warning', 'final warning', 'nte', 'notice to explain', 'show cause', 'admin case', 'administrative case', 'investigation', 'hearing', 'preventive suspension',
                // Work environment
                'harassment', 'bullying', 'discrimination', 'hostile', 'toxic', 'unsafe', 'safety', 'occupational', 'osha', 'accident', 'injury', 'incident', 'hazard', 'ppe', 'security', 'workplace violence', 'threat', 'grievance', 'complaint', 'dispute', 'conflict', 'issue', 'problem', 'concern'
            ],
            'Compensation and Benefits' => [
                // Salary terms
                'salary', 'salaries', 'pay', 'paid', 'payment', 'wage', 'wages', 'compensation', 'remuneration', 'earnings', 'income', 'minimum wage', 'basic pay', 'basic salary', 'daily rate', 'hourly rate', 'monthly rate', 'annual salary', 'package', 'total compensation',
                // Bonuses and extras
                'bonus', 'bonuses', 'allowance', 'allowances', 'overtime pay', 'ot pay', 'holiday pay', 'premium pay', 'night differential', 'nd', '13th month', 'thirteenth month', '14th month', 'mid-year bonus', 'christmas bonus', 'performance bonus', 'signing bonus', 'retention bonus', 'project bonus', 'spot bonus', 'quarterly bonus', 'annual bonus', 'year-end bonus',
                // Incentives
                'incentive', 'incentives', 'commission', 'commissions', 'profit sharing', 'rice subsidy', 'meal allowance', 'transpo', 'transportation', 'gas allowance', 'clothing allowance', 'communication allowance', 'cellphone allowance', 'internet allowance', 'data allowance', 'representation allowance', 'travel allowance', 'per diem', 'living allowance', 'housing allowance', 'hardship allowance', 'hazard pay', 'night pay',
                // Deductions
                'deduction', 'deductions', 'withholding', 'tax', 'taxes', 'bir', 'income tax', 'withholding tax', 'annual itr', 'tax refund', 'tax exempt', 'taxable', 'non-taxable', 'de minimis',
                // Government mandatories
                'sss', 'philhealth', 'pagibig', 'pag-ibig', 'hdmf', 'gsis', 'contribution', 'contributions', 'mandatory', 'government', 'statutory', 'sss loan', 'pagibig loan', 'salary deduction', 'premium',
                // Insurance and health
                'insurance', 'life insurance', 'accident insurance', 'benefit', 'benefits', 'health', 'healthcare', 'medical', 'medicine', 'dental', 'optical', 'hmo', 'hospitalization', 'checkup', 'check-up', 'clinic', 'wellness', 'annual physical', 'ape', 'dependent', 'dependents', 'coverage', 'group insurance', 'term life', 'critical illness', 'disability', 'outpatient', 'inpatient', 'emergency', 'surgery', 'consultation', 'lab', 'laboratory', 'xray', 'x-ray', 'ultrasound', 'prescription', 'pharmacy', 'drug', 'hospital', 'confinement',
                // Claims
                'reimbursement', 'reimburse', 'claim', 'claims', 'expense', 'expenses', 'receipt', 'receipts', 'liquidation', 'liquidate', 'or', 'official receipt', 'billing', 'invoice', 'petty cash', 'cash voucher', 'replenishment',
                // Loans
                'loan', 'loans', 'advance', 'advances', 'cash advance', 'salary loan', 'emergency loan', 'calamity loan', 'multi-purpose loan', 'company loan', 'employee loan', 'housing loan', 'car loan', 'personal loan', 'loan balance', 'loan deduction', 'amortization', 'interest', 'principal',
                // Payroll
                'payroll', 'payslip', 'pay slip', 'payday', 'pay day', 'cutoff', 'cut-off', 'net pay', 'gross pay', 'take home', 'take-home', 'payroll period', 'semi-monthly', 'bi-weekly', 'monthly pay', 'weekly pay', 'atm', 'bank account', 'direct deposit', 'salary credited', 'credited', 'delayed salary', 'late salary',
                // Separation pay
                'backpay', 'back pay', 'separation pay', 'final pay', 'last pay', 'unpaid', 'outstanding', 'remaining balance', 'pro-rated', 'prorated', 'computation', 'quitclaim', 'release',
                // Leave credits
                'leave credits', 'leave balance', 'vacation leave', 'vl', 'sick leave', 'sl', 'maternity', 'paternity', 'solo parent', 'bereavement', 'emergency leave', 'service incentive leave', 'sil', 'leave conversion', 'monetization', 'leave encashment', 'unused leave', 'forfeited', 'carry over', 'leave without pay', 'lwop', 'unpaid leave', 'absence without leave', 'special leave', 'magna carta', 'pwds', 'gynaecological', 'battered woman',
                // Time off
                'time off', 'pto', 'paid time off', 'day off', 'off day', 'absent', 'attendance bonus', 'perfect attendance', 'birthday leave', 'anniversary', 'personal day', 'mental health', 'wellness day', 'study leave', 'sabbatical'
            ],
            'Employee Development' => [
                // Training
                'training', 'trainings', 'train', 'seminar', 'seminars', 'workshop', 'workshops', 'bootcamp', 'boot camp', 'in-house training', 'external training', 'on-the-job', 'ojt', 'hands-on', 'practical', 'simulation', 'role play', 'case study',
                // Courses
                'course', 'courses', 'module', 'modules', 'class', 'classes', 'lesson', 'lessons', 'curriculum', 'syllabus', 'subject', 'topic', 'topics', 'session', 'sessions', 'program', 'programs', 'programme',
                // Learning
                'learning', 'learn', 'e-learning', 'elearning', 'online learning', 'lms', 'development', 'develop', 'self-paced', 'instructor-led', 'virtual', 'webcast', 'video', 'tutorial', 'tutorials', 'knowledge', 'knowledge base', 'resource', 'resources', 'material', 'materials', 'handout', 'handouts',
                // Skills
                'upskill', 'upskilling', 'reskill', 'reskilling', 'cross-training', 'multi-skilling', 'skill', 'skills', 'competency', 'competencies', 'capability', 'capabilities', 'proficiency', 'expertise', 'technical skill', 'soft skill', 'hard skill', 'communication', 'leadership', 'management', 'teamwork', 'collaboration', 'problem solving', 'critical thinking', 'analytical', 'creative', 'innovation', 'adaptability', 'flexibility', 'resilience',
                // Certification
                'certification', 'certificate', 'certifications', 'certificates', 'accreditation', 'license', 'licensure', 'credential', 'credentials', 'certified', 'accredited', 'professional', 'designation', 'renewal', 'continuing education', 'cpe', 'ceu', 'units',
                // Education
                'education', 'educational', 'study', 'studies', 'scholarship', 'tuition', 'school', 'college', 'university', 'degree', 'masters', 'mba', 'doctorate', 'phd', 'graduate', 'undergraduate', 'diploma', 'associate', 'thesis', 'dissertation', 'research', 'academic', 'assistance', 'subsidy', 'reimbursement', 'sponsorship',
                // Career
                'career growth', 'career path', 'career plan', 'career', 'growth', 'advancement', 'opportunities', 'opportunity', 'progression', 'ladder', 'development plan', 'idp', 'individual development', 'succession', 'pipeline', 'talent', 'high potential', 'hipo', 'fast track', 'accelerated',
                // Mentoring
                'mentoring', 'mentor', 'mentorship', 'coaching', 'coach', 'buddy', 'buddying', 'shadowing', 'shadow', 'guidance', 'guide', 'advisor', 'adviser', 'counselor', 'counseling', 'support', 'sponsor', 'sponsorship',
                // Evaluation
                'evaluation', 'evaluate', 'assessment', 'assess', 'performance review', 'appraisal', 'review', 'self-assessment', 'peer review', '360', 'multi-rater', 'competency assessment', 'skills assessment', 'gap analysis', 'needs analysis', 'tna', 'training needs',
                // Feedback
                'feedback', 'feedbacks', 'improvement', 'improve', 'suggestions', 'constructive', 'positive', 'negative', 'areas for improvement', 'strengths', 'weaknesses', 'opportunities', 'threats', 'swot',
                // Goals
                'goal', 'goals', 'objective', 'objectives', 'target', 'targets', 'kpi', 'kpis', 'okr', 'okrs', 'metrics', 'metric', 'measure', 'measures', 'indicator', 'indicators', 'benchmark', 'benchmarking', 'standard', 'expectations', 'deliverable', 'deliverables', 'milestone', 'milestones',
                // Events
                'conference', 'conferences', 'webinar', 'webinars', 'summit', 'convention', 'symposium', 'forum', 'congress', 'expo', 'exhibition', 'fair', 'meetup', 'networking', 'event', 'events', 'gathering', 'assembly',
                // Onboarding
                'orientation', 'onboarding', 'induction', 'immersion', 'probationary review', 'new hire', 'new employee', 'newcomer', 'welcome', 'introduction', 'familiarization', 'nesting', 'transition', 'integration', 'assimilation', 'acclimation', 'culture', 'values', 'mission', 'vision', 'company overview'
            ],
            'Ranking and Promotion' => [
                // Promotion
                'promotion', 'promotions', 'promote', 'promoted', 'promoting', 'upgrade', 'upgrading', 'move up', 'step up', 'advancement', 'advance', 'elevated', 'elevation', 'career move', 'next level', 'higher position', 'new role', 'bigger role', 'increased responsibility',
                // Rank
                'rank', 'ranks', 'ranking', 'rankings', 'tier', 'tiers', 'hierarchy', 'hierarchical', 'structure', 'org chart', 'organizational', 'chain of command', 'reporting line', 'direct report',
                // Demotion
                'demotion', 'demotions', 'demote', 'demoted', 'downgrade', 'downgraded', 'lower position', 'reduced', 'reassigned', 'lateral', 'lateral move', 'horizontal', 'same level',
                // Position and title
                'position', 'positions', 'title', 'titles', 'designation', 'designations', 'role', 'roles', 'job level', 'job grade', 'job title', 'supervisor', 'manager', 'director', 'executive', 'officer', 'specialist', 'analyst', 'associate', 'coordinator', 'lead', 'head', 'chief', 'vp', 'vice president', 'president', 'ceo', 'coo', 'cfo', 'cto', 'cio',
                // Levels
                'level', 'levels', 'grade', 'grades', 'step', 'steps', 'band', 'bands', 'classification', 'category', 'grouping', 'bracket', 'range', 'entry level', 'mid level', 'senior level', 'executive level', 'c-level', 'c-suite', 'junior', 'mid', 'senior', 'lead', 'principal', 'staff',
                // Salary increase
                'salary increase', 'pay increase', 'raise', 'raises', 'increment', 'increments', 'adjustment', 'adjustments', 'salary adjustment', 'pay adjustment', 'hike', 'bump', 'upgrade', 'enhanced', 'improved', 'higher pay', 'better pay', 'competitive', 'market rate', 'benchmarking',
                // Merit
                'merit', 'merits', 'meritorious', 'deserving', 'earned', 'based on performance', 'performance-based', 'results-based', 'contribution', 'contributions', 'value', 'added value', 'impact', 'impactful',
                // Performance
                'appraisal', 'appraisals', 'performance', 'performer', 'performers', 'top performer', 'high performer', 'low performer', 'underperformer', 'exceeds expectations', 'meets expectations', 'below expectations', 'needs improvement', 'satisfactory', 'unsatisfactory', 'outstanding performance', 'excellent performance', 'consistent', 'reliability', 'dependable',
                // Evaluation
                'evaluation', 'evaluations', 'review', 'reviews', 'rating', 'ratings', 'score', 'scores', 'grade', 'grading', 'assessment', 'annual review', 'quarterly review', 'mid-year review', 'year-end review', 'calibration', 'bell curve', 'forced ranking', 'distribution',
                // Criteria
                'criteria', 'criterion', 'requirement', 'requirements', 'qualification', 'qualifications', 'eligibility', 'eligible', 'qualified', 'disqualified', 'minimum', 'mandatory', 'preferred', 'nice to have', 'prerequisite', 'condition', 'standard', 'threshold',
                // Tenure
                'tenure', 'tenured', 'seniority', 'senior', 'junior', 'service length', 'length of service', 'years of service', 'loyalty', 'longevity', 'milestone', 'anniversary', 'work anniversary', 'years', 'months', 'experience', 'experienced', 'veteran', 'long-time', 'dedicated', 'committed',
                // Recognition
                'award', 'awards', 'recognition', 'recognize', 'commendation', 'commend', 'achievement', 'achievements', 'accomplishment', 'accomplishments', 'honor', 'honors', 'distinction', 'distinctions', 'excellence', 'excellent', 'outstanding', 'exemplary', 'best employee', 'employee of the month', 'employee of the year', 'star performer', 'hall of fame', 'plaque', 'trophy', 'certificate', 'appreciation', 'thank you', 'kudos', 'shoutout', 'spotlight', 'feature', 'celebrated', 'recognized', 'acknowledged', 'praised', 'commended', 'rewarded', 'incentivized', 'bonus', 'gift', 'prize', 'token'
            ],
            'General' => [] // Default
        ];

        foreach ($categories as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (stripos($queryText, $keyword) !== false) {
                    return $category;
                }
            }
        }

        return 'General';
    }

    /**
 * Get keyword-based response when Dialogflow fails
 */
private function getKeywordResponse(string $queryText): string
{
    $queryLower = strtolower($queryText);
    
    if (strpos($queryLower, 'working hours') !== false || strpos($queryLower, 'work hours') !== false) {
        return "Our standard working hours are from 8:00 AM to 5:00 PM, Monday to Friday, with a 1-hour lunch break from 12:00 PM to 1:00 PM. We also offer flexible time arrangements for eligible employees!";
    }
    
    if (strpos($queryLower, 'probation') !== false) {
        return "The probation period is typically 6 months with monthly performance reviews. After successful completion, you'll be regularized with full benefits.";
    }
    
    if (strpos($queryLower, 'flexible') !== false || strpos($queryLower, 'flexi') !== false) {
        return "Yes, we offer flexible time arrangements including flexi-time, compressed workweeks, and remote work options. For specific details about eligibility and how to apply, please submit a Flexible Work Request Form through the HR portal.";
    }
    
    if (strpos($queryLower, 'salary') !== false || strpos($queryLower, 'pay') !== false) {
        return "Payday is on the 30th of each month. You can view your payslip in the Employee Portal under 'My Payslips'.";
    }
    
    if (strpos($queryLower, 'leave') !== false) {
        return "We offer 20 days annual leave, 15 days sick leave, and various special leaves. Apply through the HR portal with 2 weeks notice.";
    }
    
    if (strpos($queryLower, 'benefit') !== false) {
        return "Our benefits package includes health insurance, dental coverage, retirement plan, and various allowances. For specific details, check the Employee Handbook or contact HR.";
    }
    
    return "Thanks for your question! I want to make sure I understand correctly. Could you provide more details about '{$queryText}'?";
}

    /**
     * 🆕 NEW: Alternative ticket creation method using DB facade
     */
    private function createTicketAlternativeMethod(string $ticketNo, $employeeNum, string $queryText, string $priority, string $category, string $reason, float $originalConfidence = null)
    {
        try {
            Log::info("🔄 Trying alternative ticket creation method", ['ticket_no' => $ticketNo]);
            
            // Calculate deadlines
            $deadlines = $this->calculateDeadlines($priority);
            
            // Use DB facade for direct insertion
            $now = now();
            $result = DB::table('hr_inbox')->insert([
                'ticket_no' => $ticketNo,
                'from_user' => $employeeNum ?: 'GUEST',
                'message' => $queryText,
                'status' => 'Open',
                'priority' => strtolower($priority),
                'category' => $category,
                'intent' => substr('Escalated: ' . $reason, 0, 50),
                'confidence' => $originalConfidence ?? 0.0,
                'response_deadline' => $deadlines['response_deadline'],
                'resolution_deadline' => $deadlines['resolution_deadline'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            if ($result) {
                return DB::table('hr_inbox')->where('ticket_no', $ticketNo)->first();
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Alternative ticket creation failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * 🆕 NEW: Ensure ticket creation no matter what - final fallback
     */
    private function ensureTicketCreation(?string $originalTicketNo, $employeeNum, string $queryText, string $reason, float $originalConfidence = null): string
    {
        $ticketNo = $originalTicketNo ?: 'TKT-EMERGENCY-' . time();

         $questionTimeFormatted = \Carbon\Carbon::now()->format('Y-m-d H:i:s.u');
        
        try {
            Log::info("🚨 EMERGENCY: Ensuring ticket creation with final fallback", ['ticket_no' => $ticketNo]);

            // Determine priority based on content and confidence
            $priority = $this->determinePriority($queryText, $originalConfidence);
            $category = $this->determineCategory($queryText);
            $deadlines = $this->calculateDeadlines($priority);

            // Get random active HR staff to assign ticket (not deactivated or archived)
            $activeHR = DB::table('users')
                ->where('role', 'HR')
                ->where('status', 'Active')
                ->where('is_archived', 0)
                ->pluck('employeeNum')
                ->toArray();
            
            $assignedTo = !empty($activeHR) ? $activeHR[array_rand($activeHR)] : null;
            
            // Try multiple creation methods
            $methods = [
                'eloquent_create' => function() use ($ticketNo, $employeeNum, $queryText, $reason, $priority, $category, $deadlines, $assignedTo) {
                    return HrInbox::create([
                        'ticket_no' => $ticketNo,
                        'from_user' => $employeeNum ?: 'GUEST',
                        'message' => $queryText,
                        'status' => 'Open',
                        'priority' => strtolower($priority),
                        'category' => $category,
                        'intent' => substr('EMERGENCY: ' . $reason, 0, 50),
                        'confidence' => 0.0,
                        'assigned_to' => $assignedTo,
                        'response_deadline' => $deadlines['response_deadline'],
                        'resolution_deadline' => $deadlines['resolution_deadline'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                },
                'db_insert' => function() use ($ticketNo, $employeeNum, $queryText, $reason, $priority, $category, $deadlines, $assignedTo) {
                    return DB::table('hr_inbox')->insert([
                        'ticket_no' => $ticketNo,
                        'from_user' => $employeeNum ?: 'GUEST',
                        'message' => $queryText,
                        'status' => 'Open',
                        'priority' => strtolower($priority),
                        'category' => $category,
                        'intent' => substr('EMERGENCY: ' . $reason, 0, 50),
                        'confidence' => 0.0,
                        'assigned_to' => $assignedTo,
                        'intent' => substr('EMERGENCY: ' . $reason, 0, 50),
                        'confidence' => 0.0,
                        'response_deadline' => $deadlines['response_deadline'],
                        'resolution_deadline' => $deadlines['resolution_deadline'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                },
                'raw_sql' => function() use ($ticketNo, $employeeNum, $queryText, $reason, $priority, $category, $deadlines) {
                    $sql = "INSERT INTO hr_inbox (ticket_no, from_user, message, status, priority, category, intent, confidence, response_deadline, resolution_deadline, created_at, updated_at) 
                            VALUES (?, ?, ?, 'Open', ?, ?, ?, 0.0, ?, ?, NOW(), NOW())";
                    return DB::insert($sql, [
                        $ticketNo,
                        $employeeNum ?: 'GUEST',
                        $queryText,
                        strtolower($priority),
                        $category,
                        substr('EMERGENCY: ' . $reason, 0, 50),
                        $deadlines['response_deadline'],
                        $deadlines['resolution_deadline']
                    ]);
                }
            ];

            foreach ($methods as $methodName => $method) {
                try {
                    $result = $method();
                    if ($result) {
                        Log::info("✅ Emergency ticket created via {$methodName}", ['ticket_no' => $ticketNo]);
                        
                        // Also create query record if possible
                        try {
                            Query::create([
                                'queryID' => Str::uuid(),
                                'employeeNum' => $employeeNum,
                                'question' => $queryText,
                                'response' => 'EMERGENCY Escalated to HR - ' . $ticketNo,
                                'confidenceScore' => 0.0,
                                'queryType' => 'Escalated',
                                'questionTime' => $questionTimeFormatted,
                                'responseTime' => \Carbon\Carbon::now()->format('Y-m-d H:i:s.u'),
                                'isEscalated' => true,
                                'handledBy' => 'HR',
                            ]);
                        } catch (\Exception $e) {
                            // Ignore query creation errors in emergency mode
                        }
                        
                        return $ticketNo;
                    }
                } catch (\Exception $e) {
                    Log::warning("Emergency method {$methodName} failed: " . $e->getMessage());
                    continue;
                }
            }

            // 🆕 FINAL FALLBACK: Log to file if database is completely down
            $this->logTicketToFile($ticketNo, $employeeNum, $queryText, $reason);
            return $ticketNo;

        } catch (\Exception $e) {
            Log::error('🚨 CRITICAL: All ticket creation methods failed: ' . $e->getMessage());
            
            // Ultimate fallback - log to file and return ticket number anyway
            $this->logTicketToFile($ticketNo, $employeeNum, $queryText, $reason);
            return $ticketNo;
        }
    }

    /**
     * 🆕 NEW: Log ticket to file as final emergency backup
     */
    private function logTicketToFile(string $ticketNo, $employeeNum, string $queryText, string $reason): void
    {
        try {
            $logEntry = [
                'timestamp' => now()->toISOString(),
                'ticket_no' => $ticketNo,
                'employeeNum' => $employeeNum,
                'query' => $queryText,
                'reason' => $reason,
                'emergency' => true
            ];

            $logPath = storage_path('logs/emergency_tickets.log');
            file_put_contents($logPath, json_encode($logEntry) . PHP_EOL, FILE_APPEND | LOCK_EX);
            
            Log::warning("🚨 Ticket logged to emergency file: {$ticketNo}");
            
        } catch (\Exception $e) {
            // If even file logging fails, there's nothing more we can do
            Log::error('🚨 CRITICAL: Emergency file logging failed: ' . $e->getMessage());
        }
    }

 public function sync(Request $request)
{
    // Force JSON response
    try {
        Log::info('Admin attempting to sync with Dialogflow', [
            'user' => Auth::user()->email ?? 'unknown',
            'user_id' => Auth::id()
        ]);

        // Check if user is admin
        if (!Auth::check() || !in_array(Auth::user()->role, ['Admin', 'HR'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Admin access required'
            ], 403);
        }

        // TRY to sync with Dialogflow, but always return JSON
        $intents = [];
        $error = null;
        $usingMockData = false;
        
        try {
            // Check if DialogflowService exists
            if (!class_exists('App\Services\DialogflowService')) {
                throw new \Exception('DialogflowService class not found');
            }
            
            $dialogflow = new DialogflowService();
            
            // Try to get intents
            $intents = $dialogflow->listIntents();
            
            $dialogflow->close();
            
            if (empty($intents)) {
                throw new \Exception('No intents received from Dialogflow');
            }
            
        } catch (\Exception $e) {
            Log::warning('Dialogflow sync failed, using mock data: ' . $e->getMessage());
            $error = $e->getMessage();
            $usingMockData = true;
            
            // Return mock data for development
            $intents = [
                [
                    'id' => 'mock-1',
                    'display_name' => 'Leave Policy Inquiry',
                    'training_phrases' => ['How do I apply for leave?', 'What is the leave policy?'],
                    'training_phrases_count' => 2,
                    'responses' => ['You can apply for leave through the HR portal.'],
                    'responses_count' => 1,
                    'priority' => 'normal',
                    'is_fallback' => false,
                    'status' => 'active',
                    'created_at' => now()->subDays(5)->toDateTimeString(),
                    'updated_at' => now()->subDays(1)->toDateTimeString(),
                ],
                [
                    'id' => 'mock-2',
                    'display_name' => 'Benefits Information',
                    'training_phrases' => ['What benefits do I get?', 'Tell me about health insurance'],
                    'training_phrases_count' => 2,
                    'responses' => ['Employees receive health insurance, dental coverage, and retirement benefits.'],
                    'responses_count' => 1,
                    'priority' => 'normal',
                    'is_fallback' => false,
                    'status' => 'active',
                    'created_at' => now()->subDays(10)->toDateTimeString(),
                    'updated_at' => now()->subDays(2)->toDateTimeString(),
                ],
            ];
        }

        Log::info('Dialogflow sync completed', [
            'intents_count' => count($intents),
            'using_mock_data' => $usingMockData,
            'user' => Auth::user()->email
        ]);

        // ALWAYS return JSON response
        return response()->json([
            'success' => true,
            'message' => 'Successfully synchronized with Dialogflow. Found ' . count($intents) . ' intents.' . 
                        ($usingMockData ? ' (Using development data)' : ''),
            'data' => [
                'intents_synced' => count($intents),
                'timestamp' => now()->toDateTimeString(),
                'intents' => $intents,
                'using_mock_data' => $usingMockData,
                'note' => $usingMockData ? 'Dialogflow API connection failed. Using development data.' : 'Successfully connected to Dialogflow.'
            ]
        ]);

    } catch (\Exception $e) {
        // Even errors should return JSON
        Log::error('Dialogflow sync error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to sync with Dialogflow: ' . $e->getMessage(),
            'error' => 'Internal Server Error',
            'data' => [
                'intents_synced' => 0,
                'timestamp' => now()->toDateTimeString(),
                'intents' => [],
                'note' => 'An unexpected error occurred.'
            ]
        ], 500);
    }
}
    /**
     * Process Dialogflow intents for database storage
     */
    private function processIntentsForDatabase(array $intents): array
    {
        $processedIntents = [];

        foreach ($intents as $intent) {
            try {
                $processedIntents[] = [
                    'intent_id' => $intent->getName(),
                    'display_name' => $intent->getDisplayName(),
                    'training_phrases' => $this->extractTrainingPhrases($intent),
                    'responses' => $this->extractResponses($intent),
                    'parameters' => $this->extractParameters($intent),
                    'priority' => $this->determineIntentPriority($intent),
                    'webhook_state' => $intent->getWebhookState(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            } catch (\Exception $e) {
                Log::warning('Failed to process intent: ' . $e->getMessage(), [
                    'intent' => $intent->getDisplayName() ?? 'unknown'
                ]);
                continue;
            }
        }

        return $processedIntents;
    }

    /**
     * Extract training phrases from intent
     */
    private function extractTrainingPhrases($intent): array
    {
        $phrases = [];
        $trainingPhrases = $intent->getTrainingPhrases();
        
        if ($trainingPhrases) {
            foreach ($trainingPhrases as $phrase) {
                $phrases[] = $phrase->getParts()[0]->getText() ?? '';
            }
        }
        
        return $phrases;
    }

    /**
     * Extract responses from intent
     */
    private function extractResponses($intent): array
    {
        $responses = [];
        $messages = $intent->getMessages();
        
        if ($messages) {
            foreach ($messages as $message) {
                if ($message->getText()) {
                    $texts = $message->getText()->getText();
                    foreach ($texts as $text) {
                        $responses[] = $text;
                    }
                }
            }
        }
        
        return $responses;
    }

    /**
     * Extract parameters from intent
     */
    private function extractParameters($intent): array
    {
        $parameters = [];
        $intentParameters = $intent->getParameters();
        
        if ($intentParameters) {
            foreach ($intentParameters as $param) {
                $parameters[] = [
                    'name' => $param->getName(),
                    'display_name' => $param->getDisplayName(),
                    'entity_type' => $param->getEntityType(),
                    'mandatory' => $param->getMandatory(),
                    'prompts' => $param->getPrompts() ? iterator_to_array($param->getPrompts()) : []
                ];
            }
        }
        
        return $parameters;
    }

    /**
     * Determine intent priority based on display name or content
     */
    private function determineIntentPriority($intent): string
    {
        $displayName = strtolower($intent->getDisplayName() ?? '');
        
        // Urgent intents
        if (str_contains($displayName, 'urgent') || 
            str_contains($displayName, 'emergency') ||
            str_contains($displayName, 'critical')) {
            return 'urgent';
        }
        
        // High priority intents
        if (str_contains($displayName, 'salary') || 
            str_contains($displayName, 'pay') ||
            str_contains($displayName, 'benefit') ||
            str_contains($displayName, 'complaint')) {
            return 'high';
        }
        
        // Normal priority (default)
        return 'normal';
    }

    /**
     * Save intents to database
     */
    private function saveIntentsToDatabase(array $intents): void
    {
        try {
            // Check if you have an Intent model
            if (class_exists('App\\Models\\Intent')) {
                $model = new \App\Models\Intent();
                
                // Clear existing intents
                $model::truncate();
                
                // Insert new intents
                foreach ($intents as $intent) {
                    $model::create($intent);
                }
                
                Log::info('Intents saved to database', ['count' => count($intents)]);
            } else {
                // If no Intent model, log to file or database table
                Log::info('No Intent model found. Intents processed but not saved to database.', [
                    'intents_count' => count($intents)
                ]);
                
                // You could create an intents table with:
                // php artisan make:model Intent -m
                // Then run migrations
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to save intents to database: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send email notification to HR staff when a ticket is assigned to them
     */
    private function sendTicketAssignmentEmail($hrEmployeeNum, $ticketNo, $message, $priority, $fromUser)
    {
        try {
            // Get HR staff email
            $hrUser = DB::table('users')
                ->where('employeeNum', $hrEmployeeNum)
                ->first(['email', 'firstName', 'lastName', 'name']);
            
            if (!$hrUser || empty($hrUser->email)) {
                Log::warning('Cannot send ticket assignment email - HR user has no email', [
                    'hr_employee_num' => $hrEmployeeNum,
                    'ticket_no' => $ticketNo
                ]);
                return;
            }
            
            $hrName = !empty($hrUser->firstName) ? $hrUser->firstName : ($hrUser->name ?? 'HR Staff');
            
            // Determine time remaining based on priority
            $timeRemaining = match(strtolower($priority)) {
                'urgent' => '30 minutes',
                'high' => '1 hour',
                'medium' => '8 hours',
                'low' => '24 hours',
                default => '24 hours'
            };
            
            $subject = "A new Ticket has been assigned to you";
            
            $htmlContent = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <div style='background: linear-gradient(135deg, #2D5A3D 0%, #4A7C59 100%); padding: 20px; text-align: center;'>
                        <h1 style='color: white; margin: 0;'>AIHRA</h1>
                        <p style='color: #E8F5E8; margin: 5px 0 0 0;'>Ticket Assignment Notification</p>
                    </div>
                    <div style='padding: 30px; background: #f8f9fa;'>
                        <p style='font-size: 16px; color: #333;'>Hey,</p>
                        <p style='font-size: 14px; color: #333; line-height: 1.6;'>You have received a message regarding <strong>\"" . htmlspecialchars($message) . "\"</strong> kindly respond within the given time frame.</p>
                        <p style='font-size: 14px; color: #333; margin-top: 15px;'><strong>Time remaining: {$timeRemaining}</strong></p>
                        
                        <div style='text-align: center; margin-top: 30px;'>
                            <a href='" . url('/hr/dashboard') . "' style='background: #2D5A3D; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;'>View Ticket</a>
                        </div>
                    </div>
                    <div style='background: #333; padding: 15px; text-align: center;'>
                        <p style='color: #999; font-size: 12px; margin: 0;'>This is an automated message from AIHRA. Please do not reply to this email.</p>
                    </div>
                </div>
            ";
            
            Mail::html($htmlContent, function ($mail) use ($hrUser, $subject) {
                $mail->to($hrUser->email)
                     ->subject($subject);
            });
            
            Log::info('✅ Ticket assignment email sent', [
                'to' => $hrUser->email,
                'ticket_no' => $ticketNo
            ]);
            
        } catch (\Exception $e) {
            // Don't fail the ticket creation if email fails
            Log::error('Failed to send ticket assignment email', [
                'error' => $e->getMessage(),
                'hr_employee_num' => $hrEmployeeNum,
                'ticket_no' => $ticketNo
            ]);
        }
    }

    

    public function getIntents()
    {
        try {
            return response()->json(['success' => true, 'data' => [], 'message' => 'Intents management coming soon']);
        } catch (\Exception $e) {
            Log::error('Error loading intents: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to load intents'], 500);
        }
    }

    public function getGuidedQuestions()
    {
        try {
            $questions = GuidedQuestion::with('children')->whereNull('parent_id')->orderBy('display_order')->get();
            return response()->json(['success' => true, 'data' => $questions]);
        } catch (\Exception $e) {
            Log::error('Error loading guided questions: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to load guided questions'], 500);
        }
    }

    public function createGuidedQuestion(Request $request)
    {
        try {
            $validated = $request->validate(['question_text' => 'required|string', 'parent_id' => 'nullable|exists:guided_questions,gq_id', 'answer_text' => 'nullable|string', 'LEVEL' => 'required|integer', 'display_order' => 'nullable|integer']);
            $question = GuidedQuestion::create($validated);
            return response()->json(['success' => true, 'data' => $question]);
        } catch (\Exception $e) {
            Log::error('Error creating guided question: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to create guided question'], 500);
        }
    }

    public function updateGuidedQuestion(Request $request, $id)
    {
        try {
            $question = GuidedQuestion::findOrFail($id);
            $validated = $request->validate(['question_text' => 'sometimes|string', 'answer_text' => 'nullable|string', 'display_order' => 'nullable|integer', 'status' => 'sometimes|in:active,inactive']);
            $question->update($validated);
            return response()->json(['success' => true, 'data' => $question]);
        } catch (\Exception $e) {
            Log::error('Error updating guided question: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update guided question'], 500);
        }
    }

    public function deleteGuidedQuestion($id)
    {
        try {
            $question = GuidedQuestion::findOrFail($id);
            $question->delete();
            return response()->json(['success' => true, 'message' => 'Guided question deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting guided question: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete guided question'], 500);
        }
    }

    // DialogflowController.php
public function syncIntents(Request $request)
{
    try {
        // Fetch intents from Dialogflow API
        $intents = $this->fetchDialogflowIntents();
        
        // Store or update in your database
        $syncedCount = $this->syncIntentsToDatabase($intents);
        
        return response()->json([
            'success' => true,
            'message' => 'Successfully synced with Dialogflow',
            'data' => [
                'intents_synced' => $syncedCount,
                'intents' => $intents,
                'is_mock_data' => false
            ]
        ]);
        
    } catch (\Exception $e) {
        // Log error
        \Log::error('Dialogflow sync error: ' . $e->getMessage());
        
        // For development/testing, you can return mock data
        $mockIntents = $this->getMockIntents();
        
        return response()->json([
            'success' => true,
            'message' => 'Using mock data - Dialogflow connection failed: ' . $e->getMessage(),
            'data' => [
                'intents_synced' => count($mockIntents),
                'intents' => $mockIntents,
                'is_mock_data' => true
            ]
        ], 200);
    }
}

private function fetchDialogflowIntents()
{
    // Implement your Dialogflow API connection here
    // Example using Google Cloud Dialogflow API
    
    $projectId = config('services.dialogflow.project_id');
    $keyFilePath = config('services.dialogflow.key_file');
    
    if (!$projectId || !$keyFilePath) {
        throw new \Exception('Dialogflow credentials not configured');
    }
    
    // Create a Dialogflow client
    $client = new \Google\Cloud\Dialogflow\V2\IntentsClient([
        'credentials' => json_decode(file_get_contents($keyFilePath), true)
    ]);
    
    // Fetch intents
    $parent = $client->agentName($projectId);
    $intents = [];
    
    try {
        $response = $client->listIntents($parent);
        foreach ($response->iterateAllElements() as $intent) {
            $intents[] = [
                'id' => $intent->getName(),
                'display_name' => $intent->getDisplayName(),
                'training_phrases' => $this->extractTrainingPhrases($intent),
                'responses' => $this->extractResponses($intent),
                'priority' => $intent->getPriority(),
                'is_fallback' => $intent->getIsFallback(),
                'status' => 'active'
            ];
        }
        
        $client->close();
        return $intents;
        
    } catch (\Exception $e) {
        $client->close();
        throw $e;
    }
}

private function getMockIntents()
{
    // Return mock data for testing
    return [
        [
            'id' => 'projects/test-project/agent/intents/123456',
            'display_name' => 'leave.inquiry',
            'training_phrases' => [
                'How do I apply for leave?',
                'What are the leave policies?',
                'How many leave days do I have?'
            ],
            'responses' => [
                'You can apply for leave through the HR portal.',
                'The leave policy allows for 20 days annual leave.'
            ],
            'priority' => 500000,
            'is_fallback' => false,
            'status' => 'active'
        ],
        [
            'id' => 'projects/test-project/agent/intents/789012',
            'display_name' => 'payroll.inquiry',
            'training_phrases' => [
                'When will I get paid?',
                'How is my salary calculated?',
                'Where can I see my payslip?'
            ],
            'responses' => [
                'Salaries are processed on the last working day of each month.',
                'You can view your payslip in the employee self-service portal.'
            ],
            'priority' => 500000,
            'is_fallback' => false,
            'status' => 'active'
        ]
    ];
}
}
