<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Models\HrAnnouncement;
use App\Models\HrInbox;

class HRController extends Controller
{
    /**
     * Display the HR dashboard.
     */
    public function index()
    {
        // Get all HR announcements
        $announcements = HrAnnouncement::all();

        // Get all inbox tickets
        $inbox = HrInbox::all();

        // Get the currently logged-in user
        $user = Auth::user();

        // Pass variables to the view
        return view('hr.hr_dashboard', compact('announcements', 'inbox', 'user'));
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

        // ✅ Validate input
        $request->validate([
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'about' => 'nullable|string|max:500',
        ]);

        // ✅ Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $filename = time() . '.' . $request->profile_picture->extension();
            $request->profile_picture->move(public_path('uploads'), $filename);
            $user->profile_picture = $filename;
        }

        // ✅ Update about
        $user->about = $request->about;

        $user->save();

        return redirect()->route('hr.profile')->with('success', 'Profile updated successfully!');
    }
}
