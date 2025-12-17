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


// In routes/web.php
Route::get('/test-dialogflow', function() {
    try {
        $service = new \App\Services\DialogflowService();
        $result = $service->detectIntent('Hello', 'test-session');
        return response()->json([
            'success' => true,
            'result' => $result
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});
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
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.patch.update');
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
        Route::get('/accounts/{employeeNum}/unresolved-tickets', [AdminController::class, 'checkUnresolvedTickets'])->name('admin.accounts.unresolved-tickets');
        
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
        
        // 🆕 ADD THIS ROUTE - Dialogflow sync
 
        
        // 🆕 ADD THIS ROUTE - Dialogflow intents management
        // Add this route to your existing Dialogflow routes
       // Add this route to match your JavaScript call
        Route::post('/dialogflow/sync', [DialogflowController::class, 'syncIntents'])->name('admin.dialogflow.sync');
        Route::get('/dialogflow/intents', [AdminController::class, 'getDialogflowIntents'])->name('admin.dialogflow.intents');
        Route::get('/dialogflow/intents/active', [AdminController::class, 'getActiveDialogflowIntents'])->name('admin.dialogflow.intents.active');
        Route::post('/dialogflow/intents', [AdminController::class, 'createDialogflowIntent'])->name('admin.dialogflow.intents.create');
        Route::put('/dialogflow/intents/{id}', [AdminController::class, 'updateDialogflowIntent'])->name('admin.dialogflow.intents.update');
        Route::delete('/dialogflow/intents/{id}', [AdminController::class, 'deleteDialogflowIntent'])->name('admin.dialogflow.intents.delete');
        Route::post('/dialogflow/intents', [DialogflowController::class, 'createIntent'])->name('admin.dialogflow.intents.create');
        Route::put('/dialogflow/intents/{id}', [DialogflowController::class, 'updateIntent'])->name('admin.dialogflow.intents.update');
        Route::delete('/dialogflow/intents/{id}', [DialogflowController::class, 'deleteIntent'])->name('admin.dialogflow.intents.delete');
        
        // 🆕 ADD THIS ROUTE - Guided questions management
        Route::get('/guided-questions', [DialogflowController::class, 'getGuidedQuestions'])->name('admin.guided-questions');
        Route::post('/guided-questions', [DialogflowController::class, 'createGuidedQuestion'])->name('admin.guided-questions.create');
        Route::put('/guided-questions/{id}', [DialogflowController::class, 'updateGuidedQuestion'])->name('admin.guided-questions.update');
        Route::delete('/guided-questions/{id}', [DialogflowController::class, 'deleteGuidedQuestion'])->name('admin.guided-questions.delete');
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

    Route::get('/test-dialogflow-intents', function() {
    try {
        $dialogflow = new \App\Services\DialogflowService();
        
        // Test if credentials are set
        echo "Project ID: " . $dialogflow->projectId . "<br>";
        echo "Credentials path: " . base_path('aihra-key.json') . "<br>";
        
        // Check if file exists
        if (file_exists(base_path('aihra-key.json'))) {
            echo "✅ Credentials file exists<br>";
        } else {
            echo "❌ Credentials file NOT found<br>";
        }
        
        // Try to list intents
        $intents = $dialogflow->listIntents();
        
        echo "✅ Intents fetched successfully<br>";
        echo "Number of intents: " . count($intents) . "<br>";
        
        $dialogflow->close();
        
        return response()->json(['success' => true, 'count' => count($intents)]);
        
    } catch (\Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "<br>";
        echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
        
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
});

Route::get('/debug-dialogflow', function() {
    echo "<h2>Dialogflow Configuration Debug</h2>";
    
    // Check .env values
    echo "<h3>Environment Variables:</h3>";
    echo "DIALOGFLOW_PROJECT_ID: " . env('DIALOGFLOW_PROJECT_ID', 'NOT SET') . "<br>";
    echo "DIALOGFLOW_CREDENTIALS_PATH: " . env('DIALOGFLOW_CREDENTIALS_PATH', 'NOT SET') . "<br>";
    echo "GOOGLE_APPLICATION_CREDENTIALS: " . env('GOOGLE_APPLICATION_CREDENTIALS', 'NOT SET') . "<br>";
    
    // Check file existence
    $credentialsPath = env('DIALOGFLOW_CREDENTIALS_PATH', 'aihra-key.json');
    $fullPath = base_path($credentialsPath);
    echo "<h3>Credentials File:</h3>";
    echo "Full path: " . $fullPath . "<br>";
    echo "Exists: " . (file_exists($fullPath) ? '✅ YES' : '❌ NO') . "<br>";
    
    if (file_exists($fullPath)) {
        echo "File size: " . filesize($fullPath) . " bytes<br>";
        echo "Readable: " . (is_readable($fullPath) ? '✅ YES' : '❌ NO') . "<br>";
        
        // Check if valid JSON
        $content = file_get_contents($fullPath);
        $json = json_decode($content, true);
        echo "Valid JSON: " . (json_last_error() === JSON_ERROR_NONE ? '✅ YES' : '❌ NO') . "<br>";
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "JSON Error: " . json_last_error_msg() . "<br>";
        } else {
            echo "JSON Keys: " . implode(', ', array_keys($json)) . "<br>";
        }
    }
    
    // Try to create Dialogflow service
    echo "<h3>Dialogflow Service Test:</h3>";
    try {
        $service = new \App\Services\DialogflowService();
        echo "✅ Service created successfully<br>";
        
        // Try to list intents
        echo "Trying to list intents...<br>";
        $intents = $service->listIntents();
        echo "✅ Intents fetched: " . count($intents) . "<br>";
        
        $service->close();
        
    } catch (\Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "<br>";
        echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    }
    
    return '';
});

    // In web.php, add this route:
Route::get('/debug-dialogflow-sync', function() {
    try {
        echo "<h1>Dialogflow Sync Debug</h1>";
        
        // 1. Check authentication
        if (!Auth::check()) {
            echo "❌ Not authenticated<br>";
            return;
        }
        
        $user = Auth::user();
        echo "✅ User: {$user->email} ({$user->role})<br>";
        
        if (!in_array($user->role, ['Admin', 'HR'])) {
            echo "❌ User not authorized (requires Admin or HR)<br>";
            return;
        }
        
        // 2. Check DialogflowService
        echo "<h2>DialogflowService Check</h2>";
        if (!class_exists('App\Services\DialogflowService')) {
            echo "❌ DialogflowService class not found<br>";
            return;
        }
        echo "✅ DialogflowService class exists<br>";
        
        // 3. Try to instantiate
        try {
            $service = new \App\Services\DialogflowService();
            echo "✅ DialogflowService instantiated<br>";
            
            // 4. Try to list intents
            $intents = $service->listIntents();
            echo "✅ Intents fetched: " . count($intents) . "<br>";
            
            if (count($intents) > 0) {
                echo "<pre>First intent: " . json_encode($intents[0], JSON_PRETTY_PRINT) . "</pre>";
            }
            
            $service->close();
            
        } catch (\Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "<br>";
            echo "<pre>Stack trace:\n" . $e->getTraceAsString() . "</pre>";
        }
        
    } catch (\Exception $e) {
        echo "❌ Debug error: " . $e->getMessage() . "<br>";
        echo "<pre>Stack trace:\n" . $e->getTraceAsString() . "</pre>";
    }
    
    return '';
});

    // Add to your web.php file temporarily
Route::get('/check-file-exists', function() {
    $filePath = env('DIALOGFLOW_CREDENTIALS_PATH', 'aihra-key.json');
    $fullPath = base_path($filePath);
    $absolutePath = realpath($fullPath);
    
    return response()->json([
        'env_value' => $filePath,
        'base_path' => base_path(),
        'full_path' => $fullPath,
        'absolute_path' => $absolutePath,
        'file_exists' => file_exists($fullPath),
        'is_readable' => is_readable($fullPath),
        'file_size' => file_exists($fullPath) ? filesize($fullPath) : 0,
        'permissions' => file_exists($fullPath) ? substr(sprintf('%o', fileperms($fullPath)), -4) : null,
        'file_content_preview' => file_exists($fullPath) ? 
            substr(file_get_contents($fullPath), 0, 200) . '...' : 
            null
    ]);
});
});

Route::get('/test-dialogflow', [App\Http\Controllers\DialogflowController::class, 'testConnection']);
