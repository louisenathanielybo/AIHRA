<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HRController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\HrAnnouncementController;
use App\Http\Controllers\FlagController;
use App\Http\Controllers\DialogflowController;
use App\Http\Controllers\GuidedQuestionController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
  // Redirect root to login
Route::get('/', function () {
    return redirect('/login');
});
// 🆕 FIXED: Login routes (should be outside auth middleware)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Public API routes
Route::post('/dialogflow-webhook', [DialogflowController::class, 'webhook'])->withoutMiddleware('csrf')->middleware('throttle:60,1');

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/about', [ProfileController::class, 'updateAbout'])->name('profile.updateAbout');
    Route::post('/password/update', [ProfileController::class, 'updatePassword'])->name('password.update');

    // Dashboard routes based on role
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $role = strtolower($user->role ?? '');
        
        if ($role === 'admin') return redirect('/admin/dashboard');
        if ($role === 'hr') return redirect('/hr/dashboard');
        
        return redirect('/employee/dashboard');
    })->name('dashboard');

    // Employee routes
    Route::prefix('employee')->group(function () {
        Route::get('/dashboard', [EmployeeController::class, 'index'])->name('employee.dashboard');
        Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
        Route::post('/flag', [FlagController::class, 'store'])->name('flag.store');
        
        Route::get('/get-latest-ticket', [EmployeeController::class, 'getLatestTicket'])->name('employee.latestTicket');
        Route::get('/messages/{ticket_no}', [EmployeeController::class, 'getMessages'])->name('employee.messages');
        Route::get('/tickets', [EmployeeController::class, 'getTickets'])->name('employee.tickets');
        Route::get('/ticket-status/{ticket_no}', [EmployeeController::class, 'getTicketStatus'])->name('employee.ticket-status');
        Route::post('/reply-to-ticket', [EmployeeController::class, 'replyToTicket'])->name('employee.reply-to-ticket');
        
        // Conversation routes
        Route::get('/conversations', [EmployeeController::class, 'getConversations'])->name('employee.conversations');
        Route::post('/conversations/start', [EmployeeController::class, 'startConversation'])->name('employee.conversations.start');
        Route::get('/conversations/{id}', [EmployeeController::class, 'getConversationMessages'])->name('employee.conversations.show');
        Route::delete('/conversations/{id}', [EmployeeController::class, 'deleteConversation'])->name('employee.conversations.delete');
    });

    // HR routes
    Route::prefix('hr')->group(function () {
        Route::get('/dashboard', [HRController::class, 'index'])->name('hr.dashboard');
        Route::get('/profile', [HRController::class, 'editProfile'])->name('hr.profile');
        Route::put('/profile/update', [HRController::class, 'updateProfile'])->name('hr.profile.update');
        Route::post('/reply', [HRController::class, 'reply'])->name('hr.reply');
        Route::post('/announcements', [HrAnnouncementController::class, 'store'])->name('hr.announcements.store');
        Route::get('/messages/{ticket_no}', [HRController::class, 'getMessages'])->name('hr.messages');
        Route::get('/tickets/json', [HRController::class, 'ticketsJson'])->name('hr.tickets.json');
        Route::post('/resolve-ticket', [HRController::class, 'resolveTicket'])->name('hr.resolve-ticket');
        // ADD THESE NEW ROUTES FOR DELETE
    Route::get('/announcements/{id}', [HRController::class, 'getAnnouncement'])->name('hr.announcements.get');
    Route::post('/announcements/{id}', [HRController::class, 'updateAnnouncement'])->name('hr.announcements.update');
    Route::delete('/announcements/{id}', [HRController::class, 'deleteAnnouncement'])->name('hr.announcements.delete');
    });

   // Admin Routes - FIXED VERSION
Route::prefix('admin')->group(function () {
    // Main dashboard - GET only
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    
    // Account management routes
    
    // Export and Import accounts - MUST be before parameterized routes
    Route::get('/accounts/export', [AdminController::class, 'exportAccounts'])->name('admin.accounts.export');
    Route::post('/accounts/import', [AdminController::class, 'importAccounts'])->name('admin.accounts.import');
    
    // Create account - POST to /admin/accounts (not /admin/dashboard)
    Route::post('/accounts', [AdminController::class, 'createAccount'])->name('admin.accounts.create');
    
    // Parameterized account routes - MUST be after specific routes like export/import
    Route::get('/accounts/{employeeNum}', [AdminController::class, 'getAccount'])->name('admin.accounts.get');
    Route::put('/accounts/{employeeNum}', [AdminController::class, 'updateAccount'])->name('admin.accounts.update');
    Route::post('/accounts/{employeeNum}/reset-password', [AdminController::class, 'resetPassword'])->name('admin.accounts.reset-password');
    Route::delete('/accounts/{employeeNum}', [AdminController::class, 'deleteAccount'])->name('admin.accounts.delete');
    
    // Other admin routes...
    Route::post('/knowledge', [AdminController::class, 'addKnowledge'])->name('admin.knowledge.add');
    Route::delete('/knowledge/{id}', [AdminController::class, 'deleteKnowledge'])->name('admin.knowledge.delete');
    Route::post('/announcements', [AdminController::class, 'addAnnouncement'])->name('admin.announcements.add');
    Route::delete('/announcements/{id}', [AdminController::class, 'deleteAnnouncement'])->name('admin.announcements.delete');
    Route::post('/profile', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
    // Add this route for AJAX ticket data
    Route::get('/tickets/data', [AdminController::class, 'getTickets'])->name('admin.tickets.data');
    Route::get('/tickets/{ticketId}', [AdminController::class, 'getTicketDetails'])->name('admin.tickets.details');
    
    // Date range filter for KPIs
    Route::get('/kpis/filter', [AdminController::class, 'getFilteredKPIs'])->name('admin.kpis.filter');
    
    // Flagged responses routes
    Route::post('/flags/{id}/update-status', [FlagController::class, 'updateStatus'])->name('admin.flags.update-status');
});

    // Guided questions (for chatbot)
    Route::get('/guided', [GuidedQuestionController::class, 'index']);
    Route::get('/guided/{id}', [GuidedQuestionController::class, 'show']);

    // Chat messages API
    Route::get('/chat/messages/{ticket_no}', function ($ticket_no) {
        $messages = DB::table('chat_messages')
            ->where('ticket_no', $ticket_no)
            ->orderBy('created_at', 'asc')
            ->get(['sender', 'message', 'created_at']);

        return response()->json($messages);
    });
});