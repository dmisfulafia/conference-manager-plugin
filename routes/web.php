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
        $user = Auth::user();
        
        $ongoingConferences = \App\Models\Conference::with('attendeeTypes')
            ->where('status', 'ongoing')
            ->orderBy('start_date', 'asc')
            ->get();
            
        $registrationsCount = $user->registrations()->count();
        $submissionsCount = $user->submissions()->count();
        
        $totalPayments = $user->payments()
            ->where('status', 'successful')
            ->sum('amount');
            
        $recentPayments = $user->payments()
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $myRegistrations = $user->registrations()
            ->with(['conference', 'attendeeType', 'submission'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('dashboard', compact(
            'ongoingConferences',
            'registrationsCount',
            'submissionsCount',
            'totalPayments',
            'recentPayments',
            'myRegistrations'
        ));
    })->name('dashboard');

    // Profile Management
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/passport', [\App\Http\Controllers\ProfileController::class, 'uploadPassport'])->name('profile.upload-passport');
    Route::put('/profile/student-id', [\App\Http\Controllers\ProfileController::class, 'uploadStudentId'])->name('profile.upload-student-id');

    // Payment Gateway & Credo Checkout
    Route::post('/payment/checkout', [\App\Http\Controllers\PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::get('/payment/callback', [\App\Http\Controllers\PaymentController::class, 'callback'])->name('payment.callback');
    Route::post('/payment/reverify', [\App\Http\Controllers\PaymentController::class, 'reverify'])->name('payment.reverify');

    // Structured Submissions
    Route::post('/submissions/abstract', [\App\Http\Controllers\SubmissionController::class, 'submitAbstract'])->name('submissions.abstract');
    Route::post('/submissions/full-paper', [\App\Http\Controllers\SubmissionController::class, 'submitFullPaper'])->name('submissions.full-paper');
});

// Protected Administration Panel (Authenticated, Verified, AND Admin/Super Admin)
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->group(function () {
    // Dashboard overview
    Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Manage accounts
    Route::get('/users', [\App\Http\Controllers\AdminController::class, 'users'])->name('admin.users');
    Route::put('/users/{user}/password', [\App\Http\Controllers\AdminController::class, 'changePassword'])->name('admin.users.password');
    Route::delete('/users/{user}', [\App\Http\Controllers\AdminController::class, 'deleteUnverifiedUser'])->name('admin.users.delete');
    Route::put('/users/{user}/verify-student-id', [\App\Http\Controllers\AdminController::class, 'verifyStudentId'])->name('admin.users.verify-student-id');
    Route::post('/add-admin', [\App\Http\Controllers\AdminController::class, 'addAdmin'])->name('admin.add-admin');

    // Manage conferences
    Route::get('/conferences', [\App\Http\Controllers\AdminController::class, 'conferences'])->name('admin.conferences');
    Route::post('/conferences', [\App\Http\Controllers\AdminController::class, 'storeConference'])->name('admin.conferences.store');
    Route::put('/conferences/{conference}', [\App\Http\Controllers\AdminController::class, 'updateConference'])->name('admin.conferences.update');
    Route::delete('/conferences/{conference}', [\App\Http\Controllers\AdminController::class, 'deleteConference'])->name('admin.conferences.delete');
});
