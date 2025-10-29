<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AdminController extends Controller
{
    // Main Dashboard - UPDATED to include users
    public function index()
    {
        $admin = Auth::user();

        $kb = DB::table('knowledge_base')->orderBy('id', 'desc')->get();
        $announcements = DB::table('announcements')->orderBy('id', 'desc')->get();
        $feedback = DB::table('feedback')->orderBy('feedbackID', 'desc')->get();
        $flags = DB::table('flaggedresponse')->orderBy('flaggedID', 'desc')->get();
        
        // 🆕 ADD THIS LINE - Get all users for account management
        $users = DB::table('users')->orderBy('employeeNum', 'asc')->get();

        return view('admin.admin_dashboard', compact(
            'admin', 'kb', 'announcements', 'feedback', 'flags', 'users'
        ));
    }

    // Add new KB entry
    public function addKnowledge(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
        ]);

        DB::table('knowledge_base')->insert([
            'question' => $request->question,
            'answer' => $request->answer,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Knowledge entry added successfully!');
    }

    // Delete KB entry
    public function deleteKnowledge($id)
    {
        DB::table('knowledge_base')->where('id', $id)->delete();
        return redirect()->route('admin.dashboard')->with('success', 'Entry deleted successfully!');
    }

    // Add announcement
    public function addAnnouncement(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
        ]);

        DB::table('announcements')->insert([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Announcement published!');
    }

    // Delete announcement
    public function deleteAnnouncement($id)
    {
        DB::table('announcements')->where('id', $id)->delete();
        return redirect()->route('admin.dashboard')->with('success', 'Announcement deleted!');
    }

    // Update Profile (picture + about)
    public function updateProfile(Request $request)
    {
        $admin = Auth::user();

        $data = [];
        if ($request->hasFile('profile_pic')) {
            $file = $request->file('profile_pic');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);
            $data['profile_picture'] = $filename;
        }

        if ($request->filled('about')) {
            $data['about'] = $request->about;
        }

        if (!empty($data)) {
            DB::table('users')->where('employeeNum', $admin->employeeNum)->update($data);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Profile updated!');
    }

    // 🆕 CREATE ACCOUNT - Updated to work with your main dashboard
    public function createAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employeeNum' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'middleName' => 'nullable|string|max:255',
            'role' => 'required|in:employee,admin,hr,manager',
            'sex' => 'required|in:male,female,other',
            'age' => 'required|integer|min:18|max:75',
            'about' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,suspended'
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.dashboard')
                ->withErrors($validator)
                ->withInput()
                ->with('active_tab', 'create-account'); // Keep create account tab active
        }

        try {
            DB::table('users')->insert([
                'employeeNum' => $request->employeeNum,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'firstName' => $request->firstName,
                'lastName' => $request->lastName,
                'middleName' => $request->middleName,
                'role' => $request->role,
                'sex' => $request->sex,
                'age' => $request->age,
                'about' => $request->about,
                'status' => $request->status,
                'profile_picture' => 'default.png'
            ]);

            return redirect()->route('admin.dashboard')
                ->with('success', 'Account created successfully!')
                ->with('active_tab', 'create-account'); // Keep create account tab active
            
        } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')
                ->withErrors(['error' => 'Failed to create account: ' . $e->getMessage()])
                ->withInput()
                ->with('active_tab', 'create-account'); // Keep create account tab active
        }
    }

    // 🆕 UPDATE ACCOUNT - Updated to work with your main dashboard
    public function updateAccount(Request $request, $employeeNum)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,' . $employeeNum . ',employeeNum',
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'middleName' => 'nullable|string|max:255',
            'role' => 'required|in:employee,admin,hr,manager',
            'sex' => 'required|in:male,female,other',
            'age' => 'required|integer|min:18|max:65',
            'about' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,suspended'
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.dashboard')
                ->withErrors($validator)
                ->withInput()
                ->with('active_tab', 'create-account');
        }

        try {
            DB::table('users')->where('employeeNum', $employeeNum)->update([
                'email' => $request->email,
                'firstName' => $request->firstName,
                'lastName' => $request->lastName,
                'middleName' => $request->middleName,
                'role' => $request->role,
                'sex' => $request->sex,
                'age' => $request->age,
                'about' => $request->about,
                'status' => $request->status
            ]);

            return redirect()->route('admin.dashboard')
                ->with('success', 'Account updated successfully!')
                ->with('active_tab', 'create-account');
            
        } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')
                ->withErrors(['error' => 'Failed to update account: ' . $e->getMessage()])
                ->withInput()
                ->with('active_tab', 'create-account');
        }
    }

    // 🆕 DELETE ACCOUNT - Updated to work with your main dashboard
 // 🆕 UPDATED DELETE ACCOUNT METHOD WITH DEBUGGING
public function deleteAccount($employeeNum)
{
    // Prevent admin from deleting their own account
    if ($employeeNum == Auth::user()->employeeNum) {
        return redirect()->route('admin.dashboard')
            ->with('error', 'You cannot delete your own account.')
            ->with('active_tab', 'create-account');
    }

    try {
        \Log::info('Attempting to delete account', ['employeeNum' => $employeeNum]);
        
        // Check if user exists
        $user = DB::table('users')->where('employeeNum', $employeeNum)->first();
        
        if (!$user) {
            \Log::error('User not found for deletion', ['employeeNum' => $employeeNum]);
            return redirect()->route('admin.dashboard')
                ->with('error', 'User not found.')
                ->with('active_tab', 'create-account');
        }

        \Log::info('User found, proceeding with deletion', ['employeeNum' => $employeeNum, 'user' => $user]);

        // Delete the user
        $deleted = DB::table('users')->where('employeeNum', $employeeNum)->delete();
        
        \Log::info('Delete query executed', ['rows_affected' => $deleted]);

        if ($deleted) {
            \Log::info('Account deleted successfully', ['employeeNum' => $employeeNum]);
            return redirect()->route('admin.dashboard')
                ->with('success', 'Account deleted successfully!')
                ->with('active_tab', 'create-account');
        } else {
            \Log::error('Delete query returned 0 rows affected', ['employeeNum' => $employeeNum]);
            return redirect()->route('admin.dashboard')
                ->with('error', 'No account was deleted. User may not exist.')
                ->with('active_tab', 'create-account');
        }
        
    } catch (\Exception $e) {
        \Log::error('Failed to delete account: ' . $e->getMessage(), [
            'employeeNum' => $employeeNum,
            'exception' => $e
        ]);
        
        return redirect()->route('admin.dashboard')
            ->with('error', 'Failed to delete account: ' . $e->getMessage())
            ->with('active_tab', 'create-account');
    }
}

    // 🆕 QUICK EDIT ACCOUNT - Simple method to get user data for editing
    public function getAccount($employeeNum)
    {
        $user = DB::table('users')->where('employeeNum', $employeeNum)->first();
        
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json($user);
    }
}