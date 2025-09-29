<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // needed for Auth::user()
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

        // Get all inbox tickets (no filter, since table has no HR-specific column)
        $inbox = HrInbox::all();

        // Get the currently logged-in user
        $user = Auth::user();

        // Pass variables to the view
        return view('hr.hr_dashboard', compact('announcements', 'inbox', 'user'));
    }


    // other methods
}
