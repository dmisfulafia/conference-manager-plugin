<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ComplaintController extends Controller
{
    /**
     * Display a listing of complaints for the logged-in user.
     */
    public function index()
    {
        $user = Auth::user();
        
        $complaints = $user->complaints()
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('complaints', compact('complaints'));
    }

    /**
     * Store a newly created complaint in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ]);

        try {
            Auth::user()->complaints()->create([
                'subject' => $request->subject,
                'message' => $request->message,
                'status' => 'pending',
            ]);

            Log::info("Complaint filed by User ID " . Auth::id());
            return back()->with('success', 'Your complaint/ticket has been successfully filed. Our support desk will look into it shortly.');
        } catch (\Exception $e) {
            Log::error("Complaint store error: " . $e->getMessage());
            return back()->with('error', 'Unable to submit your complaint. Please try again.');
        }
    }

    /**
     * Display admin index of complaints.
     */
    public function adminIndex(Request $request)
    {
        $status = $request->query('status');
        
        $query = Complaint::with('user')->orderBy('created_at', 'desc');
        
        if ($status && in_array($status, ['pending', 'resolved'])) {
            $query->where('status', $status);
        }
        
        $complaints = $query->paginate(15);
        
        return view('admin.complaints', compact('complaints'));
    }

    /**
     * Resolve and reply to a complaint.
     */
    public function adminReply(Request $request, Complaint $complaint)
    {
        $request->validate([
            'admin_reply' => 'required|string|min:5',
        ]);

        try {
            $complaint->update([
                'admin_reply' => $request->admin_reply,
                'status' => 'resolved',
            ]);

            Log::info("Complaint ID {$complaint->id} resolved by Admin ID " . Auth::id());
            return back()->with('success', 'Complaint resolved and reply sent successfully!');
        } catch (\Exception $e) {
            Log::error("Complaint admin reply error: " . $e->getMessage());
            return back()->with('error', 'Unable to resolve complaint. Please try again.');
        }
    }
}
