<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'subject' => 'required|string|max:255',
            'suggestion' => 'nullable|string',
        ]);

        Feedback::create([
            'feedbackID' => Str::uuid(), // generates a unique ID like "5f8a3c9e..."
            'employeeNum' => Auth::user()->employeeNum,
            'queryID' => null, // if not used yet
            'rating' => $request->rating,
            'subject' => $request->subject,
            'suggestion' => $request->suggestion,
            'timeStamp' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your feedback!'
        ]);
    }
}
