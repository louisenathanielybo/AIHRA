<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HrAnnouncementController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:50',
            'description' => 'required|string',
            'image' => 'nullable|image|max:10240', // optional image up to 10MB
            'expiry_date' => 'required|date|after_or_equal:today',
        ]);

        $employeeNum = Auth::user()->employeeNum ?? null;

        $imageData = null;
        if ($request->hasFile('image')) {
            $imageData = file_get_contents($request->file('image')->getRealPath());
        }

        DB::table('announcements')->insert([
            'employeeNum' => $employeeNum,
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imageData,
            'createdAt' => now(),
            'isActive' => 1,
            'expiry_date' => $request->expiry_date,
        ]);

        return redirect()->back()->with('success', 'Announcement posted successfully!');
    }
}
