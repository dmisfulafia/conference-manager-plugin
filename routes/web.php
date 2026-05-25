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
        return view('dashboard');
    })->name('dashboard');
});
