<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Services\CloudinaryService;

class ProfileController extends Controller
{
    /**
     * Show the member profile page.
     */
    public function show()
    {
        return view('profile');
    }

    /**
     * Update personal profile details.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'title' => 'required|string|in:Prof.,Dr.,Mr.,Mrs.,Ms.',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'other_names' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'gender' => 'required|string|in:Male,Female,Other',
            'country' => 'required|string|in:Nigeria,Ghana,United Kingdom,United States,Canada,South Africa,Kenya,Other',
            'address' => 'nullable|string|max:500',
            'occupation' => 'required|string|max:255',
            'institution' => 'nullable|string|max:255',
            'next_of_kin' => 'nullable|string|max:255',
            'next_of_kin_phone' => 'nullable|string|max:20',
        ]);

        try {
            // Check if occupation has changed from student to non-student
            $oldOccupation = $user->occupation;
            $newOccupation = $request->occupation;
            
            $wasStudent = stripos($oldOccupation, 'student') !== false;
            $isStudent = stripos($newOccupation, 'student') !== false;

            // Update user details
            $user->update([
                'title' => $request->title,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'other_names' => $request->other_names,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'country' => $request->country,
                'address' => $request->address,
                'occupation' => $request->occupation,
                'institution' => $request->institution,
                'next_of_kin' => $request->next_of_kin,
                'next_of_kin_phone' => $request->next_of_kin_phone,
            ]);

            // If user changed status to student, prompt them to upload ID.
            // If they changed FROM student to something else, reset student_id_verified to false for audit trail integrity.
            if ($wasStudent && !$isStudent) {
                $user->update(['student_id_verified' => false]);
            }

            Log::info("User ID {$user->id} updated their profile details.");

            return back()->with('success', 'Profile details have been successfully updated!');
        } catch (\Exception $e) {
            Log::error("Profile update error: " . $e->getMessage());
            return back()->with('error', 'An error occurred while saving your profile details.');
        }
    }

    /**
     * Upload passport photo.
     */
    public function uploadPassport(Request $request, CloudinaryService $cloudinary)
    {
        $request->validate([
            'passport_photo' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
        ]);

        $user = Auth::user();

        try {
            if ($request->hasFile('passport_photo')) {
                // Delete existing passport if present
                if ($user->passport_photo) {
                    if (str_starts_with($user->passport_photo, 'http')) {
                        $cloudinary->delete($user->passport_photo);
                    } else {
                        Storage::disk('public')->delete($user->passport_photo);
                    }
                }

                // Upload to Cloudinary in the 'passports' folder
                $secureUrl = $cloudinary->upload($request->file('passport_photo'), 'passports');

                $user->update([
                    'passport_photo' => $secureUrl,
                ]);

                Log::info("User ID {$user->id} uploaded passport photo to Cloudinary.");

                return back()->with('success', 'Passport photograph uploaded successfully to Cloudinary!');
            }

            return back()->with('error', 'Please select a valid image file to upload.');
        } catch (\Exception $e) {
            Log::error("Passport photo upload error: " . $e->getMessage());
            return back()->with('error', 'An error occurred while uploading your passport photograph: ' . $e->getMessage());
        }
    }

    /**
     * Upload student ID card.
     */
    public function uploadStudentId(Request $request, CloudinaryService $cloudinary)
    {
        $request->validate([
            'student_id_card' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120',
        ]);

        $user = Auth::user();

        try {
            if ($request->hasFile('student_id_card')) {
                // Check if user is student
                if (stripos($user->occupation, 'student') === false) {
                    return back()->with('error', 'Student ID Card upload is only applicable for student accounts. Please update your occupation first.');
                }

                // Delete existing student ID if present
                if ($user->student_id_card) {
                    if (str_starts_with($user->student_id_card, 'http')) {
                        $cloudinary->delete($user->student_id_card);
                    } else {
                        Storage::disk('public')->delete($user->student_id_card);
                    }
                }

                // Upload new ID Card to Cloudinary passports folder
                $secureUrl = $cloudinary->upload($request->file('student_id_card'), 'passports');

                $user->update([
                    'student_id_card' => $secureUrl,
                    'student_id_verified' => false, // Reset verification status to false so admins can re-verify
                ]);

                Log::info("User ID {$user->id} uploaded student ID card to Cloudinary: {$secureUrl}");

                return back()->with('success', 'Student ID card uploaded successfully to Cloudinary and is now pending administrator verification.');
            }

            return back()->with('error', 'Please select a valid file to upload.');
        } catch (\Exception $e) {
            Log::error("Student ID card upload error: " . $e->getMessage());
            return back()->with('error', 'An error occurred while uploading your Student ID card: ' . $e->getMessage());
        }
    }
}
