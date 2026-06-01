<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - FULafia Conference Portal</title>
    
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
            --fulafia-gold-light: #f7eecd;
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

        .text-fulafia-gold {
            color: var(--fulafia-gold) !important;
        }

        .text-academic-green {
            color: var(--academic-green) !important;
        }

        .bg-fulafia-light {
            background-color: var(--fulafia-gold-light) !important;
        }

        .bg-academic-green {
            background-color: var(--academic-green) !important;
            color: #ffffff !important;
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

        .btn-academic {
            background-color: var(--academic-green);
            color: #ffffff;
            border: none;
            font-weight: 500;
        }

        .btn-academic:hover {
            background-color: var(--academic-green-hover);
            color: #ffffff;
        }

        @yield('styles')
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand d-flex flex-column align-items-center py-4 text-center">
            <img src="{{ asset('logo.png') }}" alt="FULafia Logo" class="mb-2" style="height: 60px; width: auto; object-fit: contain; filter: drop-shadow(0px 2px 4px rgba(0,0,0,0.5));">
            <h5 class="heading-font text-white mb-0">FULafia Portal</h5>
            <span class="badge bg-warning text-dark px-2 py-1 mt-2 small">Member Panel</span>
        </div>
        <ul class="sidebar-menu">
            <li class="sidebar-item {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
            </li>
            <li class="sidebar-item {{ Request::routeIs('profile.show') ? 'active' : '' }}">
                <a href="{{ route('profile.show') }}"><i class="bi bi-person-circle"></i> Profile</a>
            </li>
            <li class="sidebar-item {{ Request::routeIs('conferences.index') ? 'active' : '' }}">
                <a href="{{ route('conferences.index') }}"><i class="bi bi-journal-bookmark-fill"></i> Conferences</a>
            </li>
            <li class="sidebar-item {{ Request::routeIs('payments.*') ? 'active' : '' }}">
                <a href="{{ route('payments.index') }}"><i class="bi bi-wallet2"></i> Payments</a>
            </li>
            <li class="sidebar-item {{ Request::routeIs('complaints.index') ? 'active' : '' }}">
                <a href="{{ route('complaints.index') }}"><i class="bi bi-chat-left-dots"></i> Complaints</a>
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
            <div class="heading-font fs-4 fw-bold">@yield('page_title')</div>
            <div class="d-flex align-items-center">
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-danger btn-sm px-3 rounded-pill me-2">
                        <i class="bi bi-shield-lock-fill"></i> Admin Panel
                    </a>
                @endif
                <div class="me-3 text-end d-none d-md-block">
                    <div class="fw-bold">{{ Auth::user()->name }}</div>
                    @if(stripos(Auth::user()->occupation, 'student') !== false)
                        @if(Auth::user()->student_id_verified)
                            <span class="badge bg-success small" style="font-size: 0.75rem;"><i class="bi bi-patch-check-fill me-1"></i> Verified Student</span>
                        @else
                            <span class="badge bg-warning text-dark small" style="font-size: 0.75rem;"><i class="bi bi-exclamation-triangle-fill me-1"></i> Unverified Student</span>
                        @endif
                    @else
                        <span class="badge bg-success small" style="font-size: 0.75rem;"><i class="bi bi-patch-check-fill me-1"></i> Verified Member</span>
                    @endif
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

        @if (session('error'))
            <div class="alert alert-danger border-0 shadow-sm mb-4 alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Main Content Injection -->
        @yield('content')
    </div>

    <!-- Invisible Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <!-- jQuery & Bootstrap JS via CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Credo Live Inline SDK -->
    <script src="https://pay.credocentral.com/inline.js"></script>

    @yield('scripts')
</body>
</html>
