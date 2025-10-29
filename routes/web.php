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
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard routes based on role
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $role = strtolower($user->role ?? '');
        
        if ($role === 'admin') return redirect('/admin/dashboard');
        if ($role === 'hr') return redirect('/hr/dashboard');
        
        return redirect('/employee/dashboard');
    })->name('dashboard');

    // Employee routes
   // Employee routes
Route::prefix('employee')->group(function () {
    Route::get('/dashboard', [EmployeeController::class, 'index'])->name('employee.dashboard');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
    Route::post('/flag', [FlagController::class, 'store'])->name('flag.store');
    
    // 🆕 CORRECT: Make sure these routes exist and point to EmployeeController
    Route::get('/get-latest-ticket', [EmployeeController::class, 'getLatestTicket'])->name('employee.latestTicket');
    Route::get('/messages/{ticket_no}', [EmployeeController::class, 'getMessages'])->name('employee.messages');
    Route::get('/tickets', [EmployeeController::class, 'getTickets'])->name('employee.tickets');
    Route::get('/ticket-status/{ticket_no}', [EmployeeController::class, 'getTicketStatus'])->name('employee.ticket-status');
    // 🆕 NEW: Employee replies to existing ticket
    Route::post('/reply-to-ticket', [EmployeeController::class, 'replyToTicket'])->name('employee.reply-to-ticket');
});

   // HR routes
Route::prefix('hr')->group(function () {
    Route::get('/dashboard', [HRController::class, 'index'])->name('hr.dashboard');
    Route::get('/profile', [HRController::class, 'editProfile'])->name('hr.profile');
    Route::put('/profile/update', [HRController::class, 'updateProfile'])->name('hr.profile.update');
    Route::post('/reply', [HRController::class, 'reply'])->name('hr.reply'); // ✅ Keep only this one
    Route::post('/announcements', [HrAnnouncementController::class, 'store'])->name('hr.announcements.store');
    Route::get('/messages/{ticket_no}', [HRController::class, 'getMessages'])->name('hr.messages');
    Route::get('/tickets/json', [HRController::class, 'ticketsJson'])->name('hr.tickets.json');
    Route::post('/resolve-ticket', [HRController::class, 'resolveTicket'])->name('hr.resolve-ticket'); // ✅ Fixed path
});

    // Admin routes
  Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/create-account', [AdminController::class, 'createAccount'])->name('admin.create-account');
    Route::get('/delete-account/{employeeNum}', [AdminController::class, 'deleteAccount'])->name('admin.delete-account');
    Route::put('/update-account/{employeeNum}', [AdminController::class, 'updateAccount'])->name('admin.update-account'); // 🆕 FIXED
    Route::get('/get-account/{employeeNum}', [AdminController::class, 'getAccount'])->name('admin.get-account');
    Route::post('/kb/add', [AdminController::class, 'addKnowledge'])->name('admin.kb.add');
    Route::get('/kb/delete/{id}', [AdminController::class, 'deleteKnowledge'])->name('admin.kb.delete');
    Route::post('/announcement/add', [AdminController::class, 'addAnnouncement'])->name('admin.announcement.add');
    Route::get('/announcement/delete/{id}', [AdminController::class, 'deleteAnnouncement'])->name('admin.announcement.delete');
    Route::post('/profile/update', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
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

// Remove the duplicate require if it's causing issues
// require __DIR__.'/auth.php';