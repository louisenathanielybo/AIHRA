<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    // Main Dashboard
    public function index()
    {
        $admin = Auth::user();

        $kb = DB::table('knowledge_base')->orderBy('id', 'desc')->get();
        $announcements = DB::table('announcements')->orderBy('id', 'desc')->get();
        $feedback = DB::table('feedback')->orderBy('feedbackID', 'desc')->get();
        $flags = DB::table('flaggedresponse')->orderBy('flaggedID', 'desc')->get();

        return view('admin.admin_dashboard', compact('admin', 'kb', 'announcements', 'feedback', 'flags'));
    }

    // Add new KB entry
    public function addKnowledge(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
        ]);

        DB::table('knowledge_base')->insert([
            'question' => $request->question,
            'answer' => $request->answer,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Knowledge entry added successfully!');
    }

    // Delete KB entry
    public function deleteKnowledge($id)
    {
        DB::table('knowledge_base')->where('id', $id)->delete();
        return redirect()->route('admin.dashboard')->with('success', 'Entry deleted successfully!');
    }

    // Add announcement
    public function addAnnouncement(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
        ]);

        DB::table('announcements')->insert([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Announcement published!');
    }

    // Delete announcement
    public function deleteAnnouncement($id)
    {
        DB::table('announcements')->where('id', $id)->delete();
        return redirect()->route('admin.dashboard')->with('success', 'Announcement deleted!');
    }

    // Update Profile (picture + about)
    public function updateProfile(Request $request)
    {
        $admin = Auth::user();

        $data = [];
        if ($request->hasFile('profile_pic')) {
            $file = $request->file('profile_pic');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);
            $data['profile_picture'] = $filename;
        }

        if ($request->filled('about')) {
            $data['about'] = $request->about;
        }

        if (!empty($data)) {
            DB::table('users')->where('employeeNum', $admin->employeeNum)->update($data);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Profile updated!');
    }
}
