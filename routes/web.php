<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;

// Redirect home directly to the login portal
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes (Guest only)
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Logout (Accessible to logged in users)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Email Verification Routes (Authenticated but not necessarily verified)
Route::middleware('auth')->group(function () {
    Route::get('/verify-email', [LoginController::class, 'verifyEmailNotice'])->name('verification.notice');
    Route::post('/verify-email', [LoginController::class, 'verifyEmail'])->name('verification.verify');
    Route::post('/verify-email/resend', [LoginController::class, 'resendVerificationCode'])->name('verification.resend');
});

// Protected Member Dashboard (Authenticated AND Verified)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $ongoingConferences = \App\Models\Conference::with('attendeeTypes')
            ->where('status', 'ongoing')
            ->orderBy('start_date', 'asc')
            ->get();
        return view('dashboard', compact('ongoingConferences'));
    })->name('dashboard');
});

// Protected Administration Panel (Authenticated, Verified, AND Admin/Super Admin)
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->group(function () {
    // Dashboard overview
    Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Manage accounts
    Route::get('/users', [\App\Http\Controllers\AdminController::class, 'users'])->name('admin.users');
    Route::put('/users/{user}/password', [\App\Http\Controllers\AdminController::class, 'changePassword'])->name('admin.users.password');
    Route::delete('/users/{user}', [\App\Http\Controllers\AdminController::class, 'deleteUnverifiedUser'])->name('admin.users.delete');
    Route::post('/add-admin', [\App\Http\Controllers\AdminController::class, 'addAdmin'])->name('admin.add-admin');

    // Manage conferences
    Route::get('/conferences', [\App\Http\Controllers\AdminController::class, 'conferences'])->name('admin.conferences');
    Route::post('/conferences', [\App\Http\Controllers\AdminController::class, 'storeConference'])->name('admin.conferences.store');
    Route::put('/conferences/{conference}', [\App\Http\Controllers\AdminController::class, 'updateConference'])->name('admin.conferences.update');
    Route::delete('/conferences/{conference}', [\App\Http\Controllers\AdminController::class, 'deleteConference'])->name('admin.conferences.delete');
});
