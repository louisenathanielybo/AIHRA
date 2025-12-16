<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;

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
            'employeeNum' => Auth::user()->employeeNum,
            'queryID' => null,
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
