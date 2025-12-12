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

            // 🔥 IMPROVED: Handle explicit escalation requests
            if ($this->isEscalationRequest($queryText)) {
                Log::info('Escalation request detected', ['query' => $queryText]);
                $this->resetRetryCount();

                $escalationResponse = $this->escalateToHR($queryText, $employeeNum, 'User requested human assistance', null, $conversation ? $conversation->id : null);

                // Update conversation title if needed
                if (!empty($conversation) && empty($conversation->title)) {
                    try {
                        $conversation->title = now()->toDateString() . ' - ' . Str::limit($conversation->first_message ?? $queryText, 80);
                        $conversation->save();
                    } catch (\Throwable $e) {
                        Log::warning('Failed to update conversation title: ' . $e->getMessage());
                    }
                }

                return $escalationResponse;
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
            $dialogflow = new DialogflowService();
            
            Log::info('Calling Dialogflow service', ['sessionId' => $sessionId]);
            $result = $dialogflow->detectIntent($queryText, $sessionId);
            $dialogflow->close();

            $confidence = $result->getIntentDetectionConfidence() ?? 0.0;
            $fulfillmentText = $result->getFulfillmentText() ?? "I want to make sure I give you accurate information. Let me guide you through our topics.";
            $intentName = $result->getIntent() ? $result->getIntent()->getDisplayName() : 'Default Fallback Intent';

            Log::info('Dialogflow Response', [
                'confidence' => $confidence,
                'intent' => $intentName,
                'fulfillmentText' => $fulfillmentText
            ]);

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
            if ($this->shouldEscalate($queryText, $confidence, $intentName)) {
                Log::info('Auto-escalating query', [
                    'reason' => 'Low confidence or urgent content',
                    'confidence' => $confidence,
                    'intent' => $intentName
                ]);
                $this->resetRetryCount();
                return $this->escalateToHR($queryText, $employeeNum, "Auto-escalated: Confidence {$confidence}, Intent: {$intentName}", $confidence, $conversation ? $conversation->id : null);
            }

            // 🆕 NEW: Handle retry logic for unclear questions
            if ($this->shouldRetry($confidence, $intentName)) {
                $retryCount = $this->incrementRetryCount();
                Log::info('Low confidence response, prompting retry', [
                    'retryCount' => $retryCount,
                    'confidence' => $confidence
                ]);

                // Store original query details for potential escalation
                Session::put('pending_escalation', [
                    'query' => $queryText,
                    'confidence' => $confidence,
                    'intent' => $intentName,
                    'timestamp' => now()->toIso8601String()
                ]);

                if ($retryCount > $this->maxRetries) {
                    return $this->offerHREscalation($queryText, $employeeNum, $confidence);
                }

                $retryText = $this->getRetryMessage($retryCount);

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
                    'retryCount' => $retryCount,
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

            // 🔄 Low confidence - start guided flow
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
            $fallbackReply = "I encountered an error processing that request. Let me help you by creating a support ticket for HR, or you can browse our guided topics to find what you need.";
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
            'fulfillmentText' => "I understand this is an HR-related question, but I want to make sure you get the most accurate information. Would you like me to escalate this to our HR team for proper assistance?",
            'suggest_hr' => true,
            'pending_escalation' => [
                'query' => $queryText,
                'employeeNum' => $employeeNum,
                'reason' => $reason
            ],
            'options' => [
                ['text' => '✅ Yes, please escalate to HR', 'action' => 'escalate'],
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
            1 => "I'm not sure I understand. Could you please rephrase your question?",
            2 => "I'm still having trouble understanding. Could you try asking in a different way?",
            3 => "I want to make sure I help you properly. Could you provide more details or context?"
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
            'fulfillmentText' => "I'm having difficulty understanding your question after several attempts. Would you like me to escalate this to our HR team who can provide better assistance?",
            'max_retries_reached' => true,
            'options' => [
                ['text' => '✅ Yes, please connect me with HR', 'action' => 'escalate'],
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
            'thanks' => ["You're very welcome! 😊 Is there anything else I can help you with?", "Happy to help! Let me know if you need anything else."],
            'thank you' => ["You're very welcome! 😊 Is there anything else I can help you with?", "My pleasure! Feel free to ask if you have more questions."],
            'bye' => ["👋 Goodbye! Feel free to ask if you have more HR questions.", "Have a great day! 👋"],
            'goodbye' => ["👋 Goodbye! Feel free to ask if you have more HR questions.", "Take care! 👋"],
            'how are you' => ["I'm doing great, thanks for asking! Ready to help with your HR questions. 😊", "I'm functioning well! How can I assist you with HR matters today?"],
            'who are you' => ["I'm Aihra, your AI HR Assistant! I'm here to help with HR questions and guide you to the right information. 🤖", "I'm Aihra, an AI assistant specialized in HR topics. How can I help you today?"],
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
                        'response_deadline' => $deadlines['response_deadline'],
                        'resolution_deadline' => $deadlines['resolution_deadline'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    Log::info("✅ HR Inbox created successfully on attempt {$attempt}", ['ticket_no' => $ticketNo]);
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
            'Benefits' => ['benefit', 'insurance', 'health', 'dental', 'vacation', 'time off', 'leave'],
            'Payroll' => ['salary', 'pay', 'paycheck', 'wage', 'bonus', 'tax'],
            'Employment' => ['hire', 'hiring', 'promotion', 'raise', 'position', 'job'],
            'Complaint' => ['complaint', 'issue', 'problem', 'harassment', 'discrimination'],
            'Technical' => ['system', 'login', 'password', 'access', 'technical', 'error'],
            'Policy' => ['policy', 'rule', 'regulation', 'procedure', 'guideline'],
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
        
        try {
            Log::info("🚨 EMERGENCY: Ensuring ticket creation with final fallback", ['ticket_no' => $ticketNo]);

            // Determine priority based on content and confidence
            $priority = $this->determinePriority($queryText, $originalConfidence);
            $category = $this->determineCategory($queryText);
            $deadlines = $this->calculateDeadlines($priority);

            // Try multiple creation methods
            $methods = [
                'eloquent_create' => function() use ($ticketNo, $employeeNum, $queryText, $reason, $priority, $category, $deadlines) {
                    return HrInbox::create([
                        'ticket_no' => $ticketNo,
                        'from_user' => $employeeNum ?: 'GUEST',
                        'message' => $queryText,
                        'status' => 'Open',
                        'priority' => strtolower($priority),
                        'category' => $category,
                        'intent' => substr('EMERGENCY: ' . $reason, 0, 50),
                        'confidence' => 0.0,
                        'response_deadline' => $deadlines['response_deadline'],
                        'resolution_deadline' => $deadlines['resolution_deadline'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                },
                'db_insert' => function() use ($ticketNo, $employeeNum, $queryText, $reason, $priority, $category, $deadlines) {
                    return DB::table('hr_inbox')->insert([
                        'ticket_no' => $ticketNo,
                        'from_user' => $employeeNum ?: 'GUEST',
                        'message' => $queryText,
                        'status' => 'Open',
                        'priority' => strtolower($priority),
                        'category' => $category,
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

    
}