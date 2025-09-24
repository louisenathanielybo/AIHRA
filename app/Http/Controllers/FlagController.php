<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FlaggedResponse;
use Illuminate\Support\Facades\Auth;

class FlagController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'queryID' => 'required|string',
            'reason' => 'required|string',
            'details' => 'nullable|string',
        ]);

        FlaggedResponse::create([
            'employeeNum' => Auth::user()->employeeNum,
            'queryID' => $request->queryID,
            'reasonID' => $request->reason,
            'timeStamp' => now(),
            'status' => 'Pending',
        ]);

        return back()->with('flag_success', true);
    }
}
