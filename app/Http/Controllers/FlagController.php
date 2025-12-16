<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FlaggedResponse;
use App\Models\Query;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FlagController extends Controller
{
    public function store(Request $request)
    {
        try {
            \Log::info('Flag store request received', [
                'data' => $request->all(),
                'user' => Auth::user()->employeeNum ?? 'unknown'
            ]);

            $request->validate([
                'user_query' => 'required|string',
                'bot_response' => 'required|string',
                'reason' => 'required|string',
            ]);

            // First, create or get the query record
            $queryRecord = Query::create([
                'employeeNum' => Auth::user()->employeeNum,
                'question' => $request->user_query,
                'response' => $request->bot_response,
                'questionTime' => now(),
                'isEscalated' => false,
                'handledBy' => 'Bot',
            ]);

            \Log::info('Query record created', ['queryID' => $queryRecord->queryID]);

            // Then create the flagged response
            $flagged = FlaggedResponse::create([
                'employeeNum' => Auth::user()->employeeNum,
                'queryID' => $queryRecord->queryID,
                'reason' => $request->reason,  // Store reason text directly
                'timeStamp' => now(),
                'status' => 'Pending',
            ]);

            \Log::info('Flagged response created', ['flaggedID' => $flagged->flaggedID]);

            return response()->json([
                'success' => true,
                'message' => 'Response flagged successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error flagging response', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Reviewed,Resolved',
        ]);

        $flagged = FlaggedResponse::findOrFail($id);
        $flagged->status = $request->status;
        $flagged->save();

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }
}
