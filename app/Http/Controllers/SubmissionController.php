<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\Submission;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SubmissionController extends Controller
{
    protected $cloudinary;

    public function __construct(CloudinaryService $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    /**
     * Submit an abstract for a conference registration.
     */
    public function submitAbstract(Request $request)
    {
        $request->validate([
            'registration_id' => 'required|exists:registrations,id',
            'title' => 'required|string|max:255',
            'abstract_text' => 'nullable|string',
            'abstract_file' => 'required|file|mimes:pdf,doc,docx|max:20480', // Max 20MB
        ]);

        $reg = Registration::findOrFail($request->registration_id);

        // Security check: Must belong to the logged-in user
        if ($reg->user_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized access to this registration.');
        }

        // Attendance payment check
        if (!$reg->is_attendance_paid) {
            return back()->with('error', 'You must complete your conference registration payment before submitting an abstract.');
        }

        $conf = $reg->conference;
        $submission = $reg->submission;
        $mustPayAbstract = floatval($conf->abstract_fee) > 0;

        if ($mustPayAbstract && (!$submission || !$submission->is_abstract_paid)) {
            return back()->with('error', 'You must pay the abstract submission fee before uploading your abstract.');
        }

        try {
            // Upload file to Cloudinary under the 'abstract' folder
            $fileUrl = $this->cloudinary->upload($request->file('abstract_file'), 'abstract');

            // Find or create submission record
            $submission = Submission::updateOrCreate(
                ['registration_id' => $reg->id],
                [
                    'title' => $request->title,
                    'abstract_text' => $request->abstract_text,
                    'abstract_file_path' => $fileUrl,
                    'abstract_status' => 'pending',
                    'is_abstract_paid' => true, // Verified paid or free
                ]
            );

            Log::info("Abstract Submitted for Reg ID {$reg->id} by User ID " . Auth::id());
            return back()->with('success', 'Your abstract has been successfully submitted! Our review board will notify you once verified.');
        } catch (\Exception $e) {
            Log::error("Abstract Submission Error: " . $e->getMessage());
            return back()->with('error', 'Unable to upload abstract file. Please try again.');
        }
    }

    /**
     * Submit a full paper once the abstract has been approved.
     */
    public function submitFullPaper(Request $request)
    {
        $request->validate([
            'submission_id' => 'required|exists:submissions,id',
            'full_paper_file' => 'required|file|mimes:pdf,doc,docx|max:20480', // Max 20MB
        ]);

        $submission = Submission::findOrFail($request->submission_id);
        $reg = $submission->registration;

        // Security check
        if ($reg->user_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized access to this submission.');
        }

        // Check if abstract is approved
        if ($submission->abstract_status !== 'approved') {
            return back()->with('error', 'You can only upload a full paper after your abstract has been approved.');
        }

        $conf = $reg->conference;
        $mustPayPaper = floatval($conf->full_paper_fee) > 0;

        if ($mustPayPaper && !$submission->is_full_paper_paid) {
            return back()->with('error', 'You must pay the full paper submission fee before uploading your full paper.');
        }

        try {
            // Upload file to Cloudinary under the 'full-paper-submissions' folder
            $fileUrl = $this->cloudinary->upload($request->file('full_paper_file'), 'full-paper-submissions');

            // Update submission
            $submission->update([
                'full_paper_file_path' => $fileUrl,
                'full_paper_status' => 'pending',
                'is_full_paper_paid' => true, // Verified paid or free
            ]);

            Log::info("Full Paper Submitted for Submission ID {$submission->id} by User ID " . Auth::id());
            return back()->with('success', 'Your full paper has been successfully submitted! The moderation team is currently reviewing it.');
        } catch (\Exception $e) {
            Log::error("Full Paper Submission Error: " . $e->getMessage());
            return back()->with('error', 'Unable to upload full paper file. Please try again.');
        }
    }
}
