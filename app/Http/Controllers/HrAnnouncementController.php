<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HrAnnouncementController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:50',
                'description' => 'required|string',
                'image' => 'nullable|image|max:10240', // optional image up to 10MB
                'expiry_date' => 'nullable|date|after_or_equal:today',
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

            // Return JSON for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Announcement posted successfully!'
                ]);
            }

            return redirect()->back()->with('success', 'Announcement posted successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->validator->errors()->first()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Announcement store error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create announcement: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to create announcement');
        }
    }
}
