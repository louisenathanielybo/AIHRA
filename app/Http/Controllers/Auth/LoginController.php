<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

   public function login(Request $request)
{
    $request->validate([
        'employeeNum' => 'required|string',
        'password' => 'required|string',
    ]);

    $credentials = $request->only('employeeNum', 'password');

    // Find user (plain-text password as you requested)
    $user = \App\Models\User::where('employeeNum', $credentials['employeeNum'])
             ->where('password', $credentials['password'])
             ->where('status', 'Active')
             ->first();

    if (! $user) {
        // Log for debugging without halting execution
        Log::info('Login failed', ['employeeNum' => $credentials['employeeNum']]);
        return back()->with('error', 'Invalid credentials.');
    }

    // Log success for debugging (non-blocking)
    Log::info('Login success', ['user_id' => $user->id, 'role' => $user->role]);

    // Perform login
    Auth::login($user);
    $request->session()->regenerate();

    // Redirect depending on role (case-insensitive)
    $role = strtolower($user->role ?? '');

    if ($role === 'admin') return redirect('/admin');
    if ($role === 'hr') return redirect('/hr');

    return redirect('/employee');
}


    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
