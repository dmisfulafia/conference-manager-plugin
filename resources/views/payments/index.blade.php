@extends('layouts.member')

@section('title', 'My Payments')
@section('page_title', 'Payment Transactions')

@section('styles')
    /* Elegant stat cards */
    .stat-card {
        background-color: #ffffff;
        border-radius: 12px;
        border: 1px solid rgba(157, 113, 38, 0.08);
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
        padding: 24px;
        height: 100%;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04);
    }

    .stat-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--fulafia-gold-light, #f7eecd);
        color: var(--fulafia-gold);
        font-size: 1.4rem;
        margin-bottom: 16px;
    }

    .stat-card-icon.success {
        background-color: #d1fae5;
        color: #059669;
    }

    .stat-card-icon.pending {
        background-color: #fef3c7;
        color: #d97706;
    }

    .table-container {
        background-color: #ffffff;
        border-radius: 12px;
        border: 1px solid rgba(157, 113, 38, 0.08);
        padding: 25px;
    }

    .table th {
        font-family: 'Outfit', sans-serif;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        background-color: #f9fafb;
    }

    .animate-pulse {
        animation: pulse-animation 2s infinite;
    }

    @keyframes pulse-animation {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
@endsection

@section('content')
    @php
        $user = Auth::user();
        $pendingCount = $user->payments()->where('status', 'pending')->count();
        $successfulCount = $user->payments()->where('status', 'successful')->count();
    @endphp

    <!-- Stat Cards Summary -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="stat-card shadow-sm border-0">
                <div class="stat-card-icon success"><i class="bi bi-wallet2"></i></div>
                <h6 class="text-muted small uppercase fw-bold">Total Successful Payments</h6>
                <h3 class="heading-font fw-extrabold mt-1">₦{{ number_format($totalPayments, 2) }}</h3>
                <p class="text-success small mb-0"><i class="bi bi-patch-check-fill me-1"></i> Paid to FULafia Account</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card shadow-sm border-0">
                <div class="stat-card-icon pending"><i class="bi bi-hourglass-split"></i></div>
                <h6 class="text-muted small uppercase fw-bold">Pending Transactions</h6>
                <h3 class="heading-font fw-extrabold mt-1">{{ $pendingCount }}</h3>
                @if($pendingCount > 0)
                    <p class="text-warning small mb-0"><i class="bi bi-exclamation-circle-fill me-1"></i> Awaiting gateway verification</p>
                @else
                    <p class="text-success small mb-0"><i class="bi bi-check-circle-fill me-1"></i> All transactions completed</p>
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card shadow-sm border-0">
                <div class="stat-card-icon"><i class="bi bi-credit-card-2-front"></i></div>
                <h6 class="text-muted small uppercase fw-bold">Total Transactions Logs</h6>
                <h3 class="heading-font fw-extrabold mt-1">{{ $payments->total() }}</h3>
                <p class="text-muted small mb-0"><i class="bi bi-clock-history me-1"></i> Lifetime transaction count</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Transaction Logs -->
        <div class="col-lg-8">
            <div class="table-container shadow-sm">
                <h4 class="heading-font fw-bold text-academic-green mb-4 border-bottom pb-2 d-flex align-items-center">
                    <i class="bi bi-receipt text-fulafia-gold me-2"></i> Payment Invoices & Billing Logs
                </h4>

                @if($payments->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-credit-card fs-1 text-muted opacity-40"></i>
                        <p class="mt-3 fs-6 mb-2">No payment transaction records found on your account.</p>
                        <p class="small text-muted mb-3">When you register for conferences or request premium services, your bills will populate here.</p>
                        <a href="{{ route('conferences.index') }}" class="btn btn-academic btn-sm px-4 rounded-pill shadow-sm">
                            <i class="bi bi-calendar-event me-1"></i> Browse Conferences
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th>Ref ID</th>
                                    <th>Conference</th>
                                    <th>Purpose</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $payment)
                                    <tr>
                                        <td class="font-monospace fw-bold text-dark small" title="{{ $payment->reference }}">
                                            {{ Str::limit($payment->reference, 15) }}
                                        </td>
                                        <td>
                                            @if($payment->registration && $payment->registration->conference)
                                                <span class="fw-bold text-academic-green small d-block text-truncate" style="max-width: 180px;">
                                                    {{ $payment->registration->conference->title }}
                                                </span>
                                            @else
                                                <span class="text-muted small">Generic Portal Fee</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark text-capitalize border small" style="font-size: 0.75rem;">
                                                {{ str_replace('_', ' ', $payment->purpose) }}
                                            </span>
                                        </td>
                                        <td class="fw-bold text-academic-green small">₦{{ number_format($payment->amount, 2) }}</td>
                                        <td>
                                            @if($payment->status === 'successful')
                                                <span class="badge bg-success px-2.5 py-1 rounded-pill small">
                                                    <i class="bi bi-check-circle-fill me-1"></i> Success
                                                </span>
                                            @elseif($payment->status === 'failed')
                                                <span class="badge bg-danger px-2.5 py-1 rounded-pill small">
                                                    <i class="bi bi-x-circle-fill me-1"></i> Failed
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill small animate-pulse">
                                                    <i class="bi bi-hourglass-split me-1"></i> Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex gap-1 justify-content-center">
                                                @if($payment->status === 'successful')
                                                    <a href="{{ route('payment.receipt', $payment->id) }}" target="_blank" class="btn btn-outline-success btn-xs px-2 py-1 rounded small" style="font-size: 0.75rem;">
                                                        <i class="bi bi-printer me-1"></i> Receipt
                                                    </a>
                                                @elseif($payment->status === 'pending')
                                                    <!-- Re-verify action -->
                                                    <form action="{{ route('payment.reverify') }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="reference" value="{{ $payment->reference }}">
                                                        <button type="submit" class="btn btn-outline-warning btn-xs px-2 py-1 rounded small me-1" style="font-size: 0.75rem;">
                                                            <i class="bi bi-arrow-repeat me-1"></i> Verify
                                                        </button>
                                                    </form>

                                                    @if($payment->registration)
                                                        <!-- Resume secure pop-up checkout -->
                                                        <form action="{{ route('payment.checkout') }}" method="POST" class="d-inline inline-checkout-form">
                                                            @csrf
                                                            <input type="hidden" name="conference_id" value="{{ $payment->registration->conference_id }}">
                                                            <input type="hidden" name="attendee_type_id" value="{{ $payment->registration->attendee_type_id }}">
                                                            <input type="hidden" name="wants_accommodation" value="{{ $payment->registration->wants_accommodation ? '1' : '0' }}">
                                                            <input type="hidden" name="wants_materials" value="{{ $payment->registration->wants_materials ? '1' : '0' }}">
                                                            <button type="submit" class="btn btn-gold btn-xs px-2 py-1 rounded small fw-bold" style="font-size: 0.75rem;">
                                                                <i class="bi bi-wallet2 me-1"></i> Pay
                                                            </button>
                                                        </form>
                                                    @endif
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Custom Paginate Links -->
                    <div class="mt-4">
                        {{ $payments->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar Actions Panel -->
        <div class="col-lg-4 d-flex flex-column gap-4">
            <!-- Manual Re-verification Card -->
            <div class="card border-0 shadow-sm p-4 rounded-3 bg-white" style="border: 1px solid rgba(157, 113, 38, 0.08) !important;">
                <h5 class="heading-font fw-bold text-academic-green mb-3 border-bottom pb-2">
                    <i class="bi bi-shield-check me-2 text-fulafia-gold"></i> Re-verify Payment
                </h5>
                <p class="text-muted small">Did you make a payment using Credo but it is still showing as pending in your list? Enter the exact transaction reference below to query the gateway instantly.</p>
                
                <form action="{{ route('payment.reverify') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Transaction Reference</label>
                        <input type="text" name="reference" class="form-control" placeholder="e.g. FUL_1716584283_3821" required style="font-family: monospace;">
                    </div>
                    <button type="submit" class="btn btn-gold w-100 py-2"><i class="bi bi-arrow-repeat me-1"></i> Query Transaction Status</button>
                </form>
            </div>

            <!-- Billing Support Card -->
            <div class="card border-0 shadow-sm p-4 rounded-3 bg-white" style="border: 1px solid rgba(157, 113, 38, 0.08) !important;">
                <h5 class="heading-font fw-bold text-academic-green mb-3 border-bottom pb-2">Billing Help & Support</h5>
                <p class="text-muted small">If your card was charged but the transaction remains failed or pending even after verifying, please submit a formal complaint ticket stating the reference, amount, and date.</p>
                <a href="{{ route('complaints.index') }}" class="btn btn-outline-success w-100 py-2 mt-auto">
                    <i class="bi bi-chat-left-dots-fill me-2"></i> File Billing Complaint
                </a>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Secure Inline Checkout Script -->
    <script>
        $(document).ready(function() {
            // Intercept checkout form submission for inline payment gateway integration
            $(document).on('submit', '.inline-checkout-form', function(e) {
                e.preventDefault();
                let $form = $(this);
                let $btn = $form.find('button[type="submit"]');
                let originalText = $btn.html();
                
                // Show loading spinner on payment trigger button
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    headers: {
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Open secure centered popup window pointing directly to the payment link
                            let width = 500;
                            let height = 700;
                            let left = (screen.width / 2) - (width / 2);
                            let top = (screen.height / 2) - (height / 2);
                            
                            let popup = window.open(response.payment_link, 'CredoCheckout', 'width=' + width + ',height=' + height + ',top=' + top + ',left=' + left + ',resizable=yes,scrollbars=yes,status=yes');
                            
                            $btn.html('<i class="bi bi-hourglass-split animate-pulse"></i>');

                            // Track popup state
                            let timer = setInterval(function() {
                                if (popup.closed) {
                                    clearInterval(timer);
                                    window.location.reload();
                                }
                            }, 1500);
                        } else {
                            alert(response.message || "Unable to initiate payment popup. Please try again.");
                            $btn.prop('disabled', false).html(originalText);
                        }
                    },
                    error: function(xhr) {
                        let errMsg = "An error occurred during checkout setup. Please try again.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        }
                        alert(errMsg);
                        $btn.prop('disabled', false).html(originalText);
                    }
                });
            });
        });
    </script>
@endsection
