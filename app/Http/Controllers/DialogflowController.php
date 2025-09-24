<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KnowledgeBase;
use App\Models\Query;
use Illuminate\Support\Str;

class DialogflowController extends Controller
{
    public function webhook(Request $request)
    {
        try {
            $queryText = trim(strip_tags($request->input('queryResult.queryText', '')));
            if (empty($queryText) || strlen($queryText) > 500) {
                return response()->json(['fulfillmentText' => 'I did not understand that.']);
            }

            // 1️⃣ Check knowledge_base table
            $knowledgeBase = KnowledgeBase::where('sampleQuestion', 'like', "%{$queryText}%")
                ->orWhereRaw('LOWER(sampleQuestion) LIKE LOWER(?)', ["%{$queryText}%"])
                ->first();

            $answer = $knowledgeBase ? $knowledgeBase->answer : null;
            $confidence = $request->input('queryResult.intentDetectionConfidence', 0);

            // 2️⃣ Determine final response
            if ($answer) {
                $fulfillmentText = $answer;
            } elseif ($confidence >= 0.6) {
                // Use Dialogflow response if confidence is high
                $fulfillmentText = $request->input('queryResult.fulfillmentText', "I’m not sure, please clarify.");
            } else {
                // Low confidence: escalate to HR
                $fulfillmentText = "Your question has been forwarded to HR for assistance.";
            }

            // 3️⃣ Log the query
            Query::create([
                'queryID' => Str::uuid(),
                'employeeNum' => $request->input('employeeNum', 'guest'),
                'question' => $queryText,
                'response' => $fulfillmentText,
                'confidenceScore' => $confidence,
                'queryType' => $knowledgeBase ? 'KnowledgeBase' : 'Smalltalk',
                'questionTime' => now(),
                'responseTime' => now(),
                'isEscalated' => $confidence < 0.6,
                'handledBy' => $confidence < 0.6 ? 'HR' : null,
            ]);

            return response()->json(['fulfillmentText' => $fulfillmentText]);

        } catch (\Exception $e) {
            \Log::error('Dialogflow webhook error: ' . $e->getMessage());
            return response()->json(['fulfillmentText' => 'An error occurred. Please try again later.'], 500);
        }
    }
}
