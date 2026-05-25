@extends('layouts.member')

@section('title', 'Support Tickets')
@section('page_title', 'Support & Complaints Desk')

@section('content')
    <div class="row g-4">
        <!-- Ticket Filing Section -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 rounded-3 bg-white" style="border: 1px solid rgba(157, 113, 38, 0.08) !important; position: sticky; top: 30px;">
                <h4 class="heading-font fw-extrabold text-academic-green mb-3 border-bottom pb-2">
                    <i class="bi bi-chat-left-text-fill text-fulafia-gold me-2"></i> Submit a Ticket
                </h4>
                <p class="text-muted small mb-4">Experiencing payment verification issues, credential issues, or technical portal bugs? File a formal complaint below.</p>
                
                <form action="{{ route('complaints.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Subject / Nature of Complaint</label>
                        <input type="text" name="subject" class="form-control" placeholder="e.g. Payment successful but still pending" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Detailed Explanation</label>
                        <textarea name="message" class="form-control" rows="5" placeholder="Please provide all relevant details (e.g. payment date, Credo reference, specific error message) so we can assist you quickly..." required style="resize: none;"></textarea>
                    </div>

                    <button type="submit" class="btn btn-gold w-100 py-2.5 rounded-3 fw-bold shadow-sm">
                        <i class="bi bi-send-fill me-2"></i> Submit Complaint
                    </button>
                </form>
            </div>
        </div>

        <!-- Past Complaints / Tickets Section -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 rounded-3 bg-white" style="border: 1px solid rgba(157, 113, 38, 0.08) !important;">
                <h4 class="heading-font fw-extrabold text-academic-green mb-4 border-bottom pb-2 d-flex align-items-center">
                    <i class="bi bi-clock-history text-fulafia-gold me-2"></i> My Support History
                </h4>

                @if($complaints->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-chat-square-text fs-1 text-muted opacity-45"></i>
                        <p class="mt-3 fs-6 mb-1 text-dark">No support tickets found</p>
                        <p class="small text-muted mb-0">If you have any issues, use the left form to submit a new ticket directly to our admin team.</p>
                    </div>
                @else
                    <div class="d-flex flex-column gap-4">
                        @foreach($complaints as $ticket)
                            <div class="card border rounded-3 p-4 bg-white shadow-xs" style="border-color: rgba(157, 113, 38, 0.12) !important;">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3 pb-2 border-bottom">
                                    <div>
                                        <span class="badge bg-light text-muted border font-monospace small mb-1">#TKT-{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}</span>
                                        <h5 class="fw-bold text-academic-green mb-0">{{ $ticket->subject }}</h5>
                                    </div>
                                    <div>
                                        @if($ticket->status === 'pending')
                                            <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill fw-bold"><i class="bi bi-hourglass-split me-1"></i> Awaiting Feedback</span>
                                        @else
                                            <span class="badge bg-success px-3 py-1.5 rounded-pill fw-bold"><i class="bi bi-patch-check-fill me-1"></i> Resolved</span>
                                        @endif
                                    </div>
                                </div>

                                <p class="text-dark small mb-3 bg-light p-3 rounded border" style="white-space: pre-wrap;">{{ $ticket->message }}</p>

                                @if($ticket->status === 'resolved' && $ticket->admin_reply)
                                    <div class="bg-success-subtle text-success-emphasis p-3 rounded-3 border border-success-subtle small mt-2">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong><i class="bi bi-chat-left-quote-fill me-2"></i> Administrative Resolution Response:</strong>
                                            <span class="text-muted small">{{ $ticket->updated_at->format('M d, Y h:i A') }}</span>
                                        </div>
                                        <p class="mb-0" style="white-space: pre-wrap;">{{ $ticket->admin_reply }}</p>
                                    </div>
                                @endif
                                
                                <div class="text-muted small mt-2 d-flex justify-content-between align-items-center">
                                    <span>Submitted: {{ $ticket->created_at->format('M d, Y h:i A') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
