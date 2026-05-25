@extends('layouts.member')

@section('title', 'Member Dashboard')
@section('page_title', 'Dashboard')

@section('styles')
    /* Dashboard Cards */
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

    .welcome-banner {
        background: linear-gradient(135deg, var(--academic-green) 0%, var(--academic-green-hover) 100%);
        color: #ffffff;
        border-radius: 16px;
        padding: 35px;
        border-bottom: 4px solid var(--fulafia-gold);
        margin-bottom: 35px;
        position: relative;
        overflow: hidden;
    }

    .welcome-banner::after {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(157, 113, 38, 0.2) 0%, rgba(255, 255, 255, 0) 70%);
        top: -50px;
        right: -50px;
    }

    /* Profile Details Table */
    .profile-label {
        font-weight: 600;
        color: #6b7280;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .profile-value {
        font-size: 1.05rem;
        color: #111827;
        font-weight: 500;
    }
@endsection

@section('content')
    <!-- Welcome Banner -->
    <div class="welcome-banner shadow-sm">
        <h1 class="heading-font fw-extrabold mb-2">Welcome, {{ Auth::user()->title }} {{ Auth::user()->name }}!</h1>
        <p class="fs-5 opacity-90 mb-3 col-lg-8">You are logged into the Federal University of Lafia Conference Portal. Below are your registration details and quick actions to manage your submissions and payment options.</p>
        <a href="#conferences-section" class="btn btn-gold btn-lg px-4 py-2 mt-2 shadow-sm"><i class="bi bi-plus-circle me-2"></i> Register for a Conference</a>
    </div>

    <!-- Stat Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="stat-card shadow-sm border-0">
                <div class="stat-card-icon"><i class="bi bi-journal-text"></i></div>
                <h6 class="text-muted small uppercase fw-bold">My Conferences</h6>
                <h3 class="heading-font fw-extrabold mt-1">{{ $registrationsCount }}</h3>
                <p class="text-success small mb-0"><i class="bi bi-clock-history me-1"></i> Register for upcoming events</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card shadow-sm border-0">
                <div class="stat-card-icon"><i class="bi bi-file-earmark-arrow-up"></i></div>
                <h6 class="text-muted small uppercase fw-bold">My Submissions</h6>
                <h3 class="heading-font fw-extrabold mt-1">{{ $submissionsCount }}</h3>
                <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i> Abstract submission lifecycle</p>
            </div>
        </div>
        <div class="col-md-4">
            <a href="{{ route('payments.index') }}" class="text-decoration-none text-dark d-block h-100">
                <div class="stat-card shadow-sm border-0">
                    <div class="stat-card-icon"><i class="bi bi-credit-card-2-front"></i></div>
                    <h6 class="text-muted small uppercase fw-bold">Total Payments</h6>
                    <h3 class="heading-font fw-extrabold mt-1">₦{{ number_format($totalPayments, 2) }}</h3>
                    @if($totalPayments > 0)
                        <p class="text-success small mb-0"><i class="bi bi-patch-check-fill me-1"></i> View payment invoices & receipts</p>
                    @else
                        <p class="text-danger small mb-0"><i class="bi bi-exclamation-triangle me-1"></i> No transactions found</p>
                    @endif
                </div>
            </a>
        </div>
    </div>

    <!-- My Registered Conferences Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm p-4 rounded-3 bg-white" style="border: 1px solid rgba(157, 113, 38, 0.08) !important; background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);">
                <h4 class="heading-font fw-bold text-academic-green mb-4 border-bottom pb-2 d-flex align-items-center">
                    <i class="bi bi-bookmark-star-fill text-fulafia-gold me-2"></i> My Registered Conferences
                </h4>
                
                @if($myRegistrations->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-journal-bookmark fs-1 text-muted opacity-40"></i>
                        <p class="mt-2 fs-6 mb-2">You have not registered for any conferences yet.</p>
                        <a href="#conferences-section" class="btn btn-academic btn-sm px-4 rounded-pill shadow-sm"><i class="bi bi-calendar-event me-1"></i> Browse Ongoing Conferences</a>
                    </div>
                @else
                    <div class="row g-4">
                        @foreach($myRegistrations as $reg)
                            @php
                                $conf = $reg->conference;
                                $type = $reg->attendeeType;
                            @endphp
                            <div class="col-xl-6">
                                <div class="card border rounded-3 p-4 h-100 bg-white shadow-sm" style="transition: transform 0.2s; border-color: rgba(157, 113, 38, 0.15) !important;">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <span class="badge bg-gold-light text-fulafia-gold fw-bold mb-2 px-3 py-1 rounded-pill" style="font-size: 0.8rem;">
                                                <i class="bi bi-person-badge-fill me-1"></i> {{ $type->name }}
                                            </span>
                                            <h5 class="fw-bold text-academic-green mb-1">{{ $conf->title }}</h5>
                                            <p class="text-muted small mb-0"><i class="bi bi-calendar-event me-1"></i> {{ $conf->start_date->format('M d, Y') }} - {{ $conf->end_date->format('M d, Y') }}</p>
                                        </div>
                                        
                                        <div>
                                            @if($reg->is_attendance_paid)
                                                <span class="badge bg-success px-3 py-2 rounded-pill fw-bold">
                                                    <i class="bi bi-patch-check-fill me-1"></i> Paid & Active
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold animate-pulse">
                                                    <i class="bi bi-exclamation-circle-fill me-1"></i> Pending Payment
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="bg-light p-3 rounded-3 mb-4 border small">
                                        <div class="row g-2">
                                            <div class="col-sm-6">
                                                <strong>Registration Status:</strong> 
                                                @if($reg->is_attendance_paid)
                                                    <span class="text-success fw-bold"><i class="bi bi-check-circle-fill"></i> Paid</span>
                                                @else
                                                    <span class="text-warning fw-bold"><i class="bi bi-clock-history"></i> Pending Checkout</span>
                                                @endif
                                            </div>
                                            
                                            @if($reg->wants_accommodation)
                                                <div class="col-sm-6">
                                                    <strong>Hostel Accommodation:</strong> 
                                                    @if($reg->is_accommodation_paid)
                                                        <span class="text-success fw-bold"><i class="bi bi-check-circle-fill"></i> Paid</span>
                                                    @else
                                                        <span class="text-warning fw-bold"><i class="bi bi-clock-history"></i> Pending</span>
                                                    @endif
                                                </div>
                                            @endif
                                            
                                            @if($reg->wants_materials)
                                                <div class="col-sm-6">
                                                    <strong>Materials & bag Pack:</strong> 
                                                    @if($reg->is_materials_paid)
                                                        <span class="text-success fw-bold"><i class="bi bi-check-circle-fill"></i> Paid</span>
                                                    @else
                                                        <span class="text-warning fw-bold"><i class="bi bi-clock-history"></i> Pending</span>
                                                    @endif
                                                </div>
                                            @endif
                                            
                                            <div class="col-12 mt-2 pt-2 border-top text-muted d-flex justify-content-between align-items-center">
                                                <span>Registered on: {{ $reg->created_at->format('M d, Y') }}</span>
                                                <span class="fw-bold text-academic-green">Total: ₦{{ number_format($type->fee + ($reg->wants_accommodation ? $conf->accommodation_fee : 0) + ($reg->wants_materials ? $conf->conference_material_fee : 0), 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-auto pt-2">
                                        @if($reg->is_attendance_paid)
                                            <!-- Submissions Portal Access -->
                                            <div class="d-flex gap-2">
                                                <a href="#submissions-section" class="btn btn-academic w-100 py-2 rounded-3 fw-bold shadow-sm">
                                                    <i class="bi bi-file-earmark-arrow-up-fill me-2"></i> Submit Abstract / Full Paper
                                                </a>
                                            </div>
                                        @else
                                            <!-- Resume Payment Checkout -->
                                            <form action="{{ route('payment.checkout') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="conference_id" value="{{ $conf->id }}">
                                                <input type="hidden" name="attendee_type_id" value="{{ $type->id }}">
                                                <input type="hidden" name="wants_accommodation" value="{{ $reg->wants_accommodation ? '1' : '0' }}">
                                                <input type="hidden" name="wants_materials" value="{{ $reg->wants_materials ? '1' : '0' }}">
                                                <button type="submit" class="btn btn-gold w-100 py-2 rounded-3 fw-bold shadow-sm">
                                                    <i class="bi bi-wallet2 me-2"></i> Complete Payment
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Submissions & Abstract Portal Section -->
    <div id="submissions-section" class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm p-4 rounded-3 bg-white" style="border: 1px solid rgba(157, 113, 38, 0.08) !important;">
                <h4 class="heading-font fw-bold text-academic-green mb-3 border-bottom pb-2 d-flex align-items-center">
                    <i class="bi bi-file-earmark-arrow-up-fill text-fulafia-gold me-2"></i> Submissions & Abstract Portal
                </h4>
                <p class="text-muted small mb-4">Manage your research paper uploads, monitor review feedback, and track your abstract and full-paper approval stages below.</p>

                @php
                    $paidRegs = $myRegistrations->where('is_attendance_paid', true);
                @endphp

                @if($paidRegs->isEmpty())
                    <div class="text-center py-4 text-muted bg-light rounded-3 border" style="border-style: dashed !important;">
                        <i class="bi bi-lock-fill fs-2 text-muted opacity-40"></i>
                        <p class="mt-2 fs-6 mb-1 fw-bold text-dark">Submissions Portal is Locked</p>
                        <p class="small text-muted mb-0">Please complete the payment for at least one registered conference to unlock abstract and paper submissions.</p>
                    </div>
                @else
                    <div class="row g-4">
                        @foreach($paidRegs as $reg)
                            @php
                                $conf = $reg->conference;
                                $sub = $reg->submission;
                            @endphp
                            <div class="col-12 col-xl-6">
                                <div class="border rounded-3 p-4 h-100 bg-white" style="border-color: rgba(157, 113, 38, 0.15) !important;">
                                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                        <h5 class="fw-bold text-academic-green mb-0"><i class="bi bi-journal-text text-fulafia-gold me-2"></i> {{ $conf->title }}</h5>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">Active Attendee</span>
                                    </div>

                                    @if(!$sub)
                                        <!-- No submission yet: Show Abstract Submission Form -->
                                        <div class="bg-warning-subtle text-warning-emphasis p-3 rounded-3 mb-4 small border border-warning-subtle">
                                            <i class="bi bi-info-circle-fill me-2"></i> <strong>Awaiting Abstract Submission:</strong> To present a paper at this conference, please upload your research abstract using the form below.
                                        </div>

                                        <form action="{{ route('submissions.abstract') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="registration_id" value="{{ $reg->id }}">
                                            
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-dark">Research Paper Title</label>
                                                <input type="text" name="title" class="form-control" placeholder="Enter the exact title of your paper" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-dark">Abstract Summary (Optional)</label>
                                                <textarea name="abstract_text" class="form-control" rows="3" placeholder="Provide a brief text summary of your abstract..."></textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-dark">Abstract Document File (Word or PDF)</label>
                                                <input type="file" name="abstract_file" class="form-control" accept=".pdf,.doc,.docx" required>
                                                <div class="form-text small text-muted">Supported formats: PDF, DOC, DOCX. Max file size: 20MB.</div>
                                            </div>

                                            <button type="submit" class="btn btn-gold btn-sm w-100 py-2 fw-bold"><i class="bi bi-upload me-1"></i> Upload & Submit Abstract</button>
                                        </form>

                                    @else
                                        <!-- Submission exists: Show lifecycle progress -->
                                        <div class="card border-0 bg-light p-3 rounded-3 mb-4">
                                            <h6 class="fw-bold text-dark mb-2">Paper Title: "{{ $sub->title }}"</h6>
                                            <div class="row g-3 mt-1">
                                                <!-- Abstract Stage Box -->
                                                <div class="col-12 col-md-6">
                                                    <div class="border rounded p-3 h-100 bg-white shadow-xs">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <strong class="small text-muted">STAGE 1: ABSTRACT</strong>
                                                            @if($sub->abstract_status === 'pending')
                                                                <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i> Under Review</span>
                                                            @elseif($sub->abstract_status === 'approved')
                                                                <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i> Approved</span>
                                                            @else
                                                                <span class="badge bg-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i> Denied</span>
                                                            @endif
                                                        </div>

                                                        <p class="small mb-2">
                                                            <strong>Document:</strong> 
                                                            <a href="{{ $sub->abstract_file_path }}" target="_blank" class="text-primary text-decoration-none fw-bold"><i class="bi bi-file-earmark-pdf-fill me-1"></i> View Abstract File</a>
                                                        </p>

                                                        @if($sub->abstract_status === 'denied')
                                                            <div class="alert alert-danger p-2 small mb-2" role="alert">
                                                                <strong>Rejection Feedback:</strong> {{ $sub->abstract_rejection_reason ?? 'Please revise your document guidelines and upload again.' }}
                                                            </div>

                                                            <!-- Re-submit abstract -->
                                                            <button class="btn btn-outline-danger btn-xs w-100 py-1" data-bs-toggle="collapse" data-bs-target="#reSubmitAbstract{{ $sub->id }}">
                                                                <i class="bi bi-arrow-repeat me-1"></i> Re-submit Revised Abstract
                                                            </button>

                                                            <div class="collapse mt-2" id="reSubmitAbstract{{ $sub->id }}">
                                                                <form action="{{ route('submissions.abstract') }}" method="POST" enctype="multipart/form-data" class="p-2 border rounded bg-light">
                                                                    @csrf
                                                                    <input type="hidden" name="registration_id" value="{{ $reg->id }}">
                                                                    <div class="mb-2">
                                                                        <label class="form-label small fw-bold">Updated Paper Title</label>
                                                                        <input type="text" name="title" class="form-control form-control-sm" value="{{ $sub->title }}" required>
                                                                    </div>
                                                                    <div class="mb-2">
                                                                        <label class="form-label small fw-bold">Revised Document</label>
                                                                        <input type="file" name="abstract_file" class="form-control form-control-sm" accept=".pdf,.doc,.docx" required>
                                                                    </div>
                                                                    <button type="submit" class="btn btn-danger btn-sm w-100 py-1 fw-bold">Upload Revisions</button>
                                                                </form>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Full Paper Stage Box -->
                                                <div class="col-12 col-md-6">
                                                    <div class="border rounded p-3 h-100 bg-white shadow-xs">
                                                        <strong class="small text-muted d-block mb-2">STAGE 2: FULL PAPER</strong>

                                                        @if($sub->abstract_status !== 'approved')
                                                            <div class="text-center py-3 text-muted small">
                                                                <i class="bi bi-lock-fill d-block fs-5 mb-1 text-muted"></i>
                                                                Locked until abstract approval
                                                            </div>
                                                        @else
                                                            @if(!$sub->full_paper_file_path)
                                                                <!-- Abstract approved but no full paper yet: Show upload form -->
                                                                <span class="badge bg-gold-light text-fulafia-gold mb-2"><i class="bi bi-clock-fill me-1"></i> Awaiting Upload</span>
                                                                
                                                                <button class="btn btn-gold btn-xs w-100 py-1.5 fw-bold" data-bs-toggle="collapse" data-bs-target="#uploadFullPaper{{ $sub->id }}">
                                                                    <i class="bi bi-cloud-arrow-up-fill me-1"></i> Submit Full Paper
                                                                </button>

                                                                <div class="collapse mt-2" id="uploadFullPaper{{ $sub->id }}">
                                                                    <form action="{{ route('submissions.full-paper') }}" method="POST" enctype="multipart/form-data" class="p-2 border rounded bg-light">
                                                                        @csrf
                                                                        <input type="hidden" name="submission_id" value="{{ $sub->id }}">
                                                                        <div class="mb-2">
                                                                            <label class="form-label small fw-bold">Full Paper File (Word or PDF)</label>
                                                                            <input type="file" name="full_paper_file" class="form-control form-control-sm" accept=".pdf,.doc,.docx" required>
                                                                        </div>
                                                                        <button type="submit" class="btn btn-academic btn-sm w-100 py-1 fw-bold">Upload Full Paper</button>
                                                                    </form>
                                                                </div>
                                                            @else
                                                                <!-- Full paper uploaded -->
                                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                                    @if($sub->full_paper_status === 'pending')
                                                                        <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i> Under Review</span>
                                                                    @elseif($sub->full_paper_status === 'approved')
                                                                        <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i> Approved</span>
                                                                    @else
                                                                        <span class="badge bg-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i> Denied</span>
                                                                    @endif
                                                                </div>

                                                                <p class="small mb-2">
                                                                    <strong>Document:</strong> 
                                                                    <a href="{{ $sub->full_paper_file_path }}" target="_blank" class="text-primary text-decoration-none fw-bold"><i class="bi bi-file-earmark-pdf-fill me-1"></i> View Full Paper</a>
                                                                </p>

                                                                @if($sub->full_paper_status === 'denied')
                                                                    <div class="alert alert-danger p-2 small mb-2" role="alert">
                                                                        <strong>Rejection Feedback:</strong> {{ $sub->full_paper_rejection_reason ?? 'Please revise your document and upload again.' }}
                                                                    </div>

                                                                    <!-- Re-submit full paper -->
                                                                    <button class="btn btn-outline-danger btn-xs w-100 py-1" data-bs-toggle="collapse" data-bs-target="#reSubmitFullPaper{{ $sub->id }}">
                                                                        <i class="bi bi-arrow-repeat me-1"></i> Re-submit Revised Paper
                                                                    </button>

                                                                    <div class="collapse mt-2" id="reSubmitFullPaper{{ $sub->id }}">
                                                                        <form action="{{ route('submissions.full-paper') }}" method="POST" enctype="multipart/form-data" class="p-2 border rounded bg-light">
                                                                            @csrf
                                                                            <input type="hidden" name="submission_id" value="{{ $sub->id }}">
                                                                            <div class="mb-2">
                                                                                <label class="form-label small fw-bold">Revised Paper File</label>
                                                                                <input type="file" name="full_paper_file" class="form-control form-control-sm" accept=".pdf,.doc,.docx" required>
                                                                            </div>
                                                                            <button type="submit" class="btn btn-danger btn-sm w-100 py-1 fw-bold">Upload Revisions</button>
                                                                        </form>
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Personal Profile Info -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 rounded-3 bg-white" style="border: 1px solid rgba(157, 113, 38, 0.08) !important;">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                    <h4 class="heading-font fw-bold text-academic-green mb-0"><i class="bi bi-person-badge-fill me-2"></i> Account Profile</h4>
                    <div>
                        <span class="badge bg-warning text-dark px-3 py-1 font-monospace me-2">{{ strtoupper(Auth::user()->role) }}</span>
                        <a href="{{ route('profile.show') }}" class="btn btn-outline-primary btn-sm rounded-pill"><i class="bi bi-pencil-square"></i> Edit Profile</a>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-3 text-center mb-3">
                        @if(Auth::user()->passport_photo)
                            <img src="{{ str_starts_with(Auth::user()->passport_photo, 'http') ? Auth::user()->passport_photo : asset('storage/' . Auth::user()->passport_photo) }}" alt="Passport" class="img-thumbnail rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            <div class="d-inline-flex align-items-center justify-content-center bg-light border rounded-circle text-muted" style="width: 120px; height: 120px;">
                                <i class="bi bi-person-fill" style="font-size: 3.5rem;"></i>
                            </div>
                        @endif
                        <div class="mt-2 small text-muted">Passport Photograph</div>
                    </div>
                    
                    <div class="col-md-9">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="profile-label">Title & Name</div>
                                <div class="profile-value">{{ Auth::user()->title }} {{ Auth::user()->name }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="profile-label">Email Address</div>
                                <div class="profile-value">{{ Auth::user()->email }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="profile-label">Phone Number</div>
                                <div class="profile-value">{{ Auth::user()->phone }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="profile-label">Gender</div>
                                <div class="profile-value">{{ Auth::user()->gender }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="profile-label">Country</div>
                                <div class="profile-value">{{ Auth::user()->country }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="profile-label">Occupation</div>
                                <div class="profile-value">{{ Auth::user()->occupation }}</div>
                            </div>
                            @if(Auth::user()->institution)
                                <div class="col-md-6">
                                    <div class="profile-label">Institution</div>
                                    <div class="profile-value">{{ Auth::user()->institution }}</div>
                                </div>
                            @endif
                            
                            @if(stripos(Auth::user()->occupation, 'student') !== false)
                                <div class="col-md-6">
                                    <div class="profile-label">Student ID Card Status</div>
                                    @if(Auth::user()->student_id_card)
                                        @if(Auth::user()->student_id_verified)
                                            <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i> Verified</span>
                                        @else
                                            <span class="badge bg-warning text-dark"><i class="bi bi-clock-fill me-1"></i> Pending Verification</span>
                                        @endif
                                    @else
                                        <span class="badge bg-danger"><i class="bi bi-exclamation-octagon-fill me-1"></i> Not Uploaded</span>
                                        <div class="small text-danger mt-1">Please upload your Student ID Card in Profile to verify your student rate eligibility.</div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Actions Panel -->
        <div class="col-lg-4 d-flex flex-column gap-4">
            <!-- Manual Re-verification Card -->
            <div class="card border-0 shadow-sm p-4 rounded-3 bg-white" style="border: 1px solid rgba(157, 113, 38, 0.08) !important;">
                <h5 class="heading-font fw-bold text-academic-green mb-3 border-bottom pb-2"><i class="bi bi-shield-check me-2 text-fulafia-gold"></i> Re-verify Payment</h5>
                <p class="text-muted small">Did you make a payment but it is still showing as pending? Enter your transaction reference below to query Credo API and confirm instantly.</p>
                
                <form action="{{ route('payment.reverify') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Transaction Reference</label>
                        <input type="text" name="reference" class="form-control" placeholder="e.g. FUL_1716584283_3821" required style="font-family: monospace;">
                    </div>
                    <button type="submit" class="btn btn-gold w-100 py-2"><i class="bi bi-arrow-repeat me-1"></i> Re-verify Now</button>
                </form>
            </div>

            <!-- Support & Complaints Card -->
            <div class="card border-0 shadow-sm p-4 rounded-3 bg-white" style="border: 1px solid rgba(157, 113, 38, 0.08) !important;">
                <h5 class="heading-font fw-bold text-academic-green mb-3 border-bottom pb-2">Support & Complaints</h5>
                <p class="text-muted small">Having issues with payments, abstract approval, or document uploads? Lodge a formal complaint to our portal support desk.</p>
                <a href="#" class="btn btn-outline-success w-100 py-2 mt-auto"><i class="bi bi-chat-left-dots-fill me-2"></i> Submit Complaint</a>
            </div>
        </div>
    </div>

    <!-- Ongoing Conferences Section -->
    <div id="conferences-section" class="row g-4 mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm p-4 rounded-3 bg-white" style="border: 1px solid rgba(157, 113, 38, 0.08) !important;">
                <h4 class="heading-font fw-bold text-academic-green mb-4 border-bottom pb-2"><i class="bi bi-calendar-event-fill me-2 text-fulafia-gold"></i> Ongoing Conferences Catalog</h4>
                
                @if($ongoingConferences->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-x" style="font-size: 3rem;"></i>
                        <p class="mt-3 fs-6">There are no ongoing conferences configured on the portal at this time. Please check back later.</p>
                    </div>
                @else
                    <div class="row g-4">
                        @foreach($ongoingConferences as $conf)
                            <div class="col-xl-6">
                                <div class="border rounded-3 p-4 h-100 bg-light d-flex flex-column justify-content-between">
                                    <div>
                                        <h5 class="fw-bold text-academic-green mb-2">{{ $conf->title }}</h5>
                                        <p class="text-muted small mb-3">{{ Str::limit($conf->description, 200) }}</p>
                                        
                                        <div class="mb-2 small text-dark"><i class="bi bi-geo-alt-fill text-fulafia-gold me-2"></i><strong>Venue:</strong> {{ $conf->venue }}</div>
                                        <div class="mb-3 small text-dark"><i class="bi bi-calendar3 text-fulafia-gold me-2"></i><strong>Timeline:</strong> {{ $conf->start_date->format('M d, Y') }} - {{ $conf->end_date->format('M d, Y') }}</div>
                                    </div>

                                    <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                                        @php
                                            $activeTypes = $conf->attendeeTypes->where('fee', '>', 0);
                                        @endphp
                                        <div class="text-muted small">
                                            @if($activeTypes->isNotEmpty())
                                                Available fees from <strong class="text-academic-green">₦{{ number_format($activeTypes->min('fee'), 2) }}</strong>
                                            @else
                                                Registration fees pending
                                            @endif
                                        </div>
                                        @if($activeTypes->isNotEmpty())
                                            <button class="btn btn-gold px-3 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#registerModal{{ $conf->id }}">
                                                <i class="bi bi-bookmark-plus-fill me-1"></i> Register & Pay
                                            </button>
                                        @else
                                            <button class="btn btn-secondary px-3 rounded-pill" disabled>
                                                Upcoming
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if($activeTypes->isNotEmpty())
                            <!-- Registration Form Modal -->
                            <div class="modal fade" id="registerModal{{ $conf->id }}" tabindex="-1" aria-labelledby="registerModalLabel{{ $conf->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                                        <div class="modal-header border-bottom-0 pb-0" style="background-color: var(--academic-green); color: white; border-top-left-radius: 16px; border-top-right-radius: 16px; padding: 20px;">
                                            <h5 class="modal-title heading-font fw-bold" id="registerModalLabel{{ $conf->id }}">Conference Registration Form</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('payment.checkout') }}" method="POST" class="p-4 registration-form" data-conf-id="{{ $conf->id }}">
                                            @csrf
                                            <input type="hidden" name="conference_id" value="{{ $conf->id }}">
                                            <div class="mb-4">
                                                <h6 class="fw-bold text-dark mb-2">1. Select Your Attendee Category</h6>
                                                <p class="text-muted small">Please select the appropriate category you belong to. Note that some categories may require official ID verification upon arrival.</p>
                                                
                                                @foreach($activeTypes as $type)
                                                    <div class="form-check p-3 mb-2 border rounded-3 attendee-category-row" style="cursor: pointer; transition: background-color 0.2s;">
                                                        <input class="form-check-input attendee-radio" type="radio" name="attendee_type_id" id="type_{{ $type->id }}" value="{{ $type->id }}" data-fee="{{ $type->fee }}" required>
                                                        <label class="form-check-label fw-bold d-block text-dark" for="type_{{ $type->id }}" style="cursor: pointer;">
                                                            {{ $type->name }} <span class="float-end text-fulafia-gold">₦{{ number_format($type->fee, 2) }}</span>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div class="mb-4">
                                                <h6 class="fw-bold text-dark mb-3">2. Additional Accommodation & Material Add-ons</h6>
                                                
                                                <div class="form-check p-3 mb-2 border rounded-3 wants-accommodation-row" style="cursor: pointer; transition: background-color 0.2s;">
                                                    <input class="form-check-input accommodation-checkbox" type="checkbox" name="wants_accommodation" id="accommodation_{{ $conf->id }}" value="1" data-fee="{{ $conf->accommodation_fee }}">
                                                    <label class="form-check-label fw-bold d-block text-dark" for="accommodation_{{ $conf->id }}" style="cursor: pointer;">
                                                        Request University Hostel/Guest Lodge Accommodation <span class="float-end text-muted font-monospace">+₦{{ number_format($conf->accommodation_fee, 2) }}</span>
                                                    </label>
                                                </div>

                                                <div class="form-check p-3 mb-2 border rounded-3 wants-materials-row" style="cursor: pointer; transition: background-color 0.2s;">
                                                    <input class="form-check-input materials-checkbox" type="checkbox" name="wants_materials" id="materials_{{ $conf->id }}" value="1" data-fee="{{ $conf->conference_material_fee }}">
                                                    <label class="form-check-label fw-bold d-block text-dark" for="materials_{{ $conf->id }}" style="cursor: pointer;">
                                                        Purchase Conference Materials Bag & Programs Pack <span class="float-end text-muted font-monospace">+₦{{ number_format($conf->conference_material_fee, 2) }}</span>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="p-3 bg-light rounded-3 mb-4 border" style="border-style: dashed !important;">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <div class="fw-bold text-dark">Calculated Payment Total:</div>
                                                        <div class="text-muted small">Inclusive of baseline attendee registration & selected add-ons.</div>
                                                    </div>
                                                    <span class="fs-3 fw-extrabold text-academic-green total-display">₦0.00</span>
                                                </div>
                                            </div>

                                            <div class="d-grid">
                                                <button type="submit" class="btn btn-academic btn-lg py-3 shadow-sm" style="border-radius: 12px;">
                                                    <i class="bi bi-wallet2 me-2"></i> Proceed to Checkout
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- My Transactions History -->
    <div class="col-12 mt-4">
        <div class="card border-0 shadow-sm p-4 rounded-3 bg-white" style="border: 1px solid rgba(157, 113, 38, 0.08) !important;">
            <h4 class="heading-font fw-bold text-academic-green mb-4 border-bottom pb-2"><i class="bi bi-credit-card-2-front-fill me-2 text-fulafia-gold"></i> My Payment Transactions History</h4>
            
            @if($recentPayments->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-credit-card fs-1 text-muted opacity-50"></i>
                    <p class="mt-3 fs-6 mb-0">No payment records found on your account. When you register for conferences, your transactions will appear here.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light text-muted">
                            <tr>
                                <th>Transaction Ref</th>
                                <th>Conference</th>
                                <th>Purpose</th>
                                <th>Amount</th>
                                <th>Date Initiated</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentPayments as $payment)
                                <tr>
                                    <td class="font-monospace fw-bold text-dark">{{ $payment->reference }}</td>
                                    <td>
                                        @if($payment->registration && $payment->registration->conference)
                                            <span class="fw-bold text-academic-green">{{ $payment->registration->conference->title }}</span>
                                        @else
                                            <span class="text-muted small">Generic</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark text-capitalize" style="border: 1px solid rgba(0,0,0,0.08);">{{ str_replace('_', ' ', $payment->purpose) }}</span>
                                    </td>
                                    <td class="fw-bold text-academic-green">₦{{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ $payment->created_at->format('M d, Y h:i A') }}</td>
                                    <td>
                                        @if($payment->status === 'successful')
                                            <span class="badge bg-success px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i> Successful</span>
                                        @elseif($payment->status === 'failed')
                                            <span class="badge bg-danger px-2 py-1"><i class="bi bi-x-circle-fill me-1"></i> Failed</span>
                                        @else
                                            <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-hourglass-split me-1"></i> Pending</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($payment->status === 'successful')
                                            <a href="{{ route('payment.receipt', $payment->id) }}" target="_blank" class="btn btn-outline-success btn-sm rounded-pill px-3 py-1 shadow-sm">
                                                <i class="bi bi-printer me-1"></i> Receipt
                                            </a>
                                        @elseif($payment->status === 'pending')
                                            <form action="{{ route('payment.reverify') }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="reference" value="{{ $payment->reference }}">
                                                <button type="submit" class="btn btn-outline-warning btn-sm rounded-pill px-3 py-1 shadow-sm"><i class="bi bi-arrow-repeat me-1"></i> Re-verify</button>
                                            </form>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Dynamic Price Calculation Scripts -->
    <script>
        $(document).ready(function() {
            // Check for payment callback indicators in URL
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('payment_success')) {
                window.history.replaceState({}, document.title, window.location.pathname);
                $('<div class="alert alert-success border-0 shadow-sm mb-4 alert-dismissible fade show" role="alert">' +
                    '<i class="bi bi-patch-check-fill me-2"></i> <strong>Congratulations!</strong> Your payment has been successfully processed and verified! Your Submissions Portal is now unlocked.' +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                  '</div>').insertBefore('.main-content > div').first();
            } else if (urlParams.has('payment_error')) {
                window.history.replaceState({}, document.title, window.location.pathname);
                $('<div class="alert alert-danger border-0 shadow-sm mb-4 alert-dismissible fade show" role="alert">' +
                    '<i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Payment Unsuccessful:</strong> The payment verification was unsuccessful or cancelled.' +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                  '</div>').insertBefore('.main-content > div').first();
            }
            // Function to recalculate registration price inside a specific modal/form
            function calculateTotal($form) {
                let total = 0.00;

                // 1. Get selected attendee type fee
                let $selectedRadio = $form.find('.attendee-radio:checked');
                if ($selectedRadio.length > 0) {
                    total += parseFloat($selectedRadio.data('fee')) || 0;
                }

                // 2. Add accommodation fee if checked
                let $accommodationCheck = $form.find('.accommodation-checkbox');
                if ($accommodationCheck.is(':checked')) {
                    total += parseFloat($accommodationCheck.data('fee')) || 0;
                }

                // 3. Add material fee if checked
                let $materialsCheck = $form.find('.materials-checkbox');
                if ($materialsCheck.is(':checked')) {
                    total += parseFloat($materialsCheck.data('fee')) || 0;
                }

                // Format total as Currency
                let formattedTotal = '₦' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                $form.find('.total-display').text(formattedTotal);
            }

            // Bind triggers on modal radio and checkboxes changes
            $('.registration-form').on('change', '.attendee-radio, .accommodation-checkbox, .materials-checkbox', function() {
                let $form = $(this).closest('.registration-form');
                calculateTotal($form);
            });

            // Highlight selected attendee row
            $('.registration-form').on('click', '.attendee-category-row, .wants-accommodation-row, .wants-materials-row', function(e) {
                // Trigger natural input state
                let $input = $(this).find('input');
                if (e.target !== $input[0]) {
                    if ($input.attr('type') === 'radio') {
                        $input.prop('checked', true).trigger('change');
                    } else if ($input.attr('type') === 'checkbox') {
                        $input.prop('checked', !$input.is(':checked')).trigger('change');
                    }
                }
                
                // Toggle active style
                let $form = $(this).closest('.registration-form');
                $form.find('.attendee-category-row').removeClass('bg-warning-subtle border-warning');
                $form.find('.attendee-radio:checked').closest('.attendee-category-row').addClass('bg-warning-subtle border-warning');
                
                // Toggle checkboxes styles
                $form.find('.wants-accommodation-row').toggleClass('bg-success-subtle border-success', $form.find('.accommodation-checkbox').is(':checked'));
                $form.find('.wants-materials-row').toggleClass('bg-success-subtle border-success', $form.find('.materials-checkbox').is(':checked'));
            });

            // Credo Inline Payment Interceptor
            $(document).on('submit', 'form[action*="payment/checkout"]', function(e) {
                e.preventDefault();
                let $form = $(this);
                let $btn = $form.find('button[type="submit"]');
                let originalText = $btn.html();
                
                // Show loading spinner
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading Checkout...');

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
                            // Close any open Bootstrap modal
                            let $modal = $form.closest('.modal');
                            if ($modal.length > 0) {
                                bootstrap.Modal.getInstance($modal[0]).hide();
                            }

                            // Open secure centered popup window pointing directly to the payment link
                            let width = 500;
                            let height = 700;
                            let left = (screen.width / 2) - (width / 2);
                            let top = (screen.height / 2) - (height / 2);
                            
                            let popup = window.open(response.payment_link, 'CredoCheckout', 'width=' + width + ',height=' + height + ',top=' + top + ',left=' + left + ',resizable=yes,scrollbars=yes,status=yes');
                            
                            $btn.html('<i class="bi bi-hourglass-split animate-pulse"></i> Awaiting Payment...');

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
                        } else if (xhr.responseText) {
                            try {
                                let parsed = JSON.parse(xhr.responseText);
                                if (parsed.error) errMsg = parsed.error;
                            } catch(err) {}
                        }
                        alert(errMsg);
                        $btn.prop('disabled', false).html(originalText);
                    }
                });
            });
        });
    </script>
@endsection
