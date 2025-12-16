<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\HrAnnouncement;
use App\Models\HrInbox;
use App\Models\Query;
use Illuminate\Support\Str;
use App\Models\HrReply;
use Illuminate\Support\Facades\Validator;

class HRController extends Controller
{
    /**
     * Display the HR dashboard.
     */
    public function index()
    {
        // Delete expired announcements
        DB::table('announcements')
            ->where('expiry_date', '<', now()->toDateString())
            ->whereNotNull('expiry_date')
            ->delete();

        // ✅ Get all HR announcements (excluding expired ones)
        $announcements = DB::table('announcements')
            ->where('isActive', 1)
            ->where(function($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', now()->toDateString());
            })
            ->orderBy('createdAt', 'desc')
            ->get();

        // ✅ Get all inbox tickets (newest first) - assigned to this HR OR unassigned
        $currentHR = Auth::user()->employeeNum ?? null;
        $inbox = HrInbox::where(function($query) use ($currentHR) {
                $query->where('assigned_to', $currentHR)
                      ->orWhereNull('assigned_to')
                      ->orWhere('assigned_to', '');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // ✅ Compute inbox stats - for assigned tickets OR unassigned
        $inboxStats = [
            'total' => HrInbox::where(function($q) use ($currentHR) {
                $q->where('assigned_to', $currentHR)->orWhereNull('assigned_to')->orWhere('assigned_to', '');
            })->count(),
            'urgent' => HrInbox::where(function($q) use ($currentHR) {
                $q->where('assigned_to', $currentHR)->orWhereNull('assigned_to')->orWhere('assigned_to', '');
            })->where('priority', 'urgent')->count(),
            'high' => HrInbox::where(function($q) use ($currentHR) {
                $q->where('assigned_to', $currentHR)->orWhereNull('assigned_to')->orWhere('assigned_to', '');
            })->where('priority', 'high')->count(),
            'medium' => HrInbox::where(function($q) use ($currentHR) {
                $q->where('assigned_to', $currentHR)->orWhereNull('assigned_to')->orWhere('assigned_to', '');
            })->where('priority', 'medium')->count(),
            'low' => HrInbox::where(function($q) use ($currentHR) {
                $q->where('assigned_to', $currentHR)->orWhereNull('assigned_to')->orWhere('assigned_to', '');
            })->where('priority', 'low')->count(),
            'replied' => HrInbox::where(function($q) use ($currentHR) {
                $q->where('assigned_to', $currentHR)->orWhereNull('assigned_to')->orWhere('assigned_to', '');
            })->where('status', 'Replied')->count(),
        ];

        // ✅ Get the currently logged-in user
        $user = Auth::user();

        // ✅ Pass variables to the view
        return view('hr.hr_dashboard', compact('announcements', 'inbox', 'user', 'inboxStats'));
    }

    /**
     * ✅ HR posts a new announcement.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'image'   => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
        ]);

        $employeeNum = Auth::user()->employeeNum ?? null;

        $data = [
            'employeeNum' => $employeeNum,
            'title'       => $request->title,
            'description' => $request->content,
            'createdAt'   => now(),
            'isActive'    => 1,
        ];

        // ✅ Optional image upload (stored as binary)
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $data['image'] = file_get_contents($image->getRealPath());
        }

        DB::table('announcements')->insert($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => '📢 Announcement posted successfully!'
            ]);
        }

        return back()->with('success', '📢 Announcement posted successfully!');
    }

    /**
     * ✅ Get announcement for editing
     */
    public function getAnnouncement($id)
    {
        try {
            $announcement = DB::table('announcements')
                ->where('id', $id)
                ->where('isActive', 1)
                ->first();

            if ($announcement) {
                try {
                    // Normalize payload for the frontend
                    $payload = [
                        'id' => $announcement->id ?? null,
                        'title' => $announcement->title ?? '',
                        'description' => $announcement->description ?? '',
                        // Ensure date string in Y-m-d for <input type="date">
                        'expiry_date' => !empty($announcement->expiry_date)
                            ? (function($d){
                                try { return \Carbon\Carbon::parse($d)->format('Y-m-d'); } catch (\Throwable $e) { return null; }
                              })($announcement->expiry_date)
                            : null,
                    ];

                    // Convert binary image blob to base64 if present
                    if (!empty($announcement->image)) {
                        try {
                            $binary = is_resource($announcement->image)
                                ? stream_get_contents($announcement->image)
                                : $announcement->image;
                            if ($binary !== null && $binary !== '') {
                                $payload['image'] = base64_encode($binary);
                            } else {
                                $payload['image'] = null;
                            }
                        } catch (\Throwable $e) {
                            $payload['image'] = null;
                            \Log::warning('Failed to base64-encode announcement image: ' . $e->getMessage());
                        }
                    } else {
                        $payload['image'] = null;
                    }

                    \Log::info('HR getAnnouncement payload prepared', ['payload' => $payload]);
                    return response()->json([
                        'success' => true,
                        'announcement' => $payload
                    ]);
                } catch (\Throwable $e) {
                    \Log::error('Error preparing announcement payload: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Error preparing announcement payload'
                    ], 500);
                }
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Announcement not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading announcement: ' . $e->getMessage()
            ], 500);
        }
    }

        /**
     * ✅ Update announcement
     */
    public function updateAnnouncement(Request $request, $id)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
                'expiry_date' => 'nullable|date|after_or_equal:today'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = [
                'title' => $request->title,
                'description' => $request->description,
            ];
            
            // Add expiry_date if provided
            if ($request->has('expiry_date')) {
                $data['expiry_date'] = $request->expiry_date;
            }
            
            // Handle image update if provided
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageData = file_get_contents($image->getRealPath());
                $data['image'] = $imageData;
            }
            
            $updated = DB::table('announcements')
                ->where('id', $id)
                ->where('isActive', 1)
                ->update($data);
            
            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => 'Announcement updated successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Announcement not found or could not be updated'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update announcement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ Delete announcement (soft delete by setting isActive to 0)
     */
    public function deleteAnnouncement($id)
    {
        try {
            $deleted = DB::table('announcements')
                ->where('id', $id)
                ->where('isActive', 1)
                ->update(['isActive' => 0]); // Remove updatedAt since the column doesn't exist
            
            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Announcement deleted successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Announcement not found or already deleted'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete announcement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the HR profile edit page.
     */
    public function editProfile()
    {
        $user = Auth::user(); // get the currently logged-in HR user
        return view('hr.hr_profile', compact('user'));
    }

    /**
     * Update the HR profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'about'           => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('profile_picture')) {
            $filename = time() . '.' . $request->profile_picture->extension();
            $request->profile_picture->move(public_path('uploads'), $filename);
            $user->profile_picture = $filename;
        }

        $user->about = $request->about;
        $user->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully!'
            ]);
        }

        return redirect()->route('hr.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Return tickets as JSON.
     */
    public function ticketsJson()
    {
        return response()->json(HrInbox::orderBy('created_at', 'desc')->get());
    }

    /**
     * Check and mark expired tickets (can be called manually or via scheduled task)
     */
    public function checkExpiredTickets()
    {
        $service = new \App\Services\TicketExpirationService();
        $result = $service->checkExpiredTickets();
        
        return response()->json($result);
    }

    /**
     * Get expiration statistics
     */
    public function getExpirationStats()
    {
        $service = new \App\Services\TicketExpirationService();
        $stats = $service->getExpirationStats();
        
        return response()->json($stats);
    }

    public function getMessages($ticket_no)
    {
        try {
            // Employee's original message from hr_inbox
            $inbox = HrInbox::where('ticket_no', $ticket_no)->first();

            // Get employee's full name
            $employeeName = 'Employee';
            if ($inbox && $inbox->from_user) {
                $employee = DB::table('users')
                    ->where('employeeNum', $inbox->from_user)
                    ->first(['firstName', 'lastName', 'name']);
                if ($employee) {
                    if (!empty($employee->firstName) && !empty($employee->lastName)) {
                        $employeeName = $employee->firstName . ' ' . $employee->lastName;
                    } elseif (!empty($employee->name)) {
                        $employeeName = $employee->name;
                    }
                }
            }

            // HR replies from hr_replies table
            $replies = HrReply::where('ticket_no', $ticket_no)
                ->orderBy('replied_at', 'asc')
                ->get();

            // Combine employee + HR messages
            $messages = collect();

            if ($inbox) {
                $messages->push([
                    'sender' => 'employee',
                    'sender_name' => $employeeName,
                    'message' => $inbox->message,
                    'created_at' => $inbox->created_at,
                ]);
            }

            foreach ($replies as $reply) {
                // Determine sender: some entries in hr_replies may actually be
                // employee follow-ups (they were saved there by older flows).
                // Use replied_by compared to the original ticket's from_user to
                // infer the correct sender for rendering.
                $sender = 'hr';
                $senderName = 'HR';
                if ($inbox && isset($inbox->from_user) && $reply->replied_by == $inbox->from_user) {
                    $sender = 'employee';
                    $senderName = $employeeName;
                }

                $text = $reply->hr_message;
                // Strip legacy stored prefixes like "Employee Follow-up: ..."
                if (preg_match('/Employee\s*-?\s*Follow-?up/i', $text)) {
                    $text = preg_replace('/^.*?Employee\s*-?\s*Follow-?up:?\s*/i', '', $text);
                }

                $messages->push([
                    'sender' => $sender,
                    'sender_name' => $senderName,
                    'message' => $text,
                    'created_at' => $reply->replied_at,
                ]);
            }

            return response()->json($messages->sortBy('created_at')->values());

        } catch (\Exception $e) {
            \Log::error('Get Messages Error: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    /**
     * ✅ HR replies to a ticket - ONLY using hr_replies table
     */
    public function reply(Request $request)
    {
        $request->validate([
            'ticket_no' => 'required|string|exists:hr_inbox,ticket_no',
            'message' => 'required|string',
        ]);

        try {
            // 🆕 FIXED: Only save to hr_replies table
            // Use a reliable identifier for replied_by (employeeNum > email > 'HR')
            $repliedBy = 'HR';
            if (Auth::check()) {
                $user = Auth::user();
                $repliedBy = $user->employeeNum ?? ($user->email ?? 'HR');
            }

            HrReply::create([
                'ticket_no'  => $request->ticket_no,
                'hr_message' => $request->message,
                'replied_by' => $repliedBy,
                'replied_at' => now(),
            ]);

            // 🆕 REMOVED: No chat_messages insertion

            // Update ticket status and set responded_at if first response
            $ticket = HrInbox::where('ticket_no', $request->ticket_no)->first();
            
            $updateData = [
                'status' => 'Replied', 
                'updated_at' => now()
            ];
            
            // Set responded_at timestamp if this is the first response
            if ($ticket && is_null($ticket->responded_at)) {
                $updateData['responded_at'] = now();
            }
            
            HrInbox::where('ticket_no', $request->ticket_no)->update($updateData);

            return response()->json([
                'success' => true, 
                'message' => 'Reply sent successfully!'
            ]);

        } catch (\Exception $e) {
            \Log::error('HR Reply Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false, 
                'message' => 'Failed to send reply: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ Resolve/Close a ticket
     */
    public function resolveTicket(Request $request)
    {
        $request->validate([
            'ticket_no' => 'required|string|exists:hr_inbox,ticket_no',
            'category' => 'required|string|max:100',
        ]);

        try {
            $resolvedBy = Auth::user()->employeeNum ?? null;
            HrInbox::where('ticket_no', $request->ticket_no)
                ->update([
                    'status' => 'Resolved',
                    'category' => $request->category,
                    'resolved_at' => now(),
                    'resolved_by' => $resolvedBy,
                    'updated_at' => now()
                ]);
            return response()->json(['success' => true, 'message' => 'Ticket resolved successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to resolve ticket'], 500);
        }
    }
}