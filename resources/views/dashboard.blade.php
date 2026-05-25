<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Dashboard - FULafia Conference Manager</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons via CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --fulafia-gold: #9d7126;
            --fulafia-gold-hover: #835c1c;
            --academic-green: #1a472a;
            --academic-green-hover: #12331e;
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
            overflow-x: hidden;
        }

        .heading-font {
            font-family: 'Outfit', sans-serif;
        }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: var(--academic-green);
            color: #ffffff;
            transition: all 0.3s ease;
            z-index: 1000;
            border-right: 4px solid var(--fulafia-gold);
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            padding: 24px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-brand h5 {
            margin: 0;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .sidebar-menu {
            list-style: none;
            padding: 20px 0;
            margin: 0;
            flex-grow: 1;
        }

        .sidebar-item a {
            display: flex;
            align-items: center;
            padding: 12px 24px;
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
        }

        .sidebar-item a:hover {
            color: #ffffff;
            background-color: rgba(255,255,255,0.06);
            border-left-color: var(--fulafia-gold);
        }

        .sidebar-item.active a {
            color: #ffffff;
            background-color: rgba(255,255,255,0.1);
            border-left-color: var(--fulafia-gold);
            font-weight: 600;
        }

        .sidebar-item i {
            margin-right: 12px;
            font-size: 1.2rem;
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            background-color: var(--academic-green-hover);
        }

        /* Main Content Styling */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            padding: 40px;
            transition: all 0.3s ease;
        }

        /* Top Header Navbar */
        .dashboard-navbar {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -1px rgba(0,0,0,0.01);
            border: 1px solid rgba(157, 113, 38, 0.08);
            padding: 15px 30px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

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

        .btn-gold {
            background-color: var(--fulafia-gold);
            color: #ffffff;
            border: none;
            font-weight: 500;
        }

        .btn-gold:hover {
            background-color: var(--fulafia-gold-hover);
            color: #ffffff;
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
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <h5 class="heading-font text-white">FULafia Portal</h5>
            <span class="badge bg-warning text-dark px-2 py-1 mt-1 small">Member Panel</span>
        </div>
        <ul class="sidebar-menu">
            <li class="sidebar-item active">
                <a href="#"><i class="bi bi-speedometer2"></i> Dashboard</a>
            </li>
            <li class="sidebar-item">
                <a href="#"><i class="bi bi-person-circle"></i> Profile</a>
            </li>
            <li class="sidebar-item">
                <a href="#"><i class="bi bi-journal-bookmark-fill"></i> Conferences</a>
            </li>
            <li class="sidebar-item">
                <a href="#"><i class="bi bi-wallet2"></i> Payments</a>
            </li>
            <li class="sidebar-item">
                <a href="#"><i class="bi bi-chat-left-dots"></i> Complaints</a>
            </li>
        </ul>
        <div class="sidebar-footer">
            <div class="small opacity-75">Logged in as:</div>
            <div class="fw-semibold text-truncate">{{ Auth::user()->title }} {{ Auth::user()->name }}</div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="dashboard-navbar">
            <div class="heading-font fs-4 fw-bold">Dashboard</div>
            <div class="d-flex align-items-center">
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-danger btn-sm px-3 rounded-pill me-2">
                        <i class="bi bi-shield-lock-fill"></i> Admin Panel
                    </a>
                @endif
                <div class="me-3 text-end d-none d-md-block">
                    <div class="fw-bold">{{ Auth::user()->name }}</div>
                    <span class="badge bg-success small" style="font-size: 0.75rem;">Verified Member</span>
                </div>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-outline-danger btn-sm px-3 rounded-pill">
                    <i class="bi bi-box-arrow-left"></i> Log Out
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4 alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Welcome Banner -->
        <div class="welcome-banner shadow-sm">
            <h1 class="heading-font fw-extrabold mb-2">Welcome, {{ Auth::user()->title }} {{ Auth::user()->name }}!</h1>
            <p class="fs-5 opacity-90 mb-3 col-lg-8">You are logged into the Federal University of Lafia Conference Portal. Below are your registration details and quick actions to manage your submissions and payment options.</p>
            <a href="#" class="btn btn-gold btn-lg px-4 py-2 mt-2 shadow-sm"><i class="bi bi-plus-circle me-2"></i> Register for a Conference</a>
        </div>

        <!-- Stat Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-card-icon"><i class="bi bi-journal-text"></i></div>
                    <h6 class="text-muted small uppercase fw-bold">My Conferences</h6>
                    <h3 class="heading-font fw-extrabold mt-1">0</h3>
                    <p class="text-success small mb-0"><i class="bi bi-clock-history me-1"></i> Register for upcoming events</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-card-icon"><i class="bi bi-file-earmark-arrow-up"></i></div>
                    <h6 class="text-muted small uppercase fw-bold">My Submissions</h6>
                    <h3 class="heading-font fw-extrabold mt-1">0</h3>
                    <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i> Pay abstract fee to upload</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-card-icon"><i class="bi bi-credit-card-2-front"></i></div>
                    <h6 class="text-muted small uppercase fw-bold">Total Payments</h6>
                    <h3 class="heading-font fw-extrabold mt-1">₦0.00</h3>
                    <p class="text-danger small mb-0"><i class="bi bi-exclamation-triangle me-1"></i> No transactions found</p>
                </div>
            </div>
        </div>

        <!-- Personal Profile Info -->
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4 rounded-3 bg-white" style="border: 1px solid rgba(157, 113, 38, 0.08) !important;">
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                        <h4 class="heading-font fw-bold text-academic-green mb-0"><i class="bi bi-person-badge-fill me-2"></i> Account Profile</h4>
                        <span class="badge bg-warning text-dark px-3 py-1 font-monospace">{{ strtoupper(Auth::user()->role) }}</span>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="profile-label">Title</div>
                            <div class="profile-value">{{ Auth::user()->title }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="profile-label">First Name</div>
                            <div class="profile-value">{{ Auth::user()->first_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="profile-label">Last Name</div>
                            <div class="profile-value">{{ Auth::user()->last_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="profile-label">Other Names</div>
                            <div class="profile-value">{{ Auth::user()->other_names ?? 'None' }}</div>
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
                        <div class="col-md-6">
                            <div class="profile-label">Institution</div>
                            <div class="profile-value">{{ Auth::user()->institution ?? 'Not Provided' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Actions Panel -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4 rounded-3 bg-white" style="border: 1px solid rgba(157, 113, 38, 0.08) !important;">
                    <h5 class="heading-font fw-bold text-academic-green mb-3 border-bottom pb-2">Support & Complaints</h5>
                    <p class="text-muted small">Having issues with payments, abstract approval, or document uploads? Lodge a formal complaint to our portal support desk.</p>
                    <a href="#" class="btn btn-outline-success w-100 py-2"><i class="bi bi-chat-left-dots-fill me-2"></i> Submit Complaint</a>
                </div>
            </div>
        </div>

        <!-- Ongoing Conferences Section -->
        <div class="row g-4 mt-4">
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
                                            <form action="#" method="POST" class="p-4 registration-form" data-conf-id="{{ $conf->id }}">
                                                @csrf
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
                                                    <button type="submit" class="btn btn-academic btn-lg py-3 shadow-sm" style="border-radius: 12px;" onclick="alert('This will securely initialize a transaction with Credo Gateway and process payment.'); return false;">
                                                        <i class="bi bi-wallet2 me-2"></i> Proceed to Secure Credo Checkout
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
    </div>

    <!-- Invisible Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <!-- jQuery & Bootstrap JS via CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Dynamic Price Calculation Scripts -->
    <script>
        $(document).ready(function() {
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
        });
    </script>
</body>
</html>
