<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    /**
     * Handle registration request.
     */
    public function register(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:10',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'other_names' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'gender' => 'required|string|in:Male,Female,Other',
            'occupation' => 'required|string|max:255',
            'institution' => 'nullable|string|max:255',
            'country' => 'required|string|max:100',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'title' => $request->title,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'other_names' => $request->other_names,
                'email' => $request->email,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'occupation' => $request->occupation,
                'institution' => $request->institution,
                'country' => $request->country,
                'password' => Hash::make($request->password),
                'role' => 'user',
            ]);

            // Generate a 6-digit OTP code for verification
            $code = rand(100000, 999999);

            DB::table('verification_codes')->insert([
                'user_id' => $user->id,
                'code' => $code,
                'expires_at' => now()->addMinutes(60),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            // Log code for easy developer sandbox retrieval
            Log::info("Verification OTP code generated for User: {$user->email} (ID: {$user->id}). OTP Code: {$code}");

            // Dispatch Email Verification Mailable
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)
                    ->send(new \App\Mail\VerifyEmailCode($user, (string) $code));
                Log::info("Sent Verification Email to user {$user->email} containing OTP code");
            } catch (\Exception $mailEx) {
                Log::error("Failed to send Verification email to {$user->email}: " . $mailEx->getMessage());
            }

            // Automatically log in the user
            Auth::login($user);

            // Notify user without exposing OTP code in frontend flash message
            return redirect()->route('verification.notice')
                ->with('success', 'Registration successful! A 6-digit verification code has been sent to your registered email address.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Registration error: " . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'An error occurred during registration. Please try again.']);
        }
    }
}
