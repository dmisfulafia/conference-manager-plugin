@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page_title', 'Dashboard Overview')

@section('content')
<div class="row g-4 mb-4">
    <!-- Stat 1: Total Attendees -->
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card shadow-sm">
            <div class="stat-card-icon"><i class="bi bi-people-fill text-fulafia-gold"></i></div>
            <h6 class="text-muted small uppercase fw-bold">Total Registered Attendees</h6>
            <h2 class="heading-font fw-extrabold mt-1">{{ $stats['total_users'] }}</h2>
            <p class="text-success small mb-0"><i class="bi bi-person-plus-fill me-1"></i> Registered on portal</p>
        </div>
    </div>

    <!-- Stat 2: Verified Attendees -->
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card shadow-sm">
            <div class="stat-card-icon" style="background-color: #d1fae5; color: #059669;"><i class="bi bi-shield-check"></i></div>
            <h6 class="text-muted small uppercase fw-bold">Verified Accounts</h6>
            <h2 class="heading-font fw-extrabold mt-1">{{ $stats['total_verified'] }}</h2>
            <p class="text-success small mb-0"><i class="bi bi-check-circle-fill me-1"></i> Checked and active</p>
        </div>
    </div>

    <!-- Stat 3: Unverified Attendees -->
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card shadow-sm">
            <div class="stat-card-icon" style="background-color: #fee2e2; color: #dc2626;"><i class="bi bi-shield-slash"></i></div>
            <h6 class="text-muted small uppercase fw-bold">Unverified Accounts</h6>
            <h2 class="heading-font fw-extrabold mt-1">{{ $stats['total_unverified'] }}</h2>
            <p class="text-danger small mb-0"><i class="bi bi-trash3-fill me-1"></i> Deletable by admin</p>
        </div>
    </div>

    <!-- Stat 4: Total Revenue -->
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card shadow-sm">
            <div class="stat-card-icon" style="background-color: #fef3c7; color: #d97706;"><i class="bi bi-cash-stack"></i></div>
            <h6 class="text-muted small uppercase fw-bold">Revenue </h6>
            <h2 class="heading-font fw-extrabold mt-1">₦{{ number_format($stats['total_payments'], 2) }}</h2>
            <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i> Successfully processed</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Quick Actions Panel -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm p-4 bg-white" style="border-radius: 16px; border: 1px solid rgba(157, 113, 38, 0.08) !important;">
            <h4 class="heading-font fw-bold text-academic-green mb-4"><i class="bi bi-lightning-fill text-warning me-2"></i> Quick Administrative Tools</h4>
            <div class="row g-3">
                <div class="col-sm-6">
                    <a href="{{ route('admin.conferences') }}" class="btn btn-academic w-100 py-3 shadow-sm" style="border-radius: 12px;">
                        <i class="bi bi-plus-circle-fill d-block fs-3 mb-2"></i> Create Conference
                    </a>
                </div>
                <div class="col-sm-6">
                    <a href="{{ route('admin.users') }}" class="btn btn-gold w-100 py-3 shadow-sm" style="border-radius: 12px;">
                        <i class="bi bi-people-fill d-block fs-3 mb-2"></i> Manage Accounts
                    </a>
                </div>
                @if(Auth::user()->isSuperAdmin())
                <div class="col-sm-12">
                    <button class="btn btn-dark w-100 py-3 shadow-sm" style="border-radius: 12px;" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                        <i class="bi bi-person-plus-fill me-2 fs-5"></i> Add New Administrator Account
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Conference Status List -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm p-4 bg-white" style="border-radius: 16px; border: 1px solid rgba(157, 113, 38, 0.08) !important; height: 100%;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="heading-font fw-bold text-academic-green mb-0"><i class="bi bi-calendar-event-fill me-2 text-fulafia-gold"></i> Conference Setup Summary</h4>
                <span class="badge bg-dark px-3 py-1 font-monospace">{{ $stats['total_conferences'] }} Total</span>
            </div>
            
            @php
                $activeConfs = \App\Models\Conference::where('status', 'ongoing')->orderBy('start_date', 'asc')->get();
            @endphp

            @if($activeConfs->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-calendar-x" style="font-size: 3rem;"></i>
                    <p class="mt-3 fs-6">No active conferences ongoing. Please add a conference to begin accepting registrations.</p>
                </div>
            @else
                <div class="list-group list-group-flush">
                    @foreach($activeConfs as $conf)
                        <div class="list-group-item px-0 py-3 border-0 border-bottom">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="fw-bold mb-1">{{ $conf->title }}</h6>
                                    <div class="text-muted small"><i class="bi bi-geo-alt-fill me-1"></i> {{ $conf->venue }}</div>
                                    <div class="text-muted small"><i class="bi bi-calendar3 me-1"></i> {{ $conf->start_date->format('M d, Y') }} - {{ $conf->end_date->format('M d, Y') }}</div>
                                </div>
                                <span class="badge bg-success">ONGOING</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@if(Auth::user()->isSuperAdmin())
<!-- Add Admin Modal -->
<div class="modal fade" id="addAdminModal" tabindex="-1" aria-labelledby="addAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pb-0" style="background-color: var(--academic-green); color: white; border-top-left-radius: 16px; border-top-right-radius: 16px; padding: 20px;">
                <h5 class="modal-title heading-font fw-bold" id="addAdminModalLabel">Create Administrator Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.add-admin') }}" method="POST" class="p-4">
                @csrf
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-semibold">Title</label>
                        <select name="title" class="form-select" required>
                            <option value="Prof.">Prof.</option>
                            <option value="Dr.">Dr.</option>
                            <option value="Mr.">Mr.</option>
                            <option value="Mrs.">Mrs.</option>
                            <option value="Ms.">Ms.</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">First Name</label>
                        <input type="text" name="first_name" class="form-control" required placeholder="First name">
                    </div>
                    <div class="col-md-5 mb-3">
                        <label class="form-label fw-semibold">Last Name</label>
                        <input type="text" name="last_name" class="form-control" required placeholder="Last name">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" class="form-control" required placeholder="e.g. admin@fulafia.edu.ng">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Phone Number</label>
                        <input type="text" name="phone" class="form-control" required placeholder="e.g. 080XXXXXXXX">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Gender</label>
                        <select name="gender" class="form-select" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Role Level</label>
                        <select name="role" class="form-select" required>
                            <option value="admin">Regular Admin</option>
                            <option value="super_admin">Super Admin</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Country</label>
                        <input type="text" name="country" class="form-control" required value="Nigeria">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Occupation</label>
                        <input type="text" name="occupation" class="form-control" required value="University Staff">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Other Names</label>
                        <input type="text" name="other_names" class="form-control" placeholder="Middle Name (Optional)">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control" required placeholder="Minimum 8 characters">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required placeholder="Re-enter password">
                    </div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-academic btn-lg">Create Admin Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
