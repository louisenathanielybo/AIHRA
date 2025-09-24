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
            'suggestion' => 'nullable|string',
        ]);

        Feedback::create([
            'employeeNum' => Auth::user()->employeeNum,
            'rating' => $request->rating,
            'suggestion' => $request->suggestion,
            'timeStamp' => now(),
        ]);

        return back()->with('feedback_success', true);
    }
}
