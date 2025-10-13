<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Query;
use App\Models\HrInbox;
use Illuminate\Support\Str;
use App\Services\DialogflowService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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

        // Get intent data
        $fulfillmentText = $result->getFulfillmentText() ?? 'I’m not sure, please clarify.';
        $intentName      = $result->getIntent()->getDisplayName();
        $confidence      = $result->getIntentDetectionConfidence();

        // ✅ Use logged-in user or default system user
        $employeeNum = Auth::check() ? Auth::user()->employeeNum : 0;

        // Check if we should escalate
        $isEscalated = ($intentName === 'Default Fallback Intent' || $confidence < 0.7);

        // Save chat query
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

        // ✅ Forward to HR if fallback or low confidence
        if ($isEscalated) {
            HrInbox::create([
                'ticket_no'  => strtoupper(Str::random(8)),
                'from_user'  => $employeeNum,
                'message'    => $queryText,
                'status'     => 'Pending',
                'priority'   => 'High',
            ]);

            $fulfillmentText = "✅ Your question has been forwarded to HR for further assistance.";
        }

        return response()->json([
            'fulfillmentText' => $fulfillmentText
        ]);

    } catch (\Throwable $e) {
        Log::error('Dialogflow error: ' . $e->getMessage());
        return response()->json([
            'fulfillmentText' => '⚠️ Error talking to AIHRA.'
        ], 500);
        Log::error('Dialogflow error: ' . $e->getMessage());

    }
}

}
