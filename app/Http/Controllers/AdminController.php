<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Conference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\CloudinaryService;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard panel.
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_verified' => User::where('role', 'user')->whereNotNull('email_verified_at')->count(),
            'total_unverified' => User::where('role', 'user')->whereNull('email_verified_at')->count(),
            'total_admins' => User::whereIn('role', ['admin', 'super_admin'])->count(),
            'total_conferences' => Conference::count(),
            'total_payments' => DB::table('payments')->where('status', 'successful')->sum('amount') ?? 0,
        ];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * List all attendees/users and admins.
     */
    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.users', compact('users'));
    }

    /**
     * Change a verified user's password.
     */
    public function changePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $currentUser = Auth::user();

        // Strict security checks:
        // 1. Only verified accounts can have their passwords changed (from user request description)
        if ($user->email_verified_at === null) {
            return back()->with('error', 'Password can only be changed for verified accounts. Unverified accounts should verify their email or be deleted.');
        }

        // 2. Only Super Admin can change password of other Admins/Super Admins
        if ($user->isAdmin() && !$currentUser->isSuperAdmin()) {
            return back()->with('error', 'Unauthorized. Only the Super Admin can change another administrator\'s password.');
        }

        try {
            $user->password = Hash::make($request->password);
            $user->save();

            Log::info("Admin (ID: {$currentUser->id}) changed password for User: {$user->email} (ID: {$user->id})");

            return back()->with('success', "Password for {$user->title} {$user->first_name} {$user->last_name} has been successfully updated!");
        } catch (\Exception $e) {
            Log::error("Password change error: " . $e->getMessage());
            return back()->with('error', 'An error occurred while updating the password.');
        }
    }

    /**
     * Delete an unverified user account.
     */
    public function deleteUnverifiedUser(User $user)
    {
        $currentUser = Auth::user();

        // Security check:
        // 1. Can only delete unverified accounts (as specified: "should be able to delete only unverified accounts")
        if ($user->email_verified_at !== null) {
            return back()->with('error', 'Unauthorized action. Verified member accounts cannot be deleted for audit and data integrity compliance.');
        }

        // 2. Prevent deleting other admins
        if ($user->isAdmin()) {
            return back()->with('error', 'Unauthorized. Cannot delete administrative accounts.');
        }

        try {
            $name = "{$user->title} {$user->first_name} {$user->last_name}";
            $user->delete();

            Log::info("Admin (ID: {$currentUser->id}) deleted unverified User: {$user->email}");

            return back()->with('success', "Unverified account for {$name} has been successfully deleted.");
        } catch (\Exception $e) {
            Log::error("User deletion error: " . $e->getMessage());
            return back()->with('error', 'An error occurred while deleting the account.');
        }
    }

    /**
     * Add a new Administrator account (Super Admin only).
     */
    public function addAdmin(Request $request)
    {
        // Double check super admin role
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized. Super Admin access only.');
        }

        $request->validate([
            'title' => 'required|string|max:10',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'other_names' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'gender' => 'required|string|in:Male,Female,Other',
            'occupation' => 'required|string|max:255',
            'country' => 'required|string|max:100',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:admin,super_admin',
        ]);

        try {
            User::create([
                'title' => $request->title,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'other_names' => $request->other_names,
                'email' => $request->email,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'occupation' => $request->occupation,
                'country' => $request->country,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'email_verified_at' => now(), // Admin accounts are pre-verified
            ]);

            Log::info("Super Admin (ID: " . Auth::id() . ") added new admin: {$request->email} with role: {$request->role}");

            return back()->with('success', "New administrator account has been successfully created!");
        } catch (\Exception $e) {
            Log::error("Admin creation error: " . $e->getMessage());
            return back()->with('error', 'An error occurred while creating the administrator account.');
        }
    }

    /**
     * List conferences.
     */
    public function conferences()
    {
        $conferences = Conference::with('attendeeTypes')->orderBy('start_date', 'desc')->get();
        return view('admin.conferences', compact('conferences'));
    }

    /**
     * Create a new conference.
     */
    public function storeConference(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'venue' => 'required|string|max:255',
            'status' => 'required|string|in:ongoing,past',
            'accommodation_fee' => 'required|numeric|min:0',
            'conference_material_fee' => 'required|numeric|min:0',
            
            // Category attendee fees
            'researchers_fee' => 'required|numeric|min:0',
            'postgraduate_fee' => 'required|numeric|min:0',
            'undergraduate_fee' => 'required|numeric|min:0',
            'corporate_fee' => 'required|numeric|min:0',
            'international_fee' => 'required|numeric|min:0',
            'virtual_fee' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $conference = Conference::create([
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'venue' => $request->venue,
                'status' => $request->status,
                'accommodation_fee' => $request->accommodation_fee,
                'conference_material_fee' => $request->conference_material_fee,
            ]);

            // Store the 6 specific attendee categories
            $categories = [
                'Researchers' => $request->researchers_fee,
                'Postgraduate Students' => $request->postgraduate_fee,
                'Undergraduate Students' => $request->undergraduate_fee,
                'Corporate Bodies' => $request->corporate_fee,
                'International attendee' => $request->international_fee,
                'Virtual Attendee' => $request->virtual_fee,
            ];

            foreach ($categories as $name => $fee) {
                DB::table('attendee_types')->insert([
                    'conference_id' => $conference->id,
                    'name' => $name,
                    'fee' => $fee,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return back()->with('success', 'Conference created successfully with designated attendee category fees!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Conference creation error: " . $e->getMessage());
            return back()->with('error', 'An error occurred while creating the conference.');
        }
    }

    /**
     * Update an existing conference.
     */
    public function updateConference(Request $request, Conference $conference)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'venue' => 'required|string|max:255',
            'status' => 'required|string|in:ongoing,past',
            'accommodation_fee' => 'required|numeric|min:0',
            'conference_material_fee' => 'required|numeric|min:0',
            
            // Category attendee fees
            'researchers_fee' => 'required|numeric|min:0',
            'postgraduate_fee' => 'required|numeric|min:0',
            'undergraduate_fee' => 'required|numeric|min:0',
            'corporate_fee' => 'required|numeric|min:0',
            'international_fee' => 'required|numeric|min:0',
            'virtual_fee' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $conference->update([
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'venue' => $request->venue,
                'status' => $request->status,
                'accommodation_fee' => $request->accommodation_fee,
                'conference_material_fee' => $request->conference_material_fee,
            ]);

            // Update the 6 specific attendee categories
            $categories = [
                'Researchers' => $request->researchers_fee,
                'Postgraduate Students' => $request->postgraduate_fee,
                'Undergraduate Students' => $request->undergraduate_fee,
                'Corporate Bodies' => $request->corporate_fee,
                'International attendee' => $request->international_fee,
                'Virtual Attendee' => $request->virtual_fee,
            ];

            foreach ($categories as $name => $fee) {
                DB::table('attendee_types')->updateOrInsert(
                    ['conference_id' => $conference->id, 'name' => $name],
                    ['fee' => $fee, 'updated_at' => now()]
                );
            }

            DB::commit();

            return back()->with('success', 'Conference details and attendee category fees updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Conference update error: " . $e->getMessage());
            return back()->with('error', 'An error occurred while updating the conference.');
        }
    }

    /**
     * Delete a conference.
     */
    public function deleteConference(Conference $conference)
    {
        try {
            $conference->delete();
            return back()->with('success', 'Conference has been successfully deleted.');
        } catch (\Exception $e) {
            Log::error("Conference deletion error: " . $e->getMessage());
            return back()->with('error', 'An error occurred while deleting the conference.');
        }
    }

    /**
     * Verify or Reject a student's ID Card.
     */
    public function verifyStudentId(Request $request, User $user, CloudinaryService $cloudinary)
    {
        $request->validate([
            'status' => 'required|string|in:verify,reject',
        ]);

        try {
            if ($request->status === 'verify') {
                $user->update(['student_id_verified' => true]);
                Log::info("Admin (ID: " . Auth::id() . ") verified Student ID for User: {$user->email}");
                return back()->with('success', "Student ID for {$user->title} {$user->first_name} {$user->last_name} has been successfully verified!");
            } else {
                // Reject - reset verification and delete the file so they can re-upload
                if ($user->student_id_card) {
                    if (str_starts_with($user->student_id_card, 'http')) {
                        $cloudinary->delete($user->student_id_card);
                    } else {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($user->student_id_card);
                    }
                }
                $user->update([
                    'student_id_card' => null,
                    'student_id_verified' => false,
                ]);
                Log::info("Admin (ID: " . Auth::id() . ") rejected and deleted Student ID for User: {$user->email}");
                return back()->with('success', "Student ID for {$user->title} {$user->first_name} {$user->last_name} has been rejected and cleared. The user can now upload a fresh document.");
            }
        } catch (\Exception $e) {
            Log::error("Student ID verification error: " . $e->getMessage());
            return back()->with('error', 'An error occurred while verifying the Student ID: ' . $e->getMessage());
        }
    }
}
