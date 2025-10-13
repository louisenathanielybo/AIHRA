<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Query;
use App\Models\HrInbox;
use Illuminate\Support\Str;
use App\Services\DialogflowService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DialogflowController extends Controller
{
    /**
     * Handle Dialogflow webhook requests.
     */
    public function webhook(Request $request)
    {
        try {
            // 🧩 Validate input
            $queryText = trim($request->input('queryResult.queryText', ''));
            if ($queryText === '') {
                return response()->json(['fulfillmentText' => 'No message received.']);
            }

            // 🎯 Initialize Dialogflow
            $sessionId  = session()->getId();
            $dialogflow = new DialogflowService();
            $result     = $dialogflow->detectIntent($queryText, $sessionId);
            $dialogflow->close();

            // Extract Dialogflow results
            $intentName      = $result->getIntent()?->getDisplayName() ?? 'UnknownIntent';
            $confidence      = $result->getIntentDetectionConfidence();
            $fulfillmentText = $result->getFulfillmentText() ?? 'I’m not sure, please clarify.';
            $employeeNum     = Auth::check() ? Auth::user()->employeeNum : 0;

            // 💬 Handle greetings and small talk
            if (preg_match('/\b(hi|hello|hey)\b/i', $queryText)) {
                $fulfillmentText = "👋 Hello there! How can I assist you today?";
                $confidence = 1.0;
            } elseif (preg_match('/\b(thanks|thank you)\b/i', $queryText)) {
                $fulfillmentText = "You're very welcome! 😊";
                $confidence = 1.0;
            } elseif (preg_match('/\b(bye|goodbye|see you)\b/i', $queryText)) {
                $fulfillmentText = "👋 See you next time! Don’t hesitate to ask if you have more HR-related questions.";
                $confidence = 1.0;
            }

            // 🧭 Tag category automatically
            $category = 'General';
            if (preg_match('/absent|late|attendance|leave/i', $queryText)) {
                $category = 'Attendance';
            } elseif (preg_match('/benefit|sss|philhealth|allowance/i', $queryText)) {
                $category = 'Benefits';
            } elseif (preg_match('/promotion|raise|salary|increase/i', $queryText)) {
                $category = 'Compensation';
            } elseif (preg_match('/resign|quit|termination|end of contract/i', $queryText)) {
                $category = 'Employment Status';
            } elseif (preg_match('/training|seminar|schooling/i', $queryText)) {
                $category = 'Development';
            }

            // 🧠 Decide if escalation needed
            $isEscalated = ($intentName === 'Default Fallback Intent' || $confidence < 0.5);

            // 🔺 Determine priority based on confidence
            if ($confidence >= 0.8) {
                $priority = 'Low';
            } elseif ($confidence >= 0.6) {
                $priority = 'Medium';
            } elseif ($confidence >= 0.4) {
                $priority = 'High';
            } else {
                $priority = 'Urgent';
            }

            // 🧾 Save chatbot query for analytics
            Query::create([
                'queryID'         => Str::uuid(),
                'employeeNum'     => $employeeNum,
                'question'        => $queryText,
                'response'        => $fulfillmentText,
                'confidenceScore' => $confidence,
                'queryType'       => 'Dialogflow',
                'questionTime'    => now(),
                'responseTime'    => now(),
                'isEscalated'     => $isEscalated,
                'handledBy'       => $isEscalated ? 'HR' : 'Bot',
            ]);

            // 🚀 Forward escalated queries to HR
            if ($isEscalated) {
                HrInbox::create([
                    'ticket_no'  => strtoupper(Str::random(8)),
                    'from_user'  => $employeeNum,
                    'message'    => $queryText,
                    'status'     => 'Pending',
                    'priority'   => $priority,
                    'category'   => $category,
                    'intent'     => $intentName,
                    'confidence' => $confidence,
                ]);

                $fulfillmentText = "✅ Your question has been forwarded to HR for further assistance.";
            }

            // ✅ Return structured response
            return response()->json([
                'status'          => 'success',
                'handledBy'       => $isEscalated ? 'HR' : 'Bot',
                'confidence'      => $confidence,
                'intent'          => $intentName,
                'category'        => $category,
                'priority'        => $priority,
                'fulfillmentText' => $fulfillmentText
            ]);

            $hrReply = ChatMessage::where('ticket_no', $employee_ticket)
    ->where('sender', 'hr')
    ->latest()
    ->first();

if ($hrReply) {
    return response()->json([
        'fulfillmentText' => "📩 HR replied: " . $hrReply->message
    ]);
}


        } catch (\Throwable $e) {
            Log::error('Dialogflow error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status'          => 'error',
                'fulfillmentText' => '⚠️ Error communicating with AIHRA.'
            ], 500);
        }
    }
}
