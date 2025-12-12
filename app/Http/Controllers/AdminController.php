<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $admin = Auth::user();

        // Existing code...
        $kb = DB::table('knowledge_base')->orderBy('id', 'desc')->get();
        $announcements = DB::table('announcements')->orderBy('id', 'desc')->get();
        $feedback = DB::table('feedback')->orderBy('feedbackID', 'desc')->get();
        $flags = DB::table('flaggedresponse')->orderBy('flaggedID', 'desc')->get();
        
        // 🆕 GET HR_INBOX DATA FOR CHATBOT TICKETS
        $tickets = DB::table('hr_inbox')
            ->select('*')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Calculate ticket KPIs
        $totalTickets = $tickets->count();
        $unresolvedTickets = $tickets->whereIn('status', ['Open', 'Replied', 'Waiting for HR'])->count();
        $resolvedTickets = $tickets->where('status', 'Resolved')->count();
        $expiredTickets = $tickets->where('is_expired', true)->whereIn('status', ['Open', 'Replied', 'Waiting for HR'])->count();

        // 🆕 NEW: Get data for dashboard KPIs
        $activeUsers = DB::table('users')->where('status', 'Active')->count();
        $totalInteractions = DB::table('queries')->count();
        $escalatedQueries = $totalTickets; // Using tickets as escalated queries
        
        // 🆕 NEW: Get data for resolution chart
        $resolvedQueries = DB::table('queries')->where('isEscalated', 0)->count();
        $pendingQueries = $unresolvedTickets; // Unresolved tickets are pending
        $escalatedCount = $totalTickets; // All tickets are escalated queries
        
        // 🆕 NEW: Get feedback data for feedback tab
        $feedbackData = DB::table('feedback')
            ->join('users', 'feedback.employeeNum', '=', 'users.employeeNum')
            ->select('feedback.*', 'users.firstName', 'users.lastName')
            ->orderBy('feedback.timeStamp', 'desc')
            ->get()
            ->map(function ($item, $index) {
                $item->displayID = $index + 1;
                return $item;
            });

        // 🆕 NEW: Get data for performance tab
        $recentInteractions = DB::table('queries')
            ->join('users', 'queries.employeeNum', '=', 'users.employeeNum')
            ->select('queries.*', 'users.firstName', 'users.lastName')
            ->orderBy('queries.questionTime', 'desc')
            ->limit(10)
            ->get();

        $flaggedResponses = DB::table('flaggedresponse')
            ->join('users', 'flaggedresponse.employeeNum', '=', 'users.employeeNum')
            ->join('queries', 'flaggedresponse.queryID', '=', 'queries.queryID')
            ->select('flaggedresponse.*', 'users.firstName', 'users.lastName', 'queries.question')
            ->orderBy('flaggedresponse.timeStamp', 'desc')
            ->get()
            ->map(function ($item, $index) {
                $item->displayID = $index + 1; // Add sequential display ID
                return $item;
            });

        // Get users with pagination and search
        $search = request('search', '');
        $users = DB::table('users')
            ->select('employeeNum', 'email', 'firstName', 'lastName', 'middleName', 'role', 'sex', 'age', 'profile_picture', 'about', 'status')
            ->when($search, function($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('employeeNum', 'like', "%{$search}%")
                      ->orWhere('firstName', 'like', "%{$search}%")
                      ->orWhere('lastName', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('role', 'like', "%{$search}%");
                });
            })
            ->orderBy('employeeNum', 'asc')
            ->paginate(10);

        // Get active tab from URL parameter, session, or default to dashboard
        $active_tab = $request->get('active_tab', session('active_tab', 'dashboard'));
        
        // Store in session for form submissions
        session(['active_tab' => $active_tab]);

        return view('admin.admin_dashboard', compact(
            'admin', 'kb', 'announcements', 'feedback', 'flags', 'users', 'search', 
            'active_tab', 'tickets', 'totalTickets', 'unresolvedTickets', 'resolvedTickets', 'expiredTickets',
            'activeUsers', 'totalInteractions', 'escalatedQueries',
            'resolvedQueries', 'pendingQueries', 'escalatedCount',
            'feedbackData', 'recentInteractions', 'flaggedResponses'
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

    // 🆕 CREATE ACCOUNT - Updated to work with users table
    public function createAccount(Request $request)
    {
        $minBirth = now()->subYears(65)->format('Y-m-d'); // Earliest acceptable (oldest user 65)
        $maxBirth = now()->subYears(18)->format('Y-m-d'); // Latest acceptable (youngest user 18)
        $validator = Validator::make($request->all(), [
            'employeeNum' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'middleName' => 'nullable|string|max:255',
            'role' => 'required|in:Employee,Admin,HR',
            'sex' => 'required|in:Male,Female',
            'dob' => 'required|date|after_or_equal:'.$minBirth.'|before_or_equal:'.$maxBirth,
            'about' => 'nullable|string|max:255',
            'status' => 'required|in:Active,Deactivated'
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.dashboard')
                ->withErrors($validator)
                ->withInput()
                ->with('active_tab', 'account-management');
        }

        try {
            // Compute age from dob
            $birthDate = \Carbon\Carbon::parse($request->dob);
            $age = $birthDate->diffInYears(now());
            DB::table('users')->insert([
                'employeeNum' => $request->employeeNum,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'firstName' => $request->firstName,
                'lastName' => $request->lastName,
                'middleName' => $request->middleName,
                'role' => $request->role,
                'sex' => $request->sex,
                'age' => $age,
                'dob' => $birthDate->format('Y-m-d'),
                'about' => $request->about,
                'status' => $request->status,
                'profile_picture' => 'default.png'
            ]);

            return redirect()->route('admin.dashboard')
                ->with('success', 'Account created successfully!')
                ->with('active_tab', 'account-management');
            
        } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')
                ->withErrors(['error' => 'Failed to create account: ' . $e->getMessage()])
                ->withInput()
                ->with('active_tab', 'account-management');
        }
    }

    public function updateAccount(Request $request, $employeeNum)
    {
        \Log::info('Update account request:', [
            'employeeNum' => $employeeNum, 
            'data' => $request->all()
        ]);
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,' . $employeeNum . ',employeeNum',
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'middleName' => 'nullable|string|max:255',
            'role' => 'required|in:Employee,Admin,HR',
            'sex' => 'required|in:Male,Female',
            'dob' => 'nullable|date|after_or_equal:'.now()->subYears(65)->format('Y-m-d').'|before_or_equal:'.now()->subYears(18)->format('Y-m-d'),
            'about' => 'nullable|string|max:255',
            'status' => 'required|in:Active,Deactivated'
        ]);

        if ($validator->fails()) {
            \Log::warning('Validation failed:', ['errors' => $validator->errors()]);
            return redirect()->route('admin.dashboard')
                ->withErrors($validator)
                ->withInput()
                ->with('active_tab', 'account-management');
        }

        try {
            $updatePayload = [
                'email' => $request->email,
                'firstName' => $request->firstName,
                'lastName' => $request->lastName,
                'middleName' => $request->middleName,
                'role' => $request->role,
                'sex' => $request->sex,
                'about' => $request->about,
                'status' => $request->status
            ];
            if ($request->filled('dob')) {
                $birthDate = \Carbon\Carbon::parse($request->dob);
                $updatePayload['age'] = $birthDate->diffInYears(now());
                $updatePayload['dob'] = $birthDate->format('Y-m-d');
            }
            $updated = DB::table('users')->where('employeeNum', $employeeNum)->update($updatePayload);

            if ($updated) {
                \Log::info('Account updated successfully:', ['employeeNum' => $employeeNum]);
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Account updated successfully!')
                    ->with('active_tab', 'account-management');
            } else {
                \Log::warning('No rows updated:', ['employeeNum' => $employeeNum]);
                return redirect()->route('admin.dashboard')
                    ->with('error', 'No changes were made or user not found.')
                    ->with('active_tab', 'account-management');
            }
            
        } catch (\Exception $e) {
            \Log::error('Failed to update account:', [
                'employeeNum' => $employeeNum,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('admin.dashboard')
                ->with('error', 'Failed to update account: ' . $e->getMessage())
                ->withInput()
                ->with('active_tab', 'account-management');
        }
    }

    // 🆕 DELETE ACCOUNT - Updated to work with users table
    public function deleteAccount($employeeNum)
    {
        \Log::info('Deleting account:', ['employeeNum' => $employeeNum]);
        
        // Prevent admin from deleting their own account
        if ($employeeNum == Auth::user()->employeeNum) {
            \Log::warning('Attempt to delete own account:', ['employeeNum' => $employeeNum]);
            return redirect()->route('admin.dashboard')
                ->with('error', 'You cannot delete your own account.')
                ->with('active_tab', 'account-management');
        }

        try {
            $user = DB::table('users')->where('employeeNum', $employeeNum)->first();
            
            if (!$user) {
                \Log::warning('User not found for deletion:', ['employeeNum' => $employeeNum]);
                return redirect()->route('admin.dashboard')
                    ->with('error', 'User not found.')
                    ->with('active_tab', 'account-management');
            }

            $deleted = DB::table('users')->where('employeeNum', $employeeNum)->delete();
            
            if ($deleted) {
                \Log::info('Account deleted successfully:', ['employeeNum' => $employeeNum]);
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Account deleted successfully!')
                    ->with('active_tab', 'account-management');
            } else {
                \Log::error('Delete query returned 0 rows affected:', ['employeeNum' => $employeeNum]);
                return redirect()->route('admin.dashboard')
                    ->with('error', 'No account was deleted. User may not exist.')
                    ->with('active_tab', 'account-management');
            }
            
        } catch (\Exception $e) {
            \Log::error('Failed to delete account:', [
                'employeeNum' => $employeeNum,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('admin.dashboard')
                ->with('error', 'Failed to delete account: ' . $e->getMessage())
                ->with('active_tab', 'account-management');
        }
    }

    // 🆕 GET ACCOUNT DATA for editing - with better error handling
    public function getAccount($employeeNum)
    {
        \Log::info('Fetching account data for:', ['employeeNum' => $employeeNum]);
        
        try {
            $user = DB::table('users')
                ->where('employeeNum', $employeeNum)
                ->select('employeeNum', 'email', 'firstName', 'lastName', 'middleName', 'role', 'sex', 'age', 'dob', 'profile_picture', 'about', 'status')
                ->first();
            
            if (!$user) {
                \Log::warning('User not found:', ['employeeNum' => $employeeNum]);
                return response()->json(['error' => 'User not found'], 404);
            }

            \Log::info('User found:', ['employeeNum' => $employeeNum]);
            // Ensure dob is always present, even if null
            if (!property_exists($user, 'dob')) {
                $user->dob = null;
            }
            $user->dob = $user->dob ?? null;
            return response()->json($user);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching user:', [
                'employeeNum' => $employeeNum,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    // 🆕 RESET PASSWORD
    public function resetPassword(Request $request, $employeeNum)
    {
        \Log::info('Resetting password for:', ['employeeNum' => $employeeNum]);
        
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            \Log::warning('Password validation failed:', ['errors' => $validator->errors()]);
            return redirect()->route('admin.dashboard')
                ->withErrors($validator)
                ->with('active_tab', 'account-management');
        }

        try {
            $updated = DB::table('users')->where('employeeNum', $employeeNum)->update([
                'password' => Hash::make($request->password)
            ]);

            if ($updated) {
                \Log::info('Password reset successfully:', ['employeeNum' => $employeeNum]);
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Password reset successfully!')
                    ->with('active_tab', 'account-management');
            } else {
                \Log::warning('No rows updated for password reset:', ['employeeNum' => $employeeNum]);
                return redirect()->route('admin.dashboard')
                    ->with('error', 'User not found or no changes made.')
                    ->with('active_tab', 'account-management');
            }
            
        } catch (\Exception $e) {
            \Log::error('Failed to reset password:', [
                'employeeNum' => $employeeNum,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('admin.dashboard')
                ->with('error', 'Failed to reset password: ' . $e->getMessage())
                ->with('active_tab', 'account-management');
        }
    }

    // 🆕 EXPORT CSV FUNCTIONALITY
    public function exportAccounts()
    {
        $users = DB::table('users')
            ->select('employeeNum', 'email', 'firstName', 'lastName', 'middleName', 'role', 'sex', 'age', 'status')
            ->orderBy('employeeNum', 'asc')
            ->get();

        $fileName = 'accounts_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'Employee Number',
                'Email',
                'First Name', 
                'Last Name',
                'Middle Name',
                'Role',
                'Gender',
                'Age',
                'Status'
            ]);

            // Add data rows
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->employeeNum,
                    $user->email,
                    $user->firstName,
                    $user->lastName,
                    $user->middleName ?? '',
                    $user->role,
                    $user->sex,
                    $user->age,
                    $user->status
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function getTickets()
    {
        try {
            $tickets = DB::table('hr_inbox')
                ->select('*')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'tickets' => $tickets,
                'total' => $tickets->count(),
                'unresolved' => $tickets->whereIn('status', ['Open', 'Replied', 'Waiting for HR'])->count(),
                'resolved' => $tickets->where('status', 'Resolved')->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch tickets: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTicketDetails($ticketId)
    {
        try {
            \Log::info('Fetching ticket details', ['ticketId' => $ticketId]);
            
            // Clean the ticket ID
            $ticketId = trim($ticketId);
            
            $ticket = DB::table('hr_inbox')
                ->where('ticket_no', $ticketId)
                ->orWhere('id', $ticketId)
                ->first();

            \Log::info('Ticket query result', ['found' => !is_null($ticket), 'ticketId' => $ticketId]);

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found with ID: ' . $ticketId
                ], 404);
            }

            // Ensure all fields are properly set with defaults
            $ticketData = [
                'ticket_no' => $ticket->ticket_no ?? $ticketId,
                'from_user' => $ticket->from_user ?? 'Unknown',
                'message' => $ticket->message ?? 'No message',
                'priority' => $ticket->priority ?? 'medium',
                'status' => $ticket->status ?? 'Open',
                'category' => $ticket->category ?? 'General',
                'intent' => $ticket->intent ?? 'N/A',
                'confidence' => $ticket->confidence ?? 0.0,
                'created_at' => $ticket->created_at ?? now(),
                'updated_at' => $ticket->updated_at ?? now(),
            ];

            return response()->json([
                'success' => true,
                'ticket' => $ticketData
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch ticket details', [
                'ticketId' => $ticketId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch ticket details: ' . $e->getMessage()
            ], 500);
        }
    }
}