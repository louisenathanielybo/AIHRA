<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HrAnnouncementController extends Controller
{
    /**
     * Minimal stub to accept announcement POSTs and avoid reflection errors.
     * This keeps behavior safe and returns a JSON success for now.
     */
    public function store(Request $request)
    {
        // Minimal validation (optional)
        // $request->validate(['title' => 'required', 'content' => 'required']);

        // For now, just return success so route:list and runtime references succeed.
        return response()->json(['success' => true, 'message' => 'Announcement stub received']);
    }
}
