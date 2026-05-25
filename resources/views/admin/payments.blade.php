@extends('layouts.admin')

@section('title', 'Manage Payments')
@section('page_title', 'Global Payment Invoices & Revenue Logs')

@section('content')
@php
    $successfulTotal = $payments->where('status', 'successful')->sum('amount');
    $pendingTotal = $payments->where('status', 'pending')->sum('amount');
    $failedTotal = $payments->where('status', 'failed')->sum('amount');
    $successfulCount = $payments->where('status', 'successful')->count();
    $pendingCount = $payments->where('status', 'pending')->count();
    $failedCount = $payments->where('status', 'failed')->count();
@endphp

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <p class="text-muted mb-0">Audit, verify, and export all conference payments, baseline registration fees, and accommodation/material add-ons across the university portal.</p>
</div>

<!-- Financial Summary Widgets -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card shadow-sm border-0 bg-white">
            <div class="stat-card-icon" style="background-color: #d1fae5; color: #059669;"><i class="bi bi-cash-coin"></i></div>
            <h6 class="text-muted small uppercase fw-bold mb-1">Total Revenue (Paid)</h6>
            <h3 class="heading-font fw-extrabold mt-1 text-success">₦{{ number_format($successfulTotal, 2) }}</h3>
            <p class="text-muted small mb-0"><i class="bi bi-check-circle-fill text-success me-1"></i> {{ $successfulCount }} successful payments</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card shadow-sm border-0 bg-white">
            <div class="stat-card-icon" style="background-color: #fef3c7; color: #d97706;"><i class="bi bi-hourglass-split"></i></div>
            <h6 class="text-muted small uppercase fw-bold mb-1">Pending Invoice Value</h6>
            <h3 class="heading-font fw-extrabold mt-1 text-warning">₦{{ number_format($pendingTotal, 2) }}</h3>
            <p class="text-muted small mb-0"><i class="bi bi-exclamation-circle-fill text-warning me-1"></i> {{ $pendingCount }} transactions pending</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card shadow-sm border-0 bg-white">
            <div class="stat-card-icon" style="background-color: #fee2e2; color: #dc2626;"><i class="bi bi-exclamation-octagon"></i></div>
            <h6 class="text-muted small uppercase fw-bold mb-1">Failed Invoice Value</h6>
            <h3 class="heading-font fw-extrabold mt-1 text-danger">₦{{ number_format($failedTotal, 2) }}</h3>
            <p class="text-muted small mb-0"><i class="bi bi-x-circle-fill text-danger me-1"></i> {{ $failedCount }} failed attempts</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card shadow-sm border-0 bg-white">
            <div class="stat-card-icon" style="background-color: #f7eecd; color: #9d7126;"><i class="bi bi-calculator"></i></div>
            <h6 class="text-muted small uppercase fw-bold mb-1">Total Transactions</h6>
            <h3 class="heading-font fw-extrabold mt-1 text-dark">{{ $payments->count() }}</h3>
            <p class="text-muted small mb-0"><i class="bi bi-clock-history text-muted me-1"></i> Lifetime database log count</p>
        </div>
    </div>
</div>

<!-- Advanced Audit Filters -->
<div class="card border-0 shadow-sm mb-4 rounded-3 bg-white" style="border: 1px solid rgba(157, 113, 38, 0.08) !important;">
    <div class="card-header bg-light border-bottom-0 py-3 d-flex align-items-center justify-content-between">
        <h5 class="heading-font mb-0 fw-bold text-academic-green">
            <i class="bi bi-funnel-fill text-fulafia-gold me-1"></i> Dynamic Transaction Audit Filters
        </h5>
        <button id="btnResetFilters" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-counterclockwise"></i> Reset Filters
        </button>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            <!-- Conference Filter -->
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Conference</label>
                <select id="filterConference" class="form-select">
                    <option value="">-- All Conferences --</option>
                    @foreach($payments->pluck('registration.conference.title')->unique()->filter() as $confTitle)
                        <option value="{{ $confTitle }}">{{ Str::limit($confTitle, 40) }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Purpose Filter -->
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Payment Purpose</label>
                <select id="filterPurpose" class="form-select">
                    <option value="">-- All Billing Items --</option>
                    <option value="attendance">Attendance Registration</option>
                    <option value="accommodation">Hostel Accommodation</option>
                    <option value="materials">Conference Materials</option>
                    <option value="abstract">Abstract Submission</option>
                    <option value="full_paper">Full Paper Fee</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted">Payment Status</label>
                <select id="filterStatus" class="form-select">
                    <option value="">-- All Statuses --</option>
                    <option value="successful">Successful</option>
                    <option value="pending">Pending</option>
                    <option value="failed">Failed</option>
                </select>
            </div>

            <!-- Date Range Filters -->
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted">Start Date</label>
                <input type="date" id="filterStartDate" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted">End Date</label>
                <input type="date" id="filterEndDate" class="form-control">
            </div>
        </div>
    </div>
</div>

<!-- Transaction Table Logs -->
<div class="table-responsive shadow-sm bg-white rounded-3">
    <table id="paymentsTable" class="table table-hover align-middle mb-0" style="width:100%">
        <thead class="table-light text-muted heading-font">
            <tr>
                <th>Ref ID</th>
                <th>Attendee / Payer</th>
                <th>Conference</th>
                <th>Purpose</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date Initiated</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
                <tr>
                    <td class="font-monospace fw-bold text-dark small" title="{{ $payment->reference }}">
                        {{ Str::limit($payment->reference, 15) }}
                    </td>
                    <td>
                        <div class="fw-semibold text-dark">{{ $payment->user->title }} {{ $payment->user->first_name }} {{ $payment->user->last_name }}</div>
                        <span class="text-muted font-monospace small" style="font-size: 0.75rem;">{{ $payment->user->email }}</span>
                    </td>
                    <td>
                        @if($payment->registration && $payment->registration->conference)
                            <span class="fw-bold text-academic-green small d-block text-truncate" style="max-width: 180px;" title="{{ $payment->registration->conference->title }}">
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
                    <td data-status="{{ $payment->status }}">
                        @if($payment->status === 'successful')
                            <span class="badge bg-success px-2.5 py-1 rounded-pill small">
                                <i class="bi bi-check-circle-fill me-1"></i> Successful
                            </span>
                        @elseif($payment->status === 'failed')
                            <span class="badge bg-danger px-2.5 py-1 rounded-pill small">
                                <i class="bi bi-x-circle-fill me-1"></i> Failed
                            </span>
                        @else
                            <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill small">
                                <i class="bi bi-hourglass-split me-1"></i> Pending
                            </span>
                        @endif
                    </td>
                    <td class="small text-muted" data-order="{{ $payment->created_at->timestamp }}">
                        {{ $payment->created_at->format('Y-m-d H:i') }}
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
                                    <button type="submit" class="btn btn-warning btn-xs px-2 py-1 rounded small fw-bold" style="font-size: 0.75rem;">
                                        <i class="bi bi-arrow-repeat me-1"></i> Re-verify
                                    </button>
                                </form>
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
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let table = $('#paymentsTable').DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'FULafia_Global_Payments_Audit_Report',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
                {
                    extend: 'csvHtml5',
                    title: 'FULafia_Global_Payments_Audit_Report',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
                {
                    extend: 'print',
                    title: 'FULafia Academic Portal Global Billing Invoice Ledger',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                }
            ],
            language: {
                searchPlaceholder: "Search ID, Payer email, reference...",
                search: ""
            },
            order: [[6, 'desc']] // Default sorting by date initiated desc
        });

        // Custom search algorithm for real-time audit filters
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                let selectedConf = $('#filterConference').val();
                let selectedPurpose = $('#filterPurpose').val();
                let selectedStatus = $('#filterStatus').val();
                let startDate = $('#filterStartDate').val();
                let endDate = $('#filterEndDate').val();

                let conf = data[2]; // Column index 2: Conference
                let purposeVal = $(settings.aoData[dataIndex].anCells[3]).text().trim().toLowerCase(); // Purpose text
                let status = $(settings.aoData[dataIndex].anCells[5]).attr('data-status') || '';
                
                let dateStr = data[6]; // Column index 6: Date initiated
                let rowDate = new Date(dateStr);

                // 1. Conference Filter
                if (selectedConf && conf.indexOf(selectedConf) === -1) {
                    return false;
                }

                // 2. Purpose/Billing Category Filter
                if (selectedPurpose) {
                    // Map display purpose back to technical purpose
                    let normalizedPurpose = selectedPurpose.replace('_', ' ');
                    if (purposeVal.indexOf(normalizedPurpose.toLowerCase()) === -1) {
                        return false;
                    }
                }

                // 3. Gateway Status Filter
                if (selectedStatus && status.toLowerCase() !== selectedStatus.toLowerCase()) {
                    return false;
                }

                // 4. Date Range Filter
                if (startDate) {
                    let start = new Date(startDate);
                    start.setHours(0,0,0,0);
                    if (rowDate < start) return false;
                }
                if (endDate) {
                    let end = new Date(endDate);
                    end.setHours(23,59,59,999);
                    if (rowDate > end) return false;
                }

                return true;
            }
        );

        // Re-draw table when filters change
        $('#filterConference, #filterPurpose, #filterStatus, #filterStartDate, #filterEndDate').on('change', function() {
            table.draw();
        });

        // Reset all filters button action
        $('#btnResetFilters').on('click', function() {
            $('#filterConference').val('');
            $('#filterPurpose').val('');
            $('#filterStatus').val('');
            $('#filterStartDate').val('');
            $('#filterEndDate').val('');
            table.draw();
        });

        // Style Search Bar nicely for Bootstrap integration
        $('.dataTables_filter input').addClass('form-control d-inline-block w-auto ms-2 mb-3');
    });
</script>
@endsection
