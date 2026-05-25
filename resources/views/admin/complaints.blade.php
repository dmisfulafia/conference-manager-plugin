@extends('layouts.admin')

@section('title', 'Manage Tickets')
@section('page_title', 'Support Tickets Management')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm p-4 bg-white rounded-3" style="border: 1px solid rgba(157, 113, 38, 0.08) !important;">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h4 class="heading-font fw-bold text-academic-green mb-1"><i class="bi bi-chat-left-dots-fill text-fulafia-gold me-2"></i> User Support Complaints Desk</h4>
                        <p class="text-muted small mb-0">Review pending tickets, file resolutions, and communicate with conference attendees regarding platform issues.</p>
                    </div>
                    <div>
                        <div class="btn-group rounded-pill p-1 bg-light border" role="group">
                            <a href="{{ route('admin.complaints') }}" class="btn btn-sm px-3 rounded-pill fw-bold {{ !request()->has('status') ? 'btn-academic text-white' : 'btn-light text-muted' }}">All</a>
                            <a href="{{ route('admin.complaints', ['status' => 'pending']) }}" class="btn btn-sm px-3 rounded-pill fw-bold {{ request('status') === 'pending' ? 'btn-academic text-white' : 'btn-light text-muted' }}">Pending</a>
                            <a href="{{ route('admin.complaints', ['status' => 'resolved']) }}" class="btn btn-sm px-3 rounded-pill fw-bold {{ request('status') === 'resolved' ? 'btn-academic text-white' : 'btn-light text-muted' }}">Resolved</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm p-4 bg-white rounded-3" style="border: 1px solid rgba(157, 113, 38, 0.08) !important;">
                @if($complaints->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-chat-left-dots fs-1 opacity-45"></i>
                        <h5 class="mt-3 text-dark fw-bold">No Support Tickets Found</h5>
                        <p class="small text-muted mb-0">There are no complaints matching the selected filter in the system.</p>
                    </div>
                @else
                    <div class="d-flex flex-column gap-4">
                        @foreach($complaints as $ticket)
                            <div class="card border rounded-3 p-4 bg-white shadow-xs" style="border-color: rgba(157, 113, 38, 0.12) !important;">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 pb-3 border-bottom mb-3">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge bg-light text-muted border font-monospace small">#TKT-{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}</span>
                                            @if($ticket->status === 'pending')
                                                <span class="badge bg-warning text-dark small"><i class="bi bi-hourglass-split me-1"></i> Pending Reply</span>
                                            @else
                                                <span class="badge bg-success small"><i class="bi bi-patch-check-fill me-1"></i> Resolved</span>
                                            @endif
                                        </div>
                                        <h5 class="fw-bold text-academic-green mb-0">{{ $ticket->subject }}</h5>
                                    </div>
                                    <div class="text-end small">
                                        <div class="fw-bold text-dark">{{ $ticket->user->title }} {{ $ticket->user->name }}</div>
                                        <div class="text-muted">{{ $ticket->user->email }} | {{ $ticket->user->phone }}</div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <strong class="small text-muted d-block mb-1">Attendee Complaint Detail:</strong>
                                    <div class="bg-light p-3 rounded border small text-dark" style="white-space: pre-wrap;">{{ $ticket->message }}</div>
                                </div>

                                @if($ticket->status === 'pending')
                                    <!-- Pending: Show Reply Form -->
                                    <div class="border-top pt-3 mt-3">
                                        <form action="{{ route('admin.complaints.reply', $ticket->id) }}" method="POST">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-dark"><i class="bi bi-reply-fill text-academic-green"></i> Write Resolution & Reply</label>
                                                <textarea name="admin_reply" class="form-control" rows="3" placeholder="Provide a helpful response or resolution details for the attendee..." required></textarea>
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-academic btn-sm px-4 py-2 fw-bold shadow-sm rounded-3">
                                                    <i class="bi bi-patch-check-fill me-1"></i> Send Reply & Mark Resolved
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @else
                                    <!-- Resolved: Show Admin Response -->
                                    <div class="bg-success-subtle text-success-emphasis p-3 rounded-3 border border-success-subtle small mt-2">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong><i class="bi bi-chat-left-quote-fill me-2"></i> Administrative Reply Sent:</strong>
                                            <span class="text-muted small">{{ $ticket->updated_at->format('M d, Y h:i A') }}</span>
                                        </div>
                                        <p class="mb-0" style="white-space: pre-wrap;">{{ $ticket->admin_reply }}</p>
                                    </div>
                                @endif

                                <div class="text-muted small mt-3 pt-2 border-top d-flex justify-content-between">
                                    <span>Filed: {{ $ticket->created_at->format('M d, Y h:i A') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        {{ $complaints->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
