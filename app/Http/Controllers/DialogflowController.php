<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Query;
use App\Models\HrInbox;
use App\Models\GuidedQuestion;
use Illuminate\Support\Str;
use App\Services\DialogflowService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DialogflowController extends Controller
{
    public function webhook(Request $request)
    {
        try {
            $queryText = trim($request->input('queryResult.queryText', ''));
            $employeeNum = Auth::check() ? Auth::user()->employeeNum : 0;

            if ($queryText === '') {
                return response()->json(['fulfillmentText' => 'No message received.']);
            }

            // 💬 Handle greetings - start guided flow immediately
            if (preg_match('/\b(hi|hello|hey|start|help)\b/i', $queryText)) {
                return response()->json([
                    'status' => 'guided_flow',
                    'fulfillmentText' => "👋 Hello! I'm here to help with HR questions. Let me guide you to the right information.",
                    'guided_flow' => true
                ]);
            }

            // 💬 Handle thanks
            if (preg_match('/\b(thanks|thank you)\b/i', $queryText)) {
                return response()->json([
                    'status' => 'success',
                    'fulfillmentText' => "You're very welcome! 😊 Is there anything else I can help you with?"
                ]);
            }

            // 💬 Handle goodbye
            if (preg_match('/\b(bye|goodbye|see you)\b/i', $queryText)) {
                return response()->json([
                    'status' => 'success',
                    'fulfillmentText' => "👋 Goodbye! Feel free to ask if you have more HR questions."
                ]);
            }

            // 🎯 Try Dialogflow for direct questions
            $sessionId = session()->getId() ?? Str::random(10);
            $dialogflow = new DialogflowService();
            $result = $dialogflow->detectIntent($queryText, $sessionId);
            $dialogflow->close();

            $confidence = $result->getIntentDetectionConfidence();
            $fulfillmentText = $result->getFulfillmentText() ?? "I want to make sure I give you accurate information. Let me guide you through our topics.";

            // 🤖 If high confidence, return direct answer
            if ($confidence > 0.7) {
                Query::create([
                    'queryID' => Str::uuid(),
                    'employeeNum' => $employeeNum,
                    'question' => $queryText,
                    'response' => $fulfillmentText,
                    'confidenceScore' => $confidence,
                    'queryType' => 'Dialogflow',
                    'questionTime' => now(),
                    'responseTime' => now(),
                    'isEscalated' => false,
                    'handledBy' => 'Bot',
                ]);

                return response()->json([
                    'status' => 'success',
                    'fulfillmentText' => $fulfillmentText
                ]);
            }

            // 🔄 Low confidence - start guided flow
            return response()->json([
                'status' => 'guided_flow',
                'fulfillmentText' => "I want to make sure I give you the right information. Let me guide you through our HR topics.",
                'guided_flow' => true
            ]);

        } catch (\Throwable $e) {
            Log::error('Dialogflow error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'fulfillmentText' => '⚠️ I encountered an error. Let me guide you through our topics instead.'
            ]);
        }
    }
}