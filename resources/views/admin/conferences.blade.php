@extends('layouts.admin')

@section('title', 'Manage Conferences')
@section('page_title', 'Conferences Setup Panel')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0">Create new conferences, manage schedules, toggle past/ongoing statuses, and set baseline billing rates.</p>
    <button class="btn btn-academic px-4 py-2" data-bs-toggle="modal" data-bs-target="#createConferenceModal">
        <i class="bi bi-calendar-plus-fill me-2"></i> Create Conference
    </button>
</div>

<!-- Conference List Table -->
<div class="table-responsive shadow-sm">
    <table id="conferencesTable" class="table table-hover align-middle mb-0" style="width:100%">
        <thead class="table-light">
            <tr>
                <th>Title</th>
                <th>Venue</th>
                <th>Schedules</th>
                <th>Status</th>
                <th>Accommodation Fee</th>
                <th>Material Fee</th>
                <th>Abstract Fee</th>
                <th>Full Paper Fee</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($conferences as $conf)
                <tr>
                    <td class="fw-bold text-academic-green">{{ $conf->title }}</td>
                    <td><i class="bi bi-geo-alt-fill text-muted me-1"></i> {{ $conf->venue }}</td>
                    <td>
                        <span class="small d-block text-dark fw-medium">
                            <i class="bi bi-calendar-event me-1"></i> {{ $conf->start_date->format('M d, Y') }}
                        </span>
                        <span class="small d-block text-muted">
                            to {{ $conf->end_date->format('M d, Y') }}
                        </span>
                    </td>
                    <td>
                        @if($conf->status === 'ongoing')
                            <span class="badge bg-success px-2 py-1"><i class="bi bi-play-circle-fill me-1"></i> Ongoing</span>
                        @else
                            <span class="badge bg-secondary px-2 py-1"><i class="bi bi-stop-circle-fill me-1"></i> Past</span>
                        @endif
                    </td>
                    <td class="font-monospace">₦{{ number_format($conf->accommodation_fee, 2) }}</td>
                    <td class="font-monospace">₦{{ number_format($conf->conference_material_fee, 2) }}</td>
                    <td class="font-monospace">₦{{ number_format($conf->abstract_fee, 2) }}</td>
                    <td class="font-monospace">₦{{ number_format($conf->full_paper_fee, 2) }}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <!-- Edit Button -->
                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editConferenceModal{{ $conf->id }}">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                            <!-- Delete Form -->
                            <form action="{{ route('admin.conferences.delete', $conf) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this conference profile? This will remove all associated registries.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-trash-fill"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

                <!-- Edit Conference Modal -->
                <div class="modal fade" id="editConferenceModal{{ $conf->id }}" tabindex="-1" aria-labelledby="editConferenceModalLabel{{ $conf->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                            <div class="modal-header border-bottom-0 pb-0" style="background-color: var(--fulafia-gold); color: white; border-top-left-radius: 16px; border-top-right-radius: 16px; padding: 20px;">
                                <h5 class="modal-title heading-font fw-bold" id="editConferenceModalLabel{{ $conf->id }}">Edit Conference Setup</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('admin.conferences.update', $conf) }}" method="POST" class="p-4">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Conference Title</label>
                                    <input type="text" name="title" class="form-control" value="{{ $conf->title }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Description</label>
                                    <textarea name="description" class="form-control" rows="4" required>{{ $conf->description }}</textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Start Date</label>
                                        <input type="date" name="start_date" class="form-control" value="{{ $conf->start_date->format('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">End Date</label>
                                        <input type="date" name="end_date" class="form-control" value="{{ $conf->end_date->format('Y-m-d') }}" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Venue</label>
                                        <input type="text" name="venue" class="form-control" value="{{ $conf->venue }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Status</label>
                                        <select name="status" class="form-select" required>
                                            <option value="ongoing" {{ $conf->status === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                            <option value="past" {{ $conf->status === 'past' ? 'selected' : '' }}>Past</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Accommodation Fee (₦)</label>
                                        <input type="number" step="0.01" name="accommodation_fee" class="form-control" value="{{ $conf->accommodation_fee }}" required min="0">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Conference Material Fee (₦)</label>
                                        <input type="number" step="0.01" name="conference_material_fee" class="form-control" value="{{ $conf->conference_material_fee }}" required min="0">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Abstract Submission Fee (₦)</label>
                                        <input type="number" step="0.01" name="abstract_fee" class="form-control" value="{{ $conf->abstract_fee }}" required min="0">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Full Paper Submission Fee (₦)</label>
                                        <input type="number" step="0.01" name="full_paper_fee" class="form-control" value="{{ $conf->full_paper_fee }}" required min="0">
                                    </div>
                                </div>

                                <hr class="my-4">
                                <h5 class="heading-font fw-bold text-fulafia-gold mb-3">Attendee Category Fees (₦)</h5>
                                <p class="text-muted small">Enter the standard attendance fee for each category below. Set to 0 to disable that category from appearing on the registration checkout page.</p>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-semibold">Researchers Fee</label>
                                        <input type="number" step="0.01" name="researchers_fee" class="form-control" value="{{ $conf->attendeeTypes->where('name', 'Researchers')->first()?->fee ?? 0 }}" required min="0">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-semibold">Postgraduate Students Fee</label>
                                        <input type="number" step="0.01" name="postgraduate_fee" class="form-control" value="{{ $conf->attendeeTypes->where('name', 'Postgraduate Students')->first()?->fee ?? 0 }}" required min="0">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-semibold">Undergraduate Students Fee</label>
                                        <input type="number" step="0.01" name="undergraduate_fee" class="form-control" value="{{ $conf->attendeeTypes->where('name', 'Undergraduate Students')->first()?->fee ?? 0 }}" required min="0">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-semibold">Corporate Bodies Fee</label>
                                        <input type="number" step="0.01" name="corporate_fee" class="form-control" value="{{ $conf->attendeeTypes->where('name', 'Corporate Bodies')->first()?->fee ?? 0 }}" required min="0">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-semibold">International Attendee Fee</label>
                                        <input type="number" step="0.01" name="international_fee" class="form-control" value="{{ $conf->attendeeTypes->where('name', 'International attendee')->first()?->fee ?? 0 }}" required min="0">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-semibold">Virtual Attendee Fee</label>
                                        <input type="number" step="0.01" name="virtual_fee" class="form-control" value="{{ $conf->attendeeTypes->where('name', 'Virtual Attendee')->first()?->fee ?? 0 }}" required min="0">
                                    </div>
                                </div>

                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-gold btn-lg">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Create Conference Modal -->
<div class="modal fade" id="createConferenceModal" tabindex="-1" aria-labelledby="createConferenceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pb-0" style="background-color: var(--academic-green); color: white; border-top-left-radius: 16px; border-top-right-radius: 16px; padding: 20px;">
                <h5 class="modal-title heading-font fw-bold" id="createConferenceModalLabel">Add New Conference</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.conferences.store') }}" method="POST" class="p-4">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Conference Title</label>
                    <input type="text" name="title" class="form-control" required placeholder="e.g. 5th International Conference on Computing Science">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" class="form-control" rows="4" required placeholder="Enter description detailing the calls for papers and timeline..."></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Start Date</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">End Date</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Venue</label>
                        <input type="text" name="venue" class="form-control" required placeholder="e.g. ETF Lecture Theatre, FULafia">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="ongoing">Ongoing</option>
                            <option value="past">Past</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Accommodation Fee (₦)</label>
                        <input type="number" step="0.01" name="accommodation_fee" class="form-control" required value="0" min="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Conference Material Fee (₦)</label>
                        <input type="number" step="0.01" name="conference_material_fee" class="form-control" required value="0" min="0">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Abstract Submission Fee (₦)</label>
                        <input type="number" step="0.01" name="abstract_fee" class="form-control" required value="0" min="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Full Paper Submission Fee (₦)</label>
                        <input type="number" step="0.01" name="full_paper_fee" class="form-control" required value="0" min="0">
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="heading-font fw-bold text-academic-green mb-3">Attendee Category Fees (₦)</h5>
                <p class="text-muted small">Enter the standard attendance fee for each category below. Set to 0 to disable that category from appearing on the registration checkout page.</p>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Researchers Fee</label>
                        <input type="number" step="0.01" name="researchers_fee" class="form-control" required value="0" min="0">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Postgraduate Students Fee</label>
                        <input type="number" step="0.01" name="postgraduate_fee" class="form-control" required value="0" min="0">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Undergraduate Students Fee</label>
                        <input type="number" step="0.01" name="undergraduate_fee" class="form-control" required value="0" min="0">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Corporate Bodies Fee</label>
                        <input type="number" step="0.01" name="corporate_fee" class="form-control" required value="0" min="0">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">International Attendee Fee</label>
                        <input type="number" step="0.01" name="international_fee" class="form-control" required value="0" min="0">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Virtual Attendee Fee</label>
                        <input type="number" step="0.01" name="virtual_fee" class="form-control" required value="0" min="0">
                    </div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-academic btn-lg">Publish Conference</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#conferencesTable').DataTable({
            responsive: true,
            language: {
                searchPlaceholder: "Search conferences...",
                search: ""
            }
        });
        $('.dataTables_filter input').addClass('form-control d-inline-block w-auto ms-2 mb-3');
    });
</script>
@endsection
