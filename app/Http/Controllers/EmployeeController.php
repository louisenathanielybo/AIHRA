<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function index()
    {
        // Fetch announcements from DB
        $announcements = DB::table('announcements')
            ->orderBy('id', 'desc')
            ->get();

        // Pass to view
        return view('employee.empl_dashboard', compact('announcements'));
    }
}
