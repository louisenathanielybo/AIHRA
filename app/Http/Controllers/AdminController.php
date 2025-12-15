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

        // Get sort parameters
        $ticketsSort = $request->get('tickets_sort', 'created_at');
        $ticketsDir = $request->get('tickets_dir', 'desc');
        $feedbackSort = $request->get('feedback_sort', 'timeStamp');
        $feedbackDir = $request->get('feedback_dir', 'desc');
        $interactionsSort = $request->get('interactions_sort', 'questionTime');
        $interactionsDir = $request->get('interactions_dir', 'desc');
        $flagsSort = $request->get('flags_sort', 'timeStamp');
        $flagsDir = $request->get('flags_dir', 'desc');

        // Delete expired announcements
        DB::table('announcements')
            ->where('expiry_date', '<', now()->toDateString())
            ->whereNotNull('expiry_date')
            ->delete();

        // Existing code...
        $kb = DB::table('knowledge_base')->orderBy('id', 'desc')->get();
        $announcements = DB::table('announcements')->orderBy('id', 'desc')->get();
        $feedback = DB::table('feedback')->orderBy('feedbackID', 'desc')->get();
        $flags = DB::table('flaggedresponse')->orderBy('flaggedID', 'desc')->get();
        // Get flagged count for KPI
        $flaggedCount = DB::table('flaggedresponse')->count();
        
        // 🆕 GET HR_INBOX DATA FOR CHATBOT TICKETS - all data for client-side pagination
        $ticketsForKPI = DB::table('hr_inbox')->get(); // For KPI calculations
        $ticketsSortColumn = in_array($ticketsSort, ['ticket_no', 'from_user', 'priority', 'status', 'created_at']) ? $ticketsSort : 'created_at';
        $tickets = DB::table('hr_inbox')
            ->select('*')
            ->orderBy($ticketsSortColumn, $ticketsDir)
            ->get();
        
        // Get all tickets data for JavaScript (date, priority, status)
        $allTicketsData = DB::table('hr_inbox')
            ->select(
                DB::raw('DATE(created_at) as ticket_date'),
                'priority',
                'status'
            )
            ->get();
        
        // Calculate ticket KPIs
        $totalTickets = $ticketsForKPI->count();
        $unresolvedTickets = $ticketsForKPI->whereIn('status', ['Open', 'Replied', 'Waiting for HR'])->count();
        $resolvedTickets = $ticketsForKPI->where('status', 'Resolved')->count();
        $expiredTickets = $ticketsForKPI->where('is_expired', true)->whereIn('status', ['Open', 'Replied', 'Waiting for HR'])->count();

        // 🆕 Calculate week-over-week changes for ticket statistics
        $lastWeekStart = now()->subWeek()->startOfWeek();
        $lastWeekEnd = now()->subWeek()->endOfWeek();
        $thisWeekStart = now()->startOfWeek();
        
        $lastWeekTickets = DB::table('hr_inbox')
            ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
            ->get();
        $thisWeekTickets = DB::table('hr_inbox')
            ->where('created_at', '>=', $thisWeekStart)
            ->get();
            
        $lastWeekTotal = $lastWeekTickets->count();
        $thisWeekTotal = $thisWeekTickets->count();
        $lastWeekUnresolved = $lastWeekTickets->whereIn('status', ['Open', 'Replied', 'Waiting for HR'])->count();
        $thisWeekUnresolved = $thisWeekTickets->whereIn('status', ['Open', 'Replied', 'Waiting for HR'])->count();
        $lastWeekResolved = $lastWeekTickets->where('status', 'Resolved')->count();
        $thisWeekResolved = $thisWeekTickets->where('status', 'Resolved')->count();
        
        $totalTicketsChange = $thisWeekTotal - $lastWeekTotal;
        $unresolvedTicketsChange = $thisWeekUnresolved - $lastWeekUnresolved;
        $resolvedTicketsChange = $thisWeekResolved - $lastWeekResolved;

        // 🆕 NEW: Get data for dashboard KPIs
        $activeUsers = DB::table('users')->where('status', 'Active')->count();
        $totalInteractions = DB::table('queries')->count();
        $escalatedQueries = $totalTickets; // All tickets are escalated queries
        $pendingQueries = $ticketsForKPI->whereIn('status', ['Open', 'Waiting for HR'])->count(); // Pending = tickets excluding Replied and Resolved
        $resolvedQueries = $totalInteractions - $pendingQueries; // Resolved = Total - Pending
        
        // 🆕 NEW: Get data for resolution chart
        $escalatedCount = $escalatedQueries;
        
        // 🆕 NEW: Get feedback data for feedback tab with pagination
        $feedbackSortColumn = in_array($feedbackSort, ['feedbackID', 'rating', 'timeStamp']) ? $feedbackSort : 'timeStamp';
        

        $feedbackData = DB::table('feedback')
            ->join('users', 'feedback.employeeNum', '=', 'users.employeeNum')
            ->select('feedback.*', 'users.firstName', 'users.lastName')
            ->orderBy('feedback.' . $feedbackSortColumn, $feedbackDir)
            ->get()
            ->map(function($feedback) {
                $feedback->formatted_date = \Carbon\Carbon::parse($feedback->timeStamp)->format('Y-m-d');
                return $feedback;
            });

        // 🆕 NEW: Get data for performance tab - all interactions for client-side pagination
        $interactionsSortColumn = in_array($interactionsSort, ['question', 'questionTime', 'isEscalated']) ? $interactionsSort : 'questionTime';
        $recentInteractions = DB::table('queries')
            ->join('users', 'queries.employeeNum', '=', 'users.employeeNum')
            ->select('queries.*', 'users.firstName', 'users.lastName', 
                DB::raw('TIMESTAMPDIFF(MICROSECOND, queries.questionTime, IFNULL(queries.responseTime, queries.questionTime)) / 1000000.0 as response_time_seconds'))
            ->orderBy('queries.' . $interactionsSortColumn, $interactionsDir)
            ->get();

        // Get all interactions data for JavaScript (date + response time only)
        $allInteractionsData = DB::table('queries')
            ->select(
                DB::raw('DATE(questionTime) as query_date'),
                DB::raw('TIMESTAMPDIFF(MICROSECOND, questionTime, IFNULL(responseTime, questionTime)) / 1000000.0 as response_time_seconds')
            )
            ->get();

        $flagsSortColumn = in_array($flagsSort, ['flaggedID', 'reasonID', 'timeStamp', 'status']) ? $flagsSort : 'timeStamp';
        
        $flaggedResponses = DB::table('flaggedresponse')
            ->join('users', 'flaggedresponse.employeeNum', '=', 'users.employeeNum')
            ->join('queries', 'flaggedresponse.queryID', '=', 'queries.queryID')
            ->select('flaggedresponse.*', 'users.firstName', 'users.lastName', 'queries.question')
            ->orderBy('flaggedresponse.' . $flagsSortColumn, $flagsDir)
            ->get();

        // Calculate average response time from all queries
        $avgResponseTime = DB::table('queries')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(MICROSECOND, questionTime, IFNULL(responseTime, questionTime)) / 1000000.0) as avg_seconds'))
            ->value('avg_seconds');

        // Get users with pagination and search (exclude archived accounts)
        $search = request('search', '');
        $users = DB::table('users')
            ->select('employeeNum', 'email', 'firstName', 'lastName', 'middleName', 'role', 'sex', 'age', 'profile_picture', 'about', 'status', 'dob')
            ->where('is_archived', false) // Exclude archived accounts
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
            ->paginate(20, ['*'], 'accounts_page');

        // 🆕 Get Most Asked Topics from queries table
        $mostAskedTopics = DB::table('queries')
            ->select('question')
            ->whereNotNull('question')
            ->where('question', '!=', '')
            ->get()
            ->map(function($query) {
                // Extract key topics/keywords from questions
                $question = strtolower($query->question);
                
                // Define topic categories and their keywords
                $topicMap = [
                    'Leave' => ['leave', 'vacation', 'sick leave', 'time off', 'absence', 'vl', 'sl'],
                    'Benefits' => ['benefit', 'insurance', 'health', 'dental', 'hmo', 'allowance'],
                    'Payroll' => ['payroll', 'salary', 'pay', 'wage', 'compensation', '13th month', 'bonus'],
                    'Promotion' => ['promotion', 'ranking', 'career', 'advancement', 'raise'],
                    'Training' => ['training', 'seminar', 'workshop', 'development', 'course'],
                    'Employment' => ['employment', 'hiring', 'contract', 'resignation', 'termination'],
                    'Policy' => ['policy', 'procedure', 'guideline', 'rule', 'regulation'],
                    'HR Request' => ['request', 'form', 'document', 'certificate', 'clearance']
                ];
                
                foreach ($topicMap as $topic => $keywords) {
                    foreach ($keywords as $keyword) {
                        if (strpos($question, $keyword) !== false) {
                            return $topic;
                        }
                    }
                }
                
                return 'General';
            })
            ->countBy()
            ->sortDesc()
            ->take(5)
            ->map(function($count, $topic) {
                return ['topic' => $topic, 'count' => $count];
            })
            ->values();

        // Get active tab from URL parameter, session, or default to dashboard
        $active_tab = $request->get('active_tab', 'dashboard');
        
        // Store in session for form submissions
        session(['active_tab' => $active_tab]);

        return view('admin.admin_dashboard', compact(
            'admin', 'kb', 'announcements', 'feedback', 'flags', 'users', 'search', 
            'active_tab', 'tickets', 'totalTickets', 'unresolvedTickets', 'resolvedTickets', 'expiredTickets',
            'totalTicketsChange', 'unresolvedTicketsChange', 'resolvedTicketsChange',
            'activeUsers', 'totalInteractions', 'escalatedQueries',
            'resolvedQueries', 'pendingQueries', 'escalatedCount',
            'feedbackData', 'recentInteractions', 'flaggedResponses', 'mostAskedTopics', 'avgResponseTime',
            'allInteractionsData', 'allTicketsData',
            'ticketsSort', 'ticketsDir', 'feedbackSort', 'feedbackDir', 
            'interactionsSort', 'interactionsDir', 'flagsSort', 'flagsDir',
            'flaggedCount'
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

        // Check if this is a password change request
        if ($request->filled('change_password')) {
            // Validate password change
            $request->validate([
                'current_password' => 'required',
                'password' => 'required|min:8|confirmed',
            ]);

            // Verify current password
            if (!Hash::check($request->current_password, $admin->password)) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Current password is incorrect.')
                    ->with('active_tab', 'account-settings');
            }

            // Update password
            DB::table('users')
                ->where('employeeNum', $admin->employeeNum)
                ->update(['password' => Hash::make($request->password)]);

            return redirect()->route('admin.dashboard')
                ->with('success', 'Password changed successfully!')
                ->with('active_tab', 'account-settings');
        }

        // Handle regular profile update
        $data = [];
        
        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);
            $data['profile_picture'] = $filename;
        }

        // Handle other profile fields
        if ($request->filled('firstName')) {
            $data['firstName'] = $request->firstName;
        }
        
        if ($request->filled('lastName')) {
            $data['lastName'] = $request->lastName;
        }
        
        if ($request->has('middleName')) {
            $data['middleName'] = $request->middleName;
        }
        
        if ($request->filled('email')) {
            // Check if email is unique (excluding current user)
            $existingEmail = DB::table('users')
                ->where('email', $request->email)
                ->where('employeeNum', '!=', $admin->employeeNum)
                ->first();
                
            if ($existingEmail) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Email already in use.')
                    ->with('active_tab', 'account-settings');
            }
            
            $data['email'] = $request->email;
        }
        
        if ($request->filled('dob')) {
            $birthDate = \Carbon\Carbon::parse($request->dob);
            $age = $birthDate->diffInYears(now());
            $data['dob'] = $birthDate->format('Y-m-d');
            $data['age'] = $age;
        }
        
        if ($request->filled('sex')) {
            $data['sex'] = $request->sex;
        }

        if ($request->has('about')) {
            $data['about'] = $request->about;
        }

        if (!empty($data)) {
            DB::table('users')->where('employeeNum', $admin->employeeNum)->update($data);
        }

        return redirect()->route('admin.dashboard')
            ->with('success', 'Profile updated successfully!')
            ->with('active_tab', 'account-settings');
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
            'role' => 'required|in:Employee,HR',
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
        
        // Check if the account being edited is an admin (and not the current user)
        $targetUser = DB::table('users')->where('employeeNum', $employeeNum)->first();
        if ($targetUser && $targetUser->role === 'Admin' && $targetUser->employeeNum != Auth::user()->employeeNum) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'You cannot edit other admin accounts.')
                ->with('active_tab', 'account-management');
        }
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,' . $employeeNum . ',employeeNum',
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'middleName' => 'nullable|string|max:255',
            'role' => 'required|in:Employee,HR',
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

    // 🆕 DELETE (ARCHIVE) ACCOUNT - Updated to archive instead of delete
    public function deleteAccount($employeeNum)
    {
        \Log::info('Archiving account:', ['employeeNum' => $employeeNum]);
        
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
                \Log::warning('User not found for archiving:', ['employeeNum' => $employeeNum]);
                return redirect()->route('admin.dashboard')
                    ->with('error', 'User not found.')
                    ->with('active_tab', 'account-management');
            }

            // Prevent admin from deleting other admins
            if ($user->role === 'Admin') {
                \Log::warning('Attempt to delete another admin:', ['employeeNum' => $employeeNum]);
                return redirect()->route('admin.dashboard')
                    ->with('error', 'You cannot delete admin accounts.')
                    ->with('active_tab', 'account-management');
            }

            // Archive the account and deactivate it to prevent login
            $archived = DB::table('users')
                ->where('employeeNum', $employeeNum)
                ->update([
                    'is_archived' => true,
                    'status' => 'Deactivated'
                ]);
            
            if ($archived) {
                \Log::info('Account archived successfully:', ['employeeNum' => $employeeNum]);
                return redirect()->route('admin.dashboard', ['active_tab' => 'account-management'])
                    ->with('success', 'Account archived successfully!')
                    ->withFragment('account-management');
            } else {
                \Log::error('Archive query returned 0 rows affected:', ['employeeNum' => $employeeNum]);
                return redirect()->route('admin.dashboard', ['active_tab' => 'account-management'])
                    ->with('error', 'No account was deleted. User may not exist.')
                    ->withFragment('account-management');
            }
            
        } catch (\Exception $e) {
            \Log::error('Failed to archive account:', [
                'employeeNum' => $employeeNum,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('admin.dashboard')
                ->with('error', 'Failed to archive account: ' . $e->getMessage())
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
                ->where('is_archived', false) // Exclude archived accounts
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
        
        // Check if the account being reset is an admin (and not the current user)
        $targetUser = DB::table('users')->where('employeeNum', $employeeNum)->first();
        if ($targetUser && $targetUser->role === 'Admin' && $targetUser->employeeNum != Auth::user()->employeeNum) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'You cannot reset passwords for other admin accounts.')
                ->with('active_tab', 'account-management');
        }
        
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
            ->select('employeeNum', 'email', 'firstName', 'lastName', 'middleName', 'role', 'sex', 'age', 'dob', 'status')
            ->where('is_archived', false) // Exclude archived accounts
            ->orderBy('employeeNum', 'asc')
            ->get();

        $fileName = 'accounts_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers (Template for import)
            fputcsv($file, [
                'Employee Number',
                'Email',
                'First Name', 
                'Last Name',
                'Middle Name',
                'Role',
                'Gender',
                'Date of Birth (YYYY-MM-DD)',
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
                    $user->dob ?? '',
                    $user->status
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    // 🆕 IMPORT CSV FUNCTIONALITY
    public function importAccounts(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        try {
            $file = $request->file('csv_file');
            $csv = array_map('str_getcsv', file($file->getRealPath()));
            
            // Get headers (first row)
            $headers = array_shift($csv);
            
            // Normalize headers (remove BOM, trim, lowercase)
            $headers = array_map(function($header) {
                return strtolower(trim(str_replace("\xEF\xBB\xBF", '', $header)));
            }, $headers);
            
            $imported = 0;
            $failed = 0;
            $errors = [];
            
            foreach ($csv as $index => $row) {
                $rowNumber = $index + 2; // +2 because we removed header and array is 0-indexed
                
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }
                
                try {
                    // Map CSV columns to data array
                    $data = array_combine($headers, $row);
                    
                    // Validate required fields
                    $employeeNum = trim($data['employee number'] ?? '');
                    $email = trim($data['email'] ?? '');
                    $firstName = trim($data['first name'] ?? '');
                    $lastName = trim($data['last name'] ?? '');
                    $role = trim($data['role'] ?? 'Employee');
                    $sex = trim($data['gender'] ?? 'Male');
                    $dob = trim($data['date of birth (yyyy-mm-dd)'] ?? $data['date of birth'] ?? '');
                    $status = trim($data['status'] ?? 'Active');
                    $middleName = trim($data['middle name'] ?? '');
                    
                    // Validate required fields
                    if (empty($employeeNum) || empty($email) || empty($firstName) || empty($lastName)) {
                        $failed++;
                        $errors[] = "Row {$rowNumber}: Missing required fields (Employee Number, Email, First Name, or Last Name)";
                        continue;
                    }
                    
                    // Check if employee number already exists
                    $exists = DB::table('users')->where('employeeNum', $employeeNum)->exists();
                    if ($exists) {
                        $failed++;
                        $errors[] = "Row {$rowNumber}: Employee Number {$employeeNum} already exists";
                        continue;
                    }
                    
                    // Check if email already exists
                    $emailExists = DB::table('users')->where('email', $email)->exists();
                    if ($emailExists) {
                        $failed++;
                        $errors[] = "Row {$rowNumber}: Email {$email} already exists";
                        continue;
                    }
                    
                    // Validate and calculate age from DOB
                    $age = null;
                    if (!empty($dob)) {
                        try {
                            $birthDate = \Carbon\Carbon::parse($dob);
                            $age = $birthDate->diffInYears(now());
                            
                            // Validate age range (18-65)
                            if ($age < 18 || $age > 65) {
                                $failed++;
                                $errors[] = "Row {$rowNumber}: Invalid age ({$age}). Must be between 18 and 65.";
                                continue;
                            }
                        } catch (\Exception $e) {
                            $failed++;
                            $errors[] = "Row {$rowNumber}: Invalid date format for DOB. Use YYYY-MM-DD.";
                            continue;
                        }
                    } else {
                        // If no DOB provided, default age to 25
                        $age = 25;
                        $dob = now()->subYears(25)->format('Y-m-d');
                    }
                    

                    // Validate role - fail if Admin
                    if ($role === 'Admin') {
                        $failed++;
                        $errors[] = "Row {$rowNumber}: Admin role is not allowed. Only Employee or HR roles can be imported.";
                        continue;
                    }
                    if (!in_array($role, ['Employee', 'HR'])) {
                        $role = 'Employee';
                    }
                    
                    // Validate sex
                    if (!in_array($sex, ['Male', 'Female'])) {
                        $sex = 'Male';
                    }
                    
                    // Validate status
                    if (!in_array($status, ['Active', 'Deactivated'])) {
                        $status = 'Active';
                    }
                    
                    // Generate default password: LastName + BirthYear (e.g., Smith1990)
                    $birthYear = \Carbon\Carbon::parse($dob)->format('Y');
                    $defaultPassword = str_replace(' ', '', $lastName) . $birthYear;
                    
                    // Insert user (without created_at/updated_at as they don't exist in the table)
                    DB::table('users')->insert([
                        'employeeNum' => $employeeNum,
                        'email' => $email,
                        'password' => Hash::make($defaultPassword),
                        'firstName' => $firstName,
                        'lastName' => $lastName,
                        'middleName' => $middleName,
                        'role' => $role,
                        'sex' => $sex,
                        'age' => $age,
                        'dob' => $dob,
                        'status' => $status,
                        'profile_picture' => DB::raw("'default.png'"), // Store as string in BLOB
                        'name' => $firstName . ' ' . $lastName, // Combine names for the name field
                        'birth_date' => $dob // Some records use birth_date instead of dob
                    ]);
                    
                    $imported++;
                    
                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = "Row {$rowNumber}: {$e->getMessage()}";
                    \Log::error("CSV Import Error Row {$rowNumber}: " . $e->getMessage());
                }
            }
            
            $message = "Import completed! {$imported} accounts imported successfully.";
            if ($failed > 0) {
                $message .= " {$failed} rows failed.";
            }
            
            $response = redirect()->route('admin.dashboard')
                ->with('success', $message)
                ->with('active_tab', 'account-management');
            
            if (!empty($errors)) {
                $response->with('import_errors', $errors);
            }
            
            return $response;
            
        } catch (\Exception $e) {
            \Log::error('CSV Import Failed: ' . $e->getMessage());
            return redirect()->route('admin.dashboard')
                ->with('error', 'Failed to import CSV: ' . $e->getMessage())
                ->with('active_tab', 'account-management');
        }
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
                'resolved_by' => $ticket->resolved_by ?? null,
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

    public function getFilteredKPIs(Request $request)
    {
        try {
            $range = $request->input('range', 'overall');
            $startDate = null;
            $endDate = null;

            // Calculate date range based on selection
            $today = now();
            
            switch($range) {
                case 'daily':
                    $startDate = $today->copy()->startOfDay();
                    $endDate = $today->copy()->endOfDay();
                    break;
                    
                case 'weekly':
                    $startDate = $today->copy()->startOfWeek();
                    $endDate = $today->copy()->endOfWeek();
                    break;
                    
                case 'monthly':
                    $startDate = $today->copy()->startOfMonth();
                    $endDate = $today->copy()->endOfMonth();
                    break;
                    
                case 'annually':
                    $startDate = $today->copy()->startOfYear();
                    $endDate = $today->copy()->endOfYear();
                    break;
                    
                case 'overall':
                default:
                    // No date filtering for overall
                    break;
            }

            // Build queries with optional date filtering
            $queriesQuery = DB::table('queries');
            $ticketsQuery = DB::table('hr_inbox');
            $feedbackQuery = DB::table('feedback');
            $flaggedQuery = DB::table('flaggedresponse');

            if ($startDate && $endDate) {
                $queriesQuery->whereBetween('questionTime', [$startDate, $endDate]);
                $ticketsQuery->whereBetween('created_at', [$startDate, $endDate]);
                $feedbackQuery->whereBetween('timeStamp', [$startDate, $endDate]);
                $flaggedQuery->whereBetween('timeStamp', [$startDate, $endDate]);
            }

            // Calculate KPIs
            $totalInteractions = $queriesQuery->count();
            $ticketsData = $ticketsQuery->get();
            $escalatedQueries = $ticketsData->count(); // All tickets are escalated queries
            $pendingQueries = $ticketsData->whereIn('status', ['Open', 'Waiting for HR'])->count();
            $resolvedQueries = $totalInteractions - $pendingQueries;

            // Feedback KPIs
            $feedbacks = $feedbackQuery->get();
            $feedbackCount = $feedbacks->count();
            $feedbackAvg = $feedbackCount > 0 ? round($feedbacks->avg('rating'), 2) : null;

            // Flagged responses count
            $flaggedCount = $flaggedQuery->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'totalInteractions' => $totalInteractions,
                    'escalatedQueries' => $escalatedQueries,
                    'pendingQueries' => $pendingQueries,
                    'resolvedQueries' => $resolvedQueries,
                    'resolutionRate' => $totalInteractions > 0 ? round(($resolvedQueries / $totalInteractions) * 100, 1) : 0,
                    'feedbackAvg' => $feedbackAvg,
                    'feedbackCount' => $feedbackCount,
                    'flaggedCount' => $flaggedCount
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch filtered KPIs', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch filtered KPIs: ' . $e->getMessage()
            ], 500);
        }
    }

    // Add these methods after the existing methods in your AdminController

/**
 * 🆕 DIALOGFLOW INTENTS MANAGEMENT
 */

// Get all Dialogflow intents
public function getDialogflowIntents()
{
    try {
        $dialogflowService = new \App\Services\DialogflowService();
        $intents = $dialogflowService->listIntents();
        
        // Calculate statistics
        $totalTrainingPhrases = 0;
        foreach ($intents as $intent) {
            $totalTrainingPhrases += $intent['training_phrases_count'] ?? 0;
        }
        
        $stats = [
            'total_intents' => count($intents),
            'total_training_phrases' => $totalTrainingPhrases,
            'last_updated' => now()->format('Y-m-d H:i:s'),
            'total_questions' => \App\Models\GuidedQuestion::count()
        ];
        
        return response()->json([
            'success' => true,
            'intents' => $intents,
            'stats' => $stats
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Failed to get Dialogflow intents: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch intents from Dialogflow: ' . $e->getMessage()
        ], 500);
    }
}

// Get active intents for dropdown
public function getActiveDialogflowIntents()
{
    try {
        $dialogflowService = new \App\Services\DialogflowService();
        $intents = $dialogflowService->listIntents();
        
        // Filter out fallback intents
        $activeIntents = array_filter($intents, function($intent) {
            return !($intent['is_fallback'] ?? false);
        });
        
        return response()->json([
            'success' => true,
            'intents' => array_values($activeIntents)
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Failed to get active intents: ' . $e->getMessage());
        return response()->json([
            'success' => true,
            'intents' => []
        ]);
    }
}

// Create a new intent
public function createDialogflowIntent(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'intent_name' => 'required|string|max:255',
            'display_name' => 'required|string|max:255',
            'training_phrases' => 'required|array',
            'training_phrases.*' => 'string',
            'responses' => 'required|array',
            'responses.*' => 'string',
            'priority' => 'nullable|integer|min:0|max:1000000',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $dialogflowService = new \App\Services\DialogflowService();
        $result = $dialogflowService->createIntent($request->all());
        
        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Intent created successfully!',
                'intent' => $result['intent']
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create intent: ' . ($result['error'] ?? 'Unknown error')
            ], 500);
        }
        
    } catch (\Exception $e) {
        \Log::error('Failed to create Dialogflow intent: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to create intent: ' . $e->getMessage()
        ], 500);
    }
}

// Update an intent
public function updateDialogflowIntent(Request $request, $intentName)
{
    try {
        $validator = Validator::make($request->all(), [
            'display_name' => 'sometimes|string|max:255',
            'training_phrases' => 'sometimes|array',
            'training_phrases.*' => 'string',
            'responses' => 'sometimes|array',
            'responses.*' => 'string',
            'priority' => 'nullable|integer|min:0|max:1000000',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $dialogflowService = new \App\Services\DialogflowService();
        $result = $dialogflowService->updateIntent($intentName, $request->all());
        
        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Intent updated successfully!',
                'intent' => $result['intent']
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update intent: ' . ($result['error'] ?? 'Unknown error')
            ], 500);
        }
        
    } catch (\Exception $e) {
        \Log::error('Failed to update Dialogflow intent: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to update intent: ' . $e->getMessage()
        ], 500);
    }
}

// Delete an intent
public function deleteDialogflowIntent($intentName)
{
    try {
        $dialogflowService = new \App\Services\DialogflowService();
        $result = $dialogflowService->deleteIntent($intentName);
        
        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Intent deleted successfully!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete intent: ' . ($result['error'] ?? 'Unknown error')
            ], 500);
        }
        
    } catch (\Exception $e) {
        \Log::error('Failed to delete Dialogflow intent: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to delete intent: ' . $e->getMessage()
        ], 500);
    }
}

// Sync with Dialogflow (refresh intents)
public function syncWithDialogflow()
{
    try {
        $dialogflowService = new \App\Services\DialogflowService();
        $intents = $dialogflowService->listIntents();
        
        // In a real application, you might want to cache or store metadata locally
        // For now, we'll just return the refreshed list
        
        return response()->json([
            'success' => true,
            'message' => 'Successfully synchronized with Dialogflow!',
            'intents_count' => count($intents)
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Failed to sync with Dialogflow: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to sync with Dialogflow: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * 🆕 GUIDED QUESTIONS MANAGEMENT
 */

// Get all guided questions
public function getGuidedQuestions()
{
    try {
        $questions = \App\Models\GuidedQuestion::orderBy('display_order')
            ->orderBy('id')
            ->get();
        
        return response()->json([
            'success' => true,
            'questions' => $questions
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Failed to get guided questions: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch guided questions'
        ], 500);
    }
}

// Create a new guided question
public function createGuidedQuestion(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'question_text' => 'required|string|max:1000',
            'linked_intent' => 'nullable|string|max:255',
            'display_order' => 'nullable|integer|min:1',
            'response_type' => 'required|in:text,buttons,cards,quick_replies',
            'custom_response' => 'nullable|json',
            'category' => 'nullable|string|max:100',
            'status' => 'required|in:active,archived'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $question = \App\Models\GuidedQuestion::create([
            'question_text' => $request->question_text,
            'linked_intent' => $request->linked_intent,
            'display_order' => $request->display_order ?? 1,
            'response_type' => $request->response_type,
            'custom_response' => $request->custom_response ? json_decode($request->custom_response, true) : null,
            'category' => $request->category,
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Guided question created successfully!',
            'question' => $question
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Failed to create guided question: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to create guided question: ' . $e->getMessage()
        ], 500);
    }
}

// Update a guided question
public function updateGuidedQuestion(Request $request, $id)
{
    try {
        $question = \App\Models\GuidedQuestion::find($id);
        
        if (!$question) {
            return response()->json([
                'success' => false,
                'message' => 'Guided question not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'question_text' => 'sometimes|string|max:1000',
            'linked_intent' => 'nullable|string|max:255',
            'display_order' => 'nullable|integer|min:1',
            'response_type' => 'sometimes|in:text,buttons,cards,quick_replies',
            'custom_response' => 'nullable|json',
            'category' => 'nullable|string|max:100',
            'status' => 'sometimes|in:active,archived'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = [];
        if ($request->has('question_text')) {
            $updateData['question_text'] = $request->question_text;
        }
        if ($request->has('linked_intent')) {
            $updateData['linked_intent'] = $request->linked_intent;
        }
        if ($request->has('display_order')) {
            $updateData['display_order'] = $request->display_order;
        }
        if ($request->has('response_type')) {
            $updateData['response_type'] = $request->response_type;
        }
        if ($request->has('custom_response')) {
            $updateData['custom_response'] = $request->custom_response ? json_decode($request->custom_response, true) : null;
        }
        if ($request->has('category')) {
            $updateData['category'] = $request->category;
        }
        if ($request->has('status')) {
            $updateData['status'] = $request->status;
        }

        $question->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Guided question updated successfully!',
            'question' => $question->fresh()
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Failed to update guided question: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to update guided question: ' . $e->getMessage()
        ], 500);
    }
}

// Delete a guided question
public function deleteGuidedQuestion($id)
{
    try {
        $question = \App\Models\GuidedQuestion::find($id);
        
        if (!$question) {
            return response()->json([
                'success' => false,
                'message' => 'Guided question not found'
            ], 404);
        }

        $question->delete();

        return response()->json([
            'success' => true,
            'message' => 'Guided question deleted successfully!'
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Failed to delete guided question: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to delete guided question: ' . $e->getMessage()
        ], 500);
    }
}
}