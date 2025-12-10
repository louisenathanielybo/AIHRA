<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HrInbox;
use App\Models\HrReply;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // 📨 Get all tickets for current employee
    public function getEmployeeTickets()
    {
        $employeeNum = Auth::user()->employeeNum;
        
        $tickets = HrInbox::where('from_user', $employeeNum)
            ->orderBy('created_at', 'desc')
            ->get(['ticket_no', 'message', 'status', 'created_at']);

        return response()->json($tickets);
    }

    // 📨 Get messages for specific ticket - FIXED VERSION
   public function getLatestTicket()
{
    $latest = \App\Models\HrInbox::orderBy('created_at', 'desc')->first();
    return response()->json($latest ?? ['ticket_no' => null]);
}

public function getMessages($ticket_no)
{
    $inquiry = HrInbox::where('ticket_no', $ticket_no)->first();

    if (!$inquiry) {
        return response()->json([]);
    }

    $replies = HrReply::where('ticket_no', $ticket_no)
        ->orderBy('replied_at', 'asc')
        ->get()
        ->map(function ($reply) {
            return [
                'message' => $reply->hr_message,
                'sender' => 'hr',
                'created_at' => $reply->replied_at,
            ];
        });

    // Combine employee + HR messages
    $messages = collect([
        [
            'message' => $inquiry->message,
            'sender' => 'employee',
            'created_at' => $inquiry->created_at,
        ]
    ])->merge($replies);

    return response()->json($messages);
}






    // 💬 Employee sends a NEW ticket/message
    public function sendMessage(Request $request)
    {
        $request->validate([
            'from_user' => 'required|string',
            'message' => 'required|string',
        ]);

        $ticketNo = 'TKT-' . strtoupper(Str::random(6));

        HrInbox::create([
            'ticket_no' => $ticketNo,
            'from_user' => $request->from_user,
            'message' => $request->message,
            'status' => 'Pending',
            'priority' => 'Low',
            'category' => 'General',
        ]);

        return response()->json([
            'success' => true, 
            'ticket_no' => $ticketNo,
            'message' => 'Ticket created successfully'
        ]);
    }

    // 💬 Employee sends a REPLY to existing ticket
    public function sendReply(Request $request)
    {
        $request->validate([
            'ticket_no' => 'required|string',
            'from_user' => 'required|string',
            'message' => 'required|string',
        ]);

        // Create a new ticket for the follow-up message
        $newTicketNo = 'TKT-' . strtoupper(Str::random(6));

        HrInbox::create([
            'ticket_no' => $newTicketNo,
            'from_user' => $request->from_user,
            'message' => $request->message,
            'status' => 'Pending',
            'priority' => 'Low',
            'category' => 'Follow-up',
        ]);

        return response()->json([
            'success' => true,
            'ticket_no' => $newTicketNo
        ]);
    }
}