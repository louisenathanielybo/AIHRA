<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\HrAnnouncement;
use App\Models\HrInbox;
use App\Models\Query;
use Illuminate\Support\Str;

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
     * HR reply to a ticket (keeps chatbot log updated).
     */
    public function sendReply(Request $request)
    {
        $request->validate([
            'ticket_no' => 'required|string',
            'message'   => 'required|string',
        ]);

        $ticket = HrInbox::where('ticket_no', $request->ticket_no)->first();

        if (!$ticket) {
            return back()->with('error', 'Ticket not found.');
        }

        // ✅ Create a corresponding Query entry so it shows in the chatbot
        Query::create([
            'queryID'         => Str::uuid(),
            'employeeNum'     => $ticket->from_user,
            'question'        => '[HR Reply]',
            'response'        => $request->message,
            'confidenceScore' => 1.0,
            'queryType'       => 'ManualReply',
            'questionTime'    => now(),
            'responseTime'    => now(),
            'isEscalated'     => false,
            'handledBy'       => 'HR',
        ]);

        // ✅ Update ticket status
        $ticket->update([
            'status' => 'Resolved',
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Reply sent successfully and recorded in the chat.');
    }

    /**
     * 🟩 Show the Announcements section (for HR sidebar).
     */
    public function announcements()
    {
        $announcements = DB::table('announcements')
            ->orderBy('createdAt', 'desc')
            ->get();

        $user = Auth::user();
        return view('hr.announcements', compact('announcements', 'user'));
    }

    /**
     * 🟩 Show the Account section (for HR sidebar).
     */
    public function account()
    {
        $user = Auth::user();
        return view('hr.account', compact('user'));
    }
}
