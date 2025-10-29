<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\HrInbox;
use App\Models\ChatMessage;
use App\Models\HrReply;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function index()
    {
        $announcements = DB::table('announcements')
            ->orderBy('id', 'desc')
            ->get();

        return view('employee.empl_dashboard', compact('announcements'));
    }

    /**
     * 🆕 Get latest ticket for employee
     */
    /**
 * 🆕 Get latest ticket for employee - UPDATED to check HR replies
 */
public function getLatestTicket()
{
    try {
        $employeeNum = Auth::user()->employeeNum;
        
        $latestTicket = HrInbox::where('from_user', $employeeNum)
            ->orderBy('created_at', 'desc')
            ->first();
            
        if (!$latestTicket) {
            return response()->json([
                'ticket_no' => null,
                'has_new_reply' => false
            ]);
        }
        
        // 🆕 CHECK FOR HR REPLIES in hr_replies table
        $latestHRReply = HrReply::where('ticket_no', $latestTicket->ticket_no)
            ->orderBy('replied_at', 'desc')
            ->first();
            
        // Check if there are new HR replies (replied after ticket was last updated)
        $hasNewReply = $latestHRReply && $latestHRReply->replied_at > $latestTicket->updated_at;
        
        return response()->json([
            'ticket_no' => $latestTicket->ticket_no,
            'has_new_reply' => $hasNewReply,
            'status' => $latestTicket->status
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Get latest ticket error: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to get latest ticket'], 500);
    }
}

    /**
     * 🆕 Get messages for a ticket
     */
    /**
 * 🆕 Get messages for a ticket - UPDATED to include HR replies
 */
public function getMessages($ticket_no)
{
    try {
        $employeeNum = Auth::user()->employeeNum;
        
        // Verify the ticket belongs to this employee
        $ticket = HrInbox::where('ticket_no', $ticket_no)
            ->where('from_user', $employeeNum)
            ->first();
            
        if (!$ticket) {
            return response()->json([], 403);
        }
        
        $messages = collect();
        
        // 1. Add the original employee message
        $messages->push([
            'sender' => 'employee',
            'message' => $ticket->message,
            'created_at' => $ticket->created_at,
        ]);
        
        // 2. 🆕 ADD HR REPLIES from hr_replies table
        $hrReplies = HrReply::where('ticket_no', $ticket_no)
            ->orderBy('replied_at', 'asc')
            ->get();
            
        foreach ($hrReplies as $reply) {
            $messages->push([
                'sender' => 'hr',
                'message' => $reply->hr_message,
                'created_at' => $reply->replied_at,
            ]);
        }
        
        // 3. Sort all messages by date
        return response()->json($messages->sortBy('created_at')->values());
        
    } catch (\Exception $e) {
        \Log::error('Get messages error: ' . $e->getMessage());
        return response()->json([], 500);
    }
}

    /**
 * 🆕 Get all tickets for employee
 */
/**
 * 🆕 Get all tickets for employee
 */
public function getTickets()
{
    try {
        $employeeNum = Auth::user()->employeeNum;
        
        $tickets = HrInbox::where('from_user', $employeeNum)
            ->orderBy('created_at', 'desc')
            ->get(['ticket_no', 'message', 'status', 'created_at']);
            
        return response()->json($tickets);
        
    } catch (\Exception $e) {
        \Log::error('Get tickets error: ' . $e->getMessage());
        return response()->json([], 500);
    }
}

/**
 * 🆕 Get ticket status
 */
public function getTicketStatus($ticket_no)
{
    try {
        $employeeNum = Auth::user()->employeeNum;
        
        $ticket = HrInbox::where('ticket_no', $ticket_no)
            ->where('from_user', $employeeNum)
            ->first(['status']);
            
        if (!$ticket) {
            return response()->json(['status' => 'Not Found'], 404);
        }
        
        return response()->json(['status' => $ticket->status]);
        
    } catch (\Exception $e) {
        \Log::error('Get ticket status error: ' . $e->getMessage());
        return response()->json(['status' => 'Error'], 500);
    }
}

public function replyToTicket(Request $request)
{
    $request->validate([
        'ticket_no' => 'required|string|exists:hr_inbox,ticket_no',
        'message' => 'required|string',
    ]);

    try {
        $employeeNum = Auth::user()->employeeNum;
        
        // Verify the ticket belongs to this employee
        $ticket = HrInbox::where('ticket_no', $request->ticket_no)
            ->where('from_user', $employeeNum)
            ->first();
            
        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found or access denied'
            ], 403);
        }

        // 🆕 Save employee's additional message to hr_replies (as employee follow-up)
        \App\Models\HrReply::create([
            'ticket_no'  => $request->ticket_no,
            'hr_message' => "🔁 Employee Follow-up: " . $request->message,
            'replied_by' => $employeeNum,
            'replied_at' => now(),
        ]);

        // Update ticket status to indicate new activity
        HrInbox::where('ticket_no', $request->ticket_no)
            ->update([
                'status' => 'Waiting for HR',
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Reply sent successfully! HR has been notified.'
        ]);

    } catch (\Exception $e) {
        \Log::error('Employee ticket reply error: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to send reply: ' . $e->getMessage()
        ], 500);
    }
}
}