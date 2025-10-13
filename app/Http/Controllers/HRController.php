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
    // Employee’s original message
    $inbox = HrInbox::where('ticket_no', $ticket_no)->first();

    // HR replies (multiple possible)
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

    foreach ($replies as $r) {
        $messages->push([
            'sender' => 'hr',
            'message' => $r->hr_message,
            'created_at' => $r->replied_at,
        ]);
    }

    return response()->json($messages->sortBy('created_at')->values());
}

   public function reply(Request $request)
{
    $request->validate([
        'ticket_no' => 'required|string|exists:hr_inbox,ticket_no',
        'message' => 'required|string',
    ]);

    // Save to hr_replies for record
    \App\Models\HrReply::create([
        'ticket_no'  => $request->ticket_no,
        'hr_message' => $request->message,
        'replied_by' => Auth::check() ? Auth::user()->username : 'HR',
        'replied_at' => now(),
    ]);

    // Also insert directly into chat_messages (like a bot reply)
    DB::table('chat_messages')->insert([
        'ticket_no' => $request->ticket_no,
        'sender'    => 'hr',
        'message'   => $request->message,
        'created_at' => now(),
    ]);

    // Update status
    \App\Models\HrInbox::where('ticket_no', $request->ticket_no)
        ->update(['status' => 'Replied', 'updated_at' => now()]);

    return response()->json(['success' => true, 'message' => 'Reply sent and displayed in chat.']);
}

}
