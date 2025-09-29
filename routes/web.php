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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/assigned-items', [App\Http\Controllers\StockController::class, 'index'])->name('admin.assigned-items.index');
    Route::get('/admin/assigned-items/{id}/edit', [App\Http\Controllers\StockController::class, 'edit'])->name('admin.assigned-items.edit');
    Route::put('/admin/assigned-items/{id}', [App\Http\Controllers\StockController::class, 'update'])->name('admin.assigned-items.update');
    Route::delete('/admin/assigned-items/{id}', [App\Http\Controllers\StockController::class, 'destroy'])->name('admin.assigned-items.destroy');
});

Route::middleware(['auth', 'role:HR'])->group(function () {
    Route::get('/hr', [HRController::class, 'index'])->name('hr.dashboard');
});

Route::middleware(['auth', 'role:Employee'])->group(function () {
    Route::get('/employee', [EmployeeController::class, 'index'])->name('employee.dashboard');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
    Route::post('/flag', [FlagController::class, 'store'])->name('flag.store');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/employee', [EmployeeController::class, 'index'])->name('employee.dashboard');
    Route::get('/hr', [HRController::class, 'index'])->name('hr.dashboard');
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
});


Route::post('/dialogflow-webhook', [DialogflowController::class, 'webhook'])->withoutMiddleware('csrf')->middleware('throttle:60,1');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    // Store announcement
    Route::post('/hr/announcements', [HrAnnouncementController::class, 'store'])->name('hr.announcements.store');

    // Optional: other HR routes
    Route::get('/hr', [HRController::class, 'index'])->name('hr.dashboard');
});
