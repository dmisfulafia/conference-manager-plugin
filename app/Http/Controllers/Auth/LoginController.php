<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            if (Auth::user()->email_verified_at === null) {
                return redirect()->route('verification.notice');
            }
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle login authentication.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->email_verified_at === null) {
                return redirect()->route('verification.notice');
            }

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withInput()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Show the email verification notice screen.
     */
    public function verifyEmailNotice()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->email_verified_at !== null) {
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('dashboard');
        }

        return view('auth.verify-email');
    }

    /**
     * Verify the 6-digit OTP code.
     */
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $verification = DB::table('verification_codes')
            ->where('user_id', $user->id)
            ->where('code', $request->code)
            ->first();

        if (!$verification) {
            return back()->with('error', 'Invalid verification code. Please check and try again.');
        }

        if (now()->gt($verification->expires_at)) {
            return back()->with('error', 'This verification code has expired. Please request a new one.');
        }

        // Verification successful
        DB::table('users')
            ->where('id', $user->id)
            ->update(['email_verified_at' => now()]);

        // Clean up the code
        DB::table('verification_codes')
            ->where('id', $verification->id)
            ->delete();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')
                ->with('success', 'Your account has been successfully verified! Welcome to your admin dashboard.');
        }

        return redirect()->route('dashboard')
            ->with('success', 'Your account has been successfully verified! Welcome to your dashboard.');
    }

    /**
     * Resend verification OTP code.
     */
    public function resendVerificationCode(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Clean up previous codes
        DB::table('verification_codes')
            ->where('user_id', $user->id)
            ->delete();

        // Generate a new 6-digit OTP code
        $code = rand(100000, 999999);

        DB::table('verification_codes')->insert([
            'user_id' => $user->id,
            'code' => $code,
            'expires_at' => now()->addMinutes(60),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Log::info("Verification OTP code RESENT for User: {$user->email} (ID: {$user->id}). OTP Code: {$code}");

        // Dispatch Email Verification Mailable
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)
                ->send(new \App\Mail\VerifyEmailCode($user, (string) $code));
            Log::info("Sent Resent Verification Email to user {$user->email} containing OTP code");
        } catch (\Exception $mailEx) {
            Log::error("Failed to send Resent Verification email to {$user->email}: " . $mailEx->getMessage());
        }

        return back()->with('success', 'A new verification code has been generated and sent to your registered email address.');
    }

    /**
     * Log out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'You have logged out successfully.');
    }
}
