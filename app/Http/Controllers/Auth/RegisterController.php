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
            'name' => 'required|string|max:255',
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
                'name' => $request->name,
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

            // Automatically log in the user
            Auth::login($user);

            // Flash the code for extremely easy local development testing!
            return redirect()->route('verification.notice')
                ->with('success', 'Registration successful! For easy testing, your OTP is: ' . $code);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Registration error: " . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'An error occurred during registration. Please try again.']);
        }
    }
}
