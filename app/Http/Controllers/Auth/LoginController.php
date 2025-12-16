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

    // 🆕 COMPLETELY CUSTOM LOGIN - No Auth::attempt()
    $user = \App\Models\User::where('employeeNum', $credentials['employeeNum'])->first();

    if ($user) {
        $passwordMatches = false;
        if ($user->password === $credentials['password']) {
            $passwordMatches = true;
        } else if (password_verify($credentials['password'], $user->password)) {
            $passwordMatches = true;
        } else if (md5($credentials['password']) === $user->password) {
            $passwordMatches = true;
        }

        if ($passwordMatches) {
            if ($user->status !== 'Active') {
                return back()->with('error', 'Account Currently Deactivated');
            }
            Auth::login($user);
            $request->session()->regenerate();
            if ($user->password === $credentials['password'] || md5($credentials['password']) === $user->password) {
                $user->update(['password' => bcrypt($credentials['password'])]);
            }
            return $this->redirectToDashboard($user);
        }
    }
    return back()->with('error', 'Invalid credentials.');
}

    // 🆕 Helper method for redirects
    private function redirectToDashboard($user)
    {
        $role = strtolower($user->role ?? '');
        
        if ($role === 'admin') return redirect('/admin/dashboard');
        if ($role === 'hr') return redirect('/hr/dashboard');
        return redirect('/employee/dashboard');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}