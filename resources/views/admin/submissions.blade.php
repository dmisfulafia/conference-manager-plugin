@extends('layouts.admin')

@section('title', 'Manage Submissions')
@section('page_title', 'Conference Submissions & Academic Review Portal')

@section('content')
@php
    $totalAbstracts = $submissions->count();
    $pendingAbstracts = $submissions->where('abstract_status', 'pending')->count();
    $approvedAbstracts = $submissions->where('abstract_status', 'approved')->count();
    $deniedAbstracts = $submissions->where('abstract_status', 'denied')->count();
    
    $totalFullPapers = $submissions->whereNotNull('full_paper_file_path')->count();
    $pendingFullPapers = $submissions->where('full_paper_status', 'pending')->whereNotNull('full_paper_file_path')->count();
    $approvedFullPapers = $submissions->where('full_paper_status', 'approved')->whereNotNull('full_paper_file_path')->count();
@endphp

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <p class="text-muted mb-0">Evaluate research abstracts, review full paper uploads, and coordinate double-blind peer reviews or academic status indicators for registered attendees.</p>
</div>

<!-- Submission Summary Widgets -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card shadow-sm border-0 bg-white">
            <div class="stat-card-icon" style="background-color: #d1fae5; color: #059669;"><i class="bi bi-file-earmark-check-fill"></i></div>
            <h6 class="text-muted small uppercase fw-bold mb-1">Approved Abstracts</h6>
            <h3 class="heading-font fw-extrabold mt-1 text-success">{{ $approvedAbstracts }}</h3>
            <p class="text-muted small mb-0"><i class="bi bi-journal-check text-success me-1"></i> {{ $totalAbstracts }} abstract submissions total</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card shadow-sm border-0 bg-white">
            <div class="stat-card-icon" style="background-color: #fef3c7; color: #d97706;"><i class="bi bi-hourglass-split"></i></div>
            <h6 class="text-muted small uppercase fw-bold mb-1">Pending Review</h6>
            <h3 class="heading-font fw-extrabold mt-1 text-warning">{{ $pendingAbstracts }}</h3>
            <p class="text-muted small mb-0"><i class="bi bi-clock-history text-warning me-1"></i> Awaiting academic response</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card shadow-sm border-0 bg-white">
            <div class="stat-card-icon" style="background-color: #e0f2fe; color: #0284c7;"><i class="bi bi-file-pdf-fill"></i></div>
            <h6 class="text-muted small uppercase fw-bold mb-1">Full Papers Uploaded</h6>
            <h3 class="heading-font fw-extrabold mt-1 text-info">{{ $totalFullPapers }}</h3>
            <p class="text-muted small mb-0"><i class="bi bi-hourglass-split text-info me-1"></i> {{ $pendingFullPapers }} pending full paper reviews</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card shadow-sm border-0 bg-white">
            <div class="stat-card-icon" style="background-color: #fee2e2; color: #dc2626;"><i class="bi bi-file-earmark-x-fill"></i></div>
            <h6 class="text-muted small uppercase fw-bold mb-1">Rejected Abstracts</h6>
            <h3 class="heading-font fw-extrabold mt-1 text-danger">{{ $deniedAbstracts }}</h3>
            <p class="text-muted small mb-0"><i class="bi bi-exclamation-triangle-fill text-danger me-1"></i> Require revised uploads</p>
        </div>
    </div>
</div>

<!-- Advanced Audit Filters -->
<div class="card border-0 shadow-sm mb-4 rounded-3 bg-white" style="border: 1px solid rgba(157, 113, 38, 0.08) !important;">
    <div class="card-header bg-light border-bottom-0 py-3 d-flex align-items-center justify-content-between">
        <h5 class="heading-font mb-0 fw-bold text-academic-green">
            <i class="bi bi-funnel-fill text-fulafia-gold me-1"></i> Submissions Audit & Filter Board
        </h5>
        <button id="btnResetFilters" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-counterclockwise"></i> Reset Filters
        </button>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            <!-- Conference Filter -->
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Conference Selection</label>
                <select id="filterConference" class="form-select">
                    <option value="">-- All Conferences --</option>
                    @foreach($submissions->pluck('registration.conference.title')->unique()->filter() as $confTitle)
                        <option value="{{ $confTitle }}">{{ Str::limit($confTitle, 50) }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Abstract Status Filter -->
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Abstract Stage Status</label>
                <select id="filterAbstractStatus" class="form-select">
                    <option value="">-- All Statuses --</option>
                    <option value="pending">Pending Review</option>
                    <option value="approved">Approved</option>
                    <option value="denied">Denied</option>
                </select>
            </div>

            <!-- Full Paper Status Filter -->
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Full Paper Stage Status</label>
                <select id="filterFullPaperStatus" class="form-select">
                    <option value="">-- All Statuses --</option>
                    <option value="not_uploaded">Not Uploaded Yet</option>
                    <option value="pending">Pending Review</option>
                    <option value="approved">Approved</option>
                    <option value="denied">Denied</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Submissions Table Logs -->
<div class="table-responsive shadow-sm bg-white rounded-3">
    <table id="submissionsTable" class="table table-hover align-middle mb-0" style="width:100%">
        <thead class="table-light text-muted heading-font">
            <tr>
                <th>Author & Registrant</th>
                <th>Conference</th>
                <th>Research Title</th>
                <th class="text-center">Abstract Review</th>
                <th class="text-center">Full Paper Review</th>
                <th>Date Initiated</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($submissions as $sub)
                @php
                    $reg = $sub->registration;
                    $user = $reg->user;
                    $conf = $reg->conference;
                @endphp
                <tr>
                    <td>
                        <div class="fw-semibold text-dark">{{ $user->title }} {{ $user->first_name }} {{ $user->last_name }}</div>
                        <span class="text-muted font-monospace small" style="font-size: 0.75rem;">{{ $user->email }}</span>
                    </td>
                    <td>
                        <span class="fw-bold text-academic-green small d-block text-truncate" style="max-width: 180px;" title="{{ $conf->title }}">
                            {{ $conf->title }}
                        </span>
                        @if($sub->is_abstract_paid)
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill py-0.5 small" style="font-size: 0.65rem;">Abstract Fee Paid</span>
                        @else
                            <span class="badge bg-light text-muted border rounded-pill py-0.5 small" style="font-size: 0.65rem;">Abstract Free</span>
                        @endif
                    </td>
                    <td class="small fw-semibold text-dark text-wrap" style="max-width: 200px;">
                        {{ $sub->title }}
                    </td>
                    <td class="text-center" data-abstract-status="{{ $sub->abstract_status }}">
                        @if($sub->abstract_status === 'pending')
                            <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill small"><i class="bi bi-hourglass-split me-1"></i> Pending</span>
                        @elseif($sub->abstract_status === 'approved')
                            <span class="badge bg-success px-2.5 py-1 rounded-pill small"><i class="bi bi-check-circle-fill me-1"></i> Approved</span>
                        @else
                            <span class="badge bg-danger px-2.5 py-1 rounded-pill small"><i class="bi bi-x-circle-fill me-1"></i> Denied</span>
                        @endif

                        @if($sub->abstract_file_path)
                            <div class="mt-1">
                                <a href="{{ $sub->abstract_file_path }}" target="_blank" class="text-primary text-decoration-none small fw-bold">
                                    <i class="bi bi-file-earmark-arrow-down"></i> View File
                                </a>
                            </div>
                        @else
                            <div class="text-muted small italic">Awaiting document</div>
                        @endif
                    </td>
                    <td class="text-center" data-paper-status="{{ $sub->full_paper_file_path ? $sub->full_paper_status : 'not_uploaded' }}">
                        @if(!$sub->full_paper_file_path)
                            <span class="badge bg-light text-muted px-2.5 py-1 rounded-pill small">Not Uploaded</span>
                        @else
                            @if($sub->full_paper_status === 'pending')
                                <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill small"><i class="bi bi-hourglass-split me-1"></i> Pending</span>
                            @elseif($sub->full_paper_status === 'approved')
                                <span class="badge bg-success px-2.5 py-1 rounded-pill small"><i class="bi bi-check-circle-fill me-1"></i> Approved</span>
                            @else
                                <span class="badge bg-danger px-2.5 py-1 rounded-pill small"><i class="bi bi-x-circle-fill me-1"></i> Denied</span>
                            @endif

                            <div class="mt-1">
                                <a href="{{ $sub->full_paper_file_path }}" target="_blank" class="text-primary text-decoration-none small fw-bold">
                                    <i class="bi bi-file-earmark-arrow-down"></i> View File
                                </a>
                            </div>
                            @if($sub->is_full_paper_paid)
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill py-0.5 small" style="font-size: 0.65rem;">Paper Fee Paid</span>
                            @else
                                <span class="badge bg-light text-muted border rounded-pill py-0.5 small" style="font-size: 0.65rem;">Paper Free</span>
                            @endif
                        @endif
                    </td>
                    <td class="small text-muted" data-order="{{ $sub->created_at->timestamp }}">
                        {{ $sub->created_at->format('Y-m-d H:i') }}
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center flex-wrap" style="max-width: 140px;">
                            <!-- Review Abstract Action -->
                            @if($sub->abstract_file_path)
                                <button class="btn btn-gold btn-xs px-2 py-1 rounded small fw-bold" style="font-size: 0.7rem;" data-bs-toggle="modal" data-bs-target="#reviewAbstractModal{{ $sub->id }}">
                                    <i class="bi bi-journal-text me-1"></i> Review Abstract
                                </button>

                                <!-- Review Abstract Modal -->
                                <div class="modal fade" id="reviewAbstractModal{{ $sub->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content text-start">
                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title fw-bold text-academic-green heading-font"><i class="bi bi-file-earmark-check-fill me-2"></i> Review Abstract</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('admin.submissions.review', $sub->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="type" value="abstract">
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <strong>Paper Title:</strong>
                                                        <p class="text-dark small bg-light p-2 rounded mt-1 border">{{ $sub->title }}</p>
                                                    </div>
                                                    <div class="mb-3">
                                                        <strong>Summary/Text:</strong>
                                                        <p class="text-muted small mt-1">{{ $sub->abstract_text ?: 'No text summary supplied.' }}</p>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-muted">Decision</label>
                                                        <select name="status" class="form-select decision-select" required onchange="toggleReasonField(this, 'abstractReasonContainer{{ $sub->id }}')">
                                                            <option value="">-- Choose Status --</option>
                                                            <option value="approved" {{ $sub->abstract_status === 'approved' ? 'selected' : '' }}>Approve (Accept abstract)</option>
                                                            <option value="denied" {{ $sub->abstract_status === 'denied' ? 'selected' : '' }}>Deny (Reject abstract)</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3 {{ $sub->abstract_status !== 'denied' ? 'd-none' : '' }}" id="abstractReasonContainer{{ $sub->id }}">
                                                        <label class="form-label small fw-bold text-muted">Feedback/Reason for Denial</label>
                                                        <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Provide feedback detailing the reasons for rejection or updates required...">{{ $sub->abstract_rejection_reason }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-academic btn-sm fw-bold">Submit Decision</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Review Full Paper Action -->
                            @if($sub->full_paper_file_path)
                                <button class="btn btn-academic btn-xs px-2 py-1 rounded small fw-bold mt-1" style="font-size: 0.7rem;" data-bs-toggle="modal" data-bs-target="#reviewPaperModal{{ $sub->id }}">
                                    <i class="bi bi-file-pdf me-1"></i> Review Full Paper
                                </button>

                                <!-- Review Paper Modal -->
                                <div class="modal fade" id="reviewPaperModal{{ $sub->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content text-start">
                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title fw-bold text-academic-green heading-font"><i class="bi bi-file-earmark-check-fill me-2"></i> Review Full Paper</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('admin.submissions.review', $sub->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="type" value="full_paper">
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <strong>Paper Title:</strong>
                                                        <p class="text-dark small bg-light p-2 rounded mt-1 border">{{ $sub->title }}</p>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-muted">Decision</label>
                                                        <select name="status" class="form-select decision-select" required onchange="toggleReasonField(this, 'paperReasonContainer{{ $sub->id }}')">
                                                            <option value="">-- Choose Status --</option>
                                                            <option value="approved" {{ $sub->full_paper_status === 'approved' ? 'selected' : '' }}>Approve (Accept full paper)</option>
                                                            <option value="denied" {{ $sub->full_paper_status === 'denied' ? 'selected' : '' }}>Deny (Reject full paper)</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3 {{ $sub->full_paper_status !== 'denied' ? 'd-none' : '' }}" id="paperReasonContainer{{ $sub->id }}">
                                                        <label class="form-label small fw-bold text-muted">Feedback/Reason for Denial</label>
                                                        <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Provide feedback detailing the reasons for rejection or updates required...">{{ $sub->full_paper_rejection_reason }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-academic btn-sm fw-bold">Submit Decision</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
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
    function toggleReasonField(selectElement, containerId) {
        let container = document.getElementById(containerId);
        if (selectElement.value === 'denied') {
            container.classList.remove('d-none');
            container.querySelector('textarea').setAttribute('required', 'required');
        } else {
            container.classList.add('d-none');
            container.querySelector('textarea').removeAttribute('required');
        }
    }

    $(document).ready(function() {
        let table = $('#submissionsTable').DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'FULafia_Conferences_Submissions_Report',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    }
                },
                {
                    extend: 'csvHtml5',
                    title: 'FULafia_Conferences_Submissions_Report',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    }
                },
                {
                    extend: 'print',
                    title: 'FULafia Academic Portal Conference Submissions Review Ledger',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    }
                }
            ],
            language: {
                searchPlaceholder: "Search paper, author, email...",
                search: ""
            },
            order: [[5, 'desc']] // Sort by date desc
        });

        // Custom search algorithm for real-time audit filters
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                let selectedConf = $('#filterConference').val();
                let selectedAbstractStatus = $('#filterAbstractStatus').val();
                let selectedFullPaperStatus = $('#filterFullPaperStatus').val();

                let conf = data[1]; // Column index 1: Conference
                
                // Fetch direct statuses from the custom attributes we placed on cells
                let abstractStatus = $(settings.aoData[dataIndex].anCells[3]).attr('data-abstract-status') || '';
                let paperStatus = $(settings.aoData[dataIndex].anCells[4]).attr('data-paper-status') || '';

                // 1. Conference Filter
                if (selectedConf && conf.indexOf(selectedConf) === -1) {
                    return false;
                }

                // 2. Abstract Status Filter
                if (selectedAbstractStatus && abstractStatus.toLowerCase() !== selectedAbstractStatus.toLowerCase()) {
                    return false;
                }

                // 3. Full Paper Status Filter
                if (selectedFullPaperStatus && paperStatus.toLowerCase() !== selectedFullPaperStatus.toLowerCase()) {
                    return false;
                }

                return true;
            }
        );

        // Re-draw table when filters change
        $('#filterConference, #filterAbstractStatus, #filterFullPaperStatus').on('change', function() {
            table.draw();
        });

        // Reset all filters button action
        $('#btnResetFilters').on('click', function() {
            $('#filterConference').val('');
            $('#filterAbstractStatus').val('');
            $('#filterFullPaperStatus').val('');
            table.draw();
        });

        // Style Search Bar nicely for Bootstrap integration
        $('.dataTables_filter input').addClass('form-control d-inline-block w-auto ms-2 mb-3');
    });
</script>
@endsection
