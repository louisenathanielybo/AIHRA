<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\HrAnnouncement;
use App\Models\HrInbox;
use App\Models\Query;
use Illuminate\Support\Str;
use App\Models\HrReply;

class HRController extends Controller
{
    /**
     * Display the HR dashboard.
     */
    public function index()
    {
        // ✅ Get all HR announcements
        $announcements = DB::table('announcements')
            ->orderBy('createdAt', 'desc')
            ->get();

        // ✅ Get all inbox tickets
        $inbox = HrInbox::all();

        // ✅ Get the currently logged-in user
        $user = Auth::user();

        // ✅ Pass variables to the view
        return view('hr.hr_dashboard', compact('announcements', 'inbox', 'user'));
    }

    /**
     * ✅ HR posts a new announcement.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'image'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $employeeNum = Auth::user()->employeeNum ?? null;

        $data = [
            'employeeNum' => $employeeNum,
            'title'       => $request->title,
            'description' => $request->content,
            'createdAt'   => now(),
            'isActive'    => 1,
        ];

        // ✅ Optional image upload (stored as binary)
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $data['image'] = file_get_contents($image->getRealPath());
        }

        DB::table('announcements')->insert($data);

        return back()->with('success', '📢 Announcement posted successfully!');
    }

    /**
     * Show the HR profile edit page.
     */
    public function editProfile()
    {
        $user = Auth::user(); // get the currently logged-in HR user
        return view('hr.hr_profile', compact('user'));
    }

    /**
     * Update the HR profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'about'           => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('profile_picture')) {
            $filename = time() . '.' . $request->profile_picture->extension();
            $request->profile_picture->move(public_path('uploads'), $filename);
            $user->profile_picture = $filename;
        }

        $user->about = $request->about;
        $user->save();

        return redirect()->route('hr.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * ✅ HR replies to a ticket.
     */
    

    /**
     * Return tickets as JSON.
     */
    public function ticketsJson()
    {
        return response()->json(HrInbox::orderBy('created_at', 'desc')->get());
    }

    public function getMessages($ticket_no)
{
    try {
        // Employee's original message from hr_inbox
        $inbox = HrInbox::where('ticket_no', $ticket_no)->first();

        // HR replies from hr_replies table
        $replies = HrReply::where('ticket_no', $ticket_no)
            ->orderBy('replied_at', 'asc')
            ->get();

        // Combine employee + HR messages
        $messages = collect();

        if ($inbox) {
            $messages->push([
                'sender' => 'employee',
                'message' => $inbox->message,
                'created_at' => $inbox->created_at,
            ]);
        }

        foreach ($replies as $reply) {
            // Determine sender: some entries in hr_replies may actually be
            // employee follow-ups (they were saved there by older flows).
            // Use replied_by compared to the original ticket's from_user to
            // infer the correct sender for rendering.
            $sender = 'hr';
            if ($inbox && isset($inbox->from_user) && $reply->replied_by == $inbox->from_user) {
                $sender = 'employee';
            }

            $text = $reply->hr_message;
            // Strip legacy stored prefixes like "Employee Follow-up: ..."
            if (preg_match('/Employee\s*-?\s*Follow-?up/i', $text)) {
                $text = preg_replace('/^.*?Employee\s*-?\s*Follow-?up:?\s*/i', '', $text);
            }

            $messages->push([
                'sender' => $sender,
                'message' => $text,
                'created_at' => $reply->replied_at,
            ]);
        }

        return response()->json($messages->sortBy('created_at')->values());

    } catch (\Exception $e) {
        \Log::error('Get Messages Error: ' . $e->getMessage());
        return response()->json([], 500);
    }
}


  /**
 * ✅ HR replies to a ticket - UPDATED to use only hr_replies table
 */
/**
 * ✅ HR replies to a ticket - UPDATED to save to chat_messages
 */
/**
 * ✅ HR replies to a ticket - FIXED timestamp issue
 */
/**
 * ✅ HR replies to a ticket - ONLY using hr_replies table
 */
public function reply(Request $request)
{
    $request->validate([
        'ticket_no' => 'required|string|exists:hr_inbox,ticket_no',
        'message' => 'required|string',
    ]);

    try {
        // 🆕 FIXED: Only save to hr_replies table
        HrReply::create([
            'ticket_no'  => $request->ticket_no,
            'hr_message' => $request->message,
            'replied_by' => Auth::check() ? Auth::user()->username : 'HR',
            'replied_at' => now(),
        ]);

        // 🆕 REMOVED: No chat_messages insertion

        // Update ticket status
        HrInbox::where('ticket_no', $request->ticket_no)
            ->update([
                'status' => 'Replied', 
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true, 
            'message' => 'Reply sent successfully!'
        ]);

    } catch (\Exception $e) {
        \Log::error('HR Reply Error: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'success' => false, 
            'message' => 'Failed to send reply: ' . $e->getMessage()
        ], 500);
    }
}
/**
 * ✅ Resolve/Close a ticket
 */
public function resolveTicket(Request $request)
{
    $request->validate([
        'ticket_no' => 'required|string|exists:hr_inbox,ticket_no',
    ]);

    try {
        // Update ticket status
        HrInbox::where('ticket_no', $request->ticket_no)
            ->update([
                'status' => 'Resolved', 
                'updated_at' => now()
            ]);

        return response()->json(['success' => true, 'message' => 'Ticket resolved successfully!']);
        
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Failed to resolve ticket'], 500);
    }
}
}
