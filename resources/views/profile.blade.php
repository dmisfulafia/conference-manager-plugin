@extends('layouts.member')

@section('title', 'My Profile')
@section('page_title', 'My Profile')

@section('styles')
    .profile-card {
        background-color: #ffffff;
        border-radius: 12px;
        border: 1px solid rgba(157, 113, 38, 0.08);
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
        margin-bottom: 24px;
    }

    .profile-card-header {
        background-color: #ffffff;
        border-bottom: 1px solid rgba(157, 113, 38, 0.08);
        padding: 20px 24px;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .profile-card-body {
        padding: 24px;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--fulafia-gold);
        box-shadow: 0 0 0 0.25rem rgba(157, 113, 38, 0.15);
    }

    .image-preview-box {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid var(--fulafia-gold-light);
        margin: 0 auto 15px auto;
        background-color: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
        position: relative;
    }

    .image-preview-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .student-id-preview {
        max-width: 100%;
        max-height: 200px;
        object-fit: contain;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        padding: 4px;
        background: white;
    }
@endsection

@section('content')
    @php
        $isStudent = stripos(Auth::user()->occupation, 'student') !== false;
    @endphp

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4 alert-dismissible fade show" role="alert">
            <h5 class="alert-heading heading-font fw-bold"><i class="bi bi-exclamation-octagon-fill me-2"></i>Please correct the following errors:</h5>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Student Verification Status Alert (Interactive & Responsive) -->
    @if($isStudent)
        @if(!Auth::user()->student_id_card)
            <div class="alert alert-warning border-0 shadow-sm mb-4 d-flex align-items-center" role="alert" style="border-left: 5px solid var(--fulafia-gold) !important;">
                <div class="fs-1 text-warning me-4">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <h5 class="alert-heading heading-font fw-bold mb-1">Student Verification Mandatory</h5>
                    <p class="mb-0 text-dark opacity-90">Because your occupation is set as <strong>{{ Auth::user()->occupation }}</strong>, you are eligible for student registration rates. However, you <strong>must upload a valid Student ID Card</strong> below. Until it is uploaded and approved by an administrator, student rate benefits remain locked.</p>
                </div>
            </div>
        @elseif(!Auth::user()->student_id_verified)
            <div class="alert alert-info border-0 shadow-sm mb-4 d-flex align-items-center" role="alert" style="border-left: 5px solid #0dcaf0 !important;">
                <div class="fs-1 text-info me-4">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <h5 class="alert-heading heading-font fw-bold mb-1">ID Card Verification Pending</h5>
                    <p class="mb-0 text-dark opacity-90">Your Student ID Card has been uploaded successfully! An administrator is currently reviewing your document. Once approved, your student pricing eligibility will be active.</p>
                </div>
            </div>
        @else
            <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center" role="alert" style="border-left: 5px solid #198754 !important;">
                <div class="fs-1 text-success me-4">
                    <i class="bi bi-patch-check-fill"></i>
                </div>
                <div>
                    <h5 class="alert-heading heading-font fw-bold mb-1">Student ID Verified!</h5>
                    <p class="mb-0 text-dark opacity-90">Congratulations! Your Student ID Card has been verified by the portal administrators. You are now fully eligible to checkout using the <strong>Undergraduate / Postgraduate Student discounted rates</strong>.</p>
                </div>
            </div>
        @endif
    @endif

    <div class="row">
        <!-- Left Column: Photo upload & Quick statistics -->
        <div class="col-lg-4 mb-4">
            <!-- Passport Photo Card -->
            <div class="card profile-card">
                <div class="profile-card-header text-center">
                    <h5 class="heading-font fw-bold text-academic-green mb-0">Passport Photograph</h5>
                </div>
                <div class="profile-card-body text-center">
                    <div class="image-preview-box">
                        @if(Auth::user()->passport_photo)
                            <img src="{{ str_starts_with(Auth::user()->passport_photo, 'http') ? Auth::user()->passport_photo : asset('storage/' . Auth::user()->passport_photo) }}" alt="Passport Photo" id="passport-preview">
                        @else
                            <i class="bi bi-person-fill" style="font-size: 4rem;" id="passport-placeholder"></i>
                            <img src="" alt="Passport Photo" id="passport-preview" class="d-none">
                        @endif
                    </div>
                    <p class="text-muted small mb-4">Upload a clear passport photograph of yourself (Max 2MB. JPG, PNG formats only).</p>
                    
                    <form action="{{ route('profile.upload-passport') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <input class="form-control" type="file" name="passport_photo" id="passport_input" accept="image/*" required>
                        </div>
                        <button type="submit" class="btn btn-gold w-100"><i class="bi bi-cloud-arrow-up-fill me-2"></i>Upload Photo</button>
                    </form>
                </div>
            </div>

            <!-- Student ID Card Upload (Visible always but emphasized for students) -->
            <div class="card profile-card">
                <div class="profile-card-header text-center">
                    <h5 class="heading-font fw-bold text-academic-green mb-0">Student ID Card</h5>
                </div>
                <div class="profile-card-body text-center">
                    @if(Auth::user()->student_id_card)
                        <div class="mb-3">
                            @if(stripos(Auth::user()->student_id_card, '.pdf') !== false)
                                <div class="p-4 border rounded-3 bg-light d-flex flex-column align-items-center mb-3">
                                    <i class="bi bi-file-earmark-pdf-fill text-danger fs-1"></i>
                                    <span class="small fw-bold text-dark mt-2">Student_ID.pdf</span>
                                    <a href="{{ str_starts_with(Auth::user()->student_id_card, 'http') ? Auth::user()->student_id_card : asset('storage/' . Auth::user()->student_id_card) }}" target="_blank" class="btn btn-outline-secondary btn-sm mt-3"><i class="bi bi-eye"></i> View PDF</a>
                                </div>
                            @else
                                <img src="{{ str_starts_with(Auth::user()->student_id_card, 'http') ? Auth::user()->student_id_card : asset('storage/' . Auth::user()->student_id_card) }}" alt="Student ID Card" class="student-id-preview mb-3 shadow-sm">
                            @endif
                            
                            <div class="mb-2">
                                <span class="profile-label">Verification Status:</span>
                                @if(Auth::user()->student_id_verified)
                                    <span class="badge bg-success ms-1"><i class="bi bi-check-circle-fill"></i> Verified</span>
                                @else
                                    <span class="badge bg-warning text-dark ms-1"><i class="bi bi-clock-fill"></i> Pending Review</span>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="p-4 border rounded-3 bg-light text-muted mb-3 d-flex flex-column align-items-center">
                            <i class="bi bi-card-image fs-1 mb-2"></i>
                            <span class="small">No Student ID Card Uploaded</span>
                        </div>
                    @endif

                    <p class="text-muted small mb-4">Provide a valid scan or photo of your university ID card for admin verification (Max 5MB. JPG, PNG, PDF formats).</p>
                    
                    <form action="{{ route('profile.upload-student-id') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <input class="form-control" type="file" name="student_id_card" id="student_id_input" accept="image/*,.pdf" required>
                        </div>
                        <button type="submit" class="btn btn-academic w-100" {{ Auth::user()->student_id_verified ? 'disabled' : '' }}>
                            <i class="bi bi-cloud-arrow-up-fill me-2"></i>{{ Auth::user()->student_id_card ? 'Update ID Card' : 'Upload ID Card' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: Personal & Professional details fields -->
        <div class="col-lg-8 mb-4">
            <div class="card profile-card">
                <div class="profile-card-header">
                    <h5 class="heading-font fw-bold text-academic-green mb-0"><i class="bi bi-person-lines-fill me-2"></i>Edit Profile Details</h5>
                </div>
                <div class="profile-card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Section 1: Personal Details -->
                        <h6 class="heading-font fw-bold text-fulafia-gold mb-3 border-bottom pb-2">1. Personal Information</h6>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-2">
                                <label for="title" class="form-label small fw-bold text-muted">Title <span class="text-danger">*</span></label>
                                <select name="title" id="title" class="form-select" required>
                                    <option value="Prof." {{ (old('title', Auth::user()->title) == 'Prof.') ? 'selected' : '' }}>Prof.</option>
                                    <option value="Dr." {{ (old('title', Auth::user()->title) == 'Dr.') ? 'selected' : '' }}>Dr.</option>
                                    <option value="Mr." {{ (old('title', Auth::user()->title) == 'Mr.') ? 'selected' : '' }}>Mr.</option>
                                    <option value="Mrs." {{ (old('title', Auth::user()->title) == 'Mrs.') ? 'selected' : '' }}>Mrs.</option>
                                    <option value="Ms." {{ (old('title', Auth::user()->title) == 'Ms.') ? 'selected' : '' }}>Ms.</option>
                                </select>
                            </div>

                            <div class="col-md-5">
                                <label for="first_name" class="form-label small fw-bold text-muted">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name', Auth::user()->first_name) }}" required>
                            </div>

                            <div class="col-md-5">
                                <label for="last_name" class="form-label small fw-bold text-muted">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" id="last_name" class="form-control" value="{{ old('last_name', Auth::user()->last_name) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label for="other_names" class="form-label small fw-bold text-muted">Other Names <span class="text-muted">(Optional)</span></label>
                                <input type="text" name="other_names" id="other_names" class="form-control" value="{{ old('other_names', Auth::user()->other_names) }}">
                            </div>

                            <div class="col-md-6">
                                <label for="gender" class="form-label small fw-bold text-muted">Gender <span class="text-danger">*</span></label>
                                <select name="gender" id="gender" class="form-select" required>
                                    <option value="Male" {{ (old('gender', Auth::user()->gender) == 'Male') ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ (old('gender', Auth::user()->gender) == 'Female') ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ (old('gender', Auth::user()->gender) == 'Other') ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label small fw-bold text-muted">Email Address <span class="text-danger">*</span></label>
                                <input type="email" id="email" class="form-control bg-light" value="{{ Auth::user()->email }}" readonly>
                                <span class="text-muted small" style="font-size: 0.75rem;">Account email cannot be modified. Contact support for assistance.</span>
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label small fw-bold text-muted">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" id="phone" class="form-control" value="{{ old('phone', Auth::user()->phone) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label for="country" class="form-label small fw-bold text-muted">Country <span class="text-danger">*</span></label>
                                <select name="country" id="country" class="form-select" required>
                                    <option value="Nigeria" {{ (old('country', Auth::user()->country) == 'Nigeria') ? 'selected' : '' }}>Nigeria</option>
                                    <option value="Ghana" {{ (old('country', Auth::user()->country) == 'Ghana') ? 'selected' : '' }}>Ghana</option>
                                    <option value="United Kingdom" {{ (old('country', Auth::user()->country) == 'United Kingdom') ? 'selected' : '' }}>United Kingdom</option>
                                    <option value="United States" {{ (old('country', Auth::user()->country) == 'United States') ? 'selected' : '' }}>United States</option>
                                    <option value="Canada" {{ (old('country', Auth::user()->country) == 'Canada') ? 'selected' : '' }}>Canada</option>
                                    <option value="South Africa" {{ (old('country', Auth::user()->country) == 'South Africa') ? 'selected' : '' }}>South Africa</option>
                                    <option value="Kenya" {{ (old('country', Auth::user()->country) == 'Kenya') ? 'selected' : '' }}>Kenya</option>
                                    <option value="Other" {{ (old('country', Auth::user()->country) == 'Other') ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="address" class="form-label small fw-bold text-muted">Residential/Office Address</label>
                                <input type="text" name="address" id="address" class="form-control" value="{{ old('address', Auth::user()->address) }}" placeholder="e.g. FULafia Permanent Campus">
                            </div>
                        </div>

                        <!-- Section 2: Professional Details -->
                        <h6 class="heading-font fw-bold text-fulafia-gold mb-3 border-bottom pb-2">2. Academic & Professional Details</h6>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="occupation" class="form-label small fw-bold text-muted">Occupation/Designation <span class="text-danger">*</span></label>
                                <input type="text" name="occupation" id="occupation" class="form-control" value="{{ old('occupation', Auth::user()->occupation) }}" required placeholder="e.g. Professor, Student, Manager">
                                <span class="text-muted small d-block mt-1" style="font-size: 0.75rem;">Important: Changing this to "Student" unlocks student-rate options but requires ID Card upload.</span>
                            </div>

                            <div class="col-md-6">
                                <label for="institution" class="form-label small fw-bold text-muted">Institution/Organization <span class="text-muted">(Optional)</span></label>
                                <input type="text" name="institution" id="institution" class="form-control" value="{{ old('institution', Auth::user()->institution) }}" placeholder="e.g. Federal University of Lafia">
                            </div>
                        </div>

                        <!-- Section 3: Next of Kin Details -->
                        <h6 class="heading-font fw-bold text-fulafia-gold mb-3 border-bottom pb-2">3. Next of Kin Information</h6>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="next_of_kin" class="form-label small fw-bold text-muted">Next of Kin Name</label>
                                <input type="text" name="next_of_kin" id="next_of_kin" class="form-control" value="{{ old('next_of_kin', Auth::user()->next_of_kin) }}" placeholder="Full name of next of kin">
                            </div>

                            <div class="col-md-6">
                                <label for="next_of_kin_phone" class="form-label small fw-bold text-muted">Next of Kin Phone Number</label>
                                <input type="tel" name="next_of_kin_phone" id="next_of_kin_phone" class="form-control" value="{{ old('next_of_kin_phone', Auth::user()->next_of_kin_phone) }}" placeholder="e.g. 080XXXXXXXX">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end border-top pt-3">
                            <button type="submit" class="btn btn-gold btn-lg px-5 shadow-sm"><i class="bi bi-save me-2"></i>Save Details</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Preview script before file upload -->
    <script>
        $(document).ready(function() {
            // Live preview passport photo
            $("#passport_input").change(function() {
                const file = this.files[0];
                if (file) {
                    let reader = new FileReader();
                    reader.onload = function(event) {
                        $("#passport-placeholder").addClass('d-none');
                        $("#passport-preview").attr("src", event.target.result).removeClass('d-none');
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
@endsection
