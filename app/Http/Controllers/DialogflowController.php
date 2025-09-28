<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Query;
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
            $queryText = trim($request->input('queryResult.queryText', ''));
            if ($queryText === '') {
                return response()->json([
                    'fulfillmentText' => 'No message received.'
                ]);
            }

            $sessionId  = session()->getId();
            $dialogflow = new DialogflowService();
            $result     = $dialogflow->detectIntent($queryText, $sessionId);
            $dialogflow->close();

            $fulfillmentText = $result->getFulfillmentText() ?? 'I’m not sure, please clarify.';

            // ✅ Fallback to the SYSTEM user if no one is logged in
            $employeeNum = Auth::check()
    ? Auth::user()->employeeNum
    : 0; 

            Query::create([
                'queryID'         => Str::uuid(),
                'employeeNum'     => $employeeNum,
                'question'        => $queryText,
                'response'        => $fulfillmentText,
                'confidenceScore' => $result->getIntentDetectionConfidence(),
                'queryType'       => 'Dialogflow',
                'questionTime'    => now(),
                'responseTime'    => now(),
                'isEscalated'     => $result->getIntentDetectionConfidence() < 0.6,
                'handledBy'       => $result->getIntentDetectionConfidence() < 0.6 ? 'HR' : 'Bot',
            ]);

            return response()->json([
                'fulfillmentText' => $fulfillmentText
            ]);

        } catch (\Throwable $e) {
            Log::error('Dialogflow error: '.$e->getMessage());
            return response()->json([
                'fulfillmentText' => '⚠️ Error talking to AIHRA.'
            ], 500);
        }
    }
}
