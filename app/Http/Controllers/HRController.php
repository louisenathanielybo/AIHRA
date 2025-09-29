<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HrAnnouncement;
use App\Models\HrInbox;
// other models you need

class HRController extends Controller
{
    // Example method
    public function index()
    {
        $announcements = HrAnnouncement::all();
        return view('hr.hr_dashboard', compact('announcements'));
    }

    // other controller methods
}
