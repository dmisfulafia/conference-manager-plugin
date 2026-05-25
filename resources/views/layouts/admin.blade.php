<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - FULafia Admin Command Center</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons via CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

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
            background-color: #111827;
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
            border-bottom: 1px solid rgba(255,255,255,0.08);
            background-color: #0f172a;
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
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
        }

        .sidebar-item a:hover {
            color: #ffffff;
            background-color: rgba(255,255,255,0.04);
            border-left-color: var(--fulafia-gold);
        }

        .sidebar-item.active a {
            color: #ffffff;
            background-color: rgba(255,255,255,0.08);
            border-left-color: var(--fulafia-gold);
            font-weight: 600;
        }

        .sidebar-item i {
            margin-right: 12px;
            font-size: 1.2rem;
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(255,255,255,0.08);
            background-color: #0f172a;
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
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
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
            background-color: var(--fulafia-gold-light);
            color: var(--fulafia-gold);
            font-size: 1.4rem;
            margin-bottom: 16px;
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

        .text-fulafia-gold {
            color: var(--fulafia-gold);
        }

        .text-academic-green {
            color: var(--academic-green);
        }

        /* Custom Table Styling */
        .table-responsive {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 15px;
            border: 1px solid rgba(157, 113, 38, 0.08);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.01);
        }

        /* DataTables export buttons customization */
        .dt-buttons .btn {
            background-color: var(--academic-green) !important;
            border-color: var(--academic-green) !important;
            color: #ffffff !important;
            border-radius: 6px !important;
            font-size: 0.85rem !important;
            font-weight: 500 !important;
            margin-right: 5px !important;
            padding: 6px 12px !important;
        }

        .dt-buttons .btn:hover {
            background-color: var(--academic-green-hover) !important;
            border-color: var(--academic-green-hover) !important;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand d-flex flex-column align-items-center py-4 text-center">
            <img src="{{ asset('logo.png') }}" alt="FULafia Logo" class="mb-2" style="height: 60px; width: auto; object-fit: contain; filter: drop-shadow(0px 2px 4px rgba(0,0,0,0.5));">
            <h5 class="heading-font text-white mb-0">FULafia Portal</h5>
            <span class="badge bg-danger text-white px-2 py-1 mt-2 small">Admin Command Center</span>
        </div>
        <ul class="sidebar-menu">
            <li class="sidebar-item {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer"></i> Dashboard</a>
            </li>
            <li class="sidebar-item {{ Request::routeIs('admin.users') ? 'active' : '' }}">
                <a href="{{ route('admin.users') }}"><i class="bi bi-people-fill"></i> Manage Users</a>
            </li>
            <li class="sidebar-item {{ Request::routeIs('admin.conferences') ? 'active' : '' }}">
                <a href="{{ route('admin.conferences') }}"><i class="bi bi-journal-bookmark-fill"></i> Conferences</a>
            </li>
            <li class="sidebar-item {{ Request::routeIs('admin.payments') ? 'active' : '' }}">
                <a href="{{ route('admin.payments') }}"><i class="bi bi-wallet2"></i> Payments Log</a>
            </li>
            <li class="sidebar-item {{ Request::routeIs('admin.complaints') ? 'active' : '' }}">
                <a href="{{ route('admin.complaints') }}"><i class="bi bi-chat-left-dots"></i> Complaints</a>
            </li>
            <li class="sidebar-item mt-4">
                <a href="{{ route('dashboard') }}" class="text-warning"><i class="bi bi-arrow-left-circle"></i> User Dashboard</a>
            </li>
        </ul>
        <div class="sidebar-footer">
            <div class="small opacity-50">Admin Session:</div>
            <div class="fw-semibold text-truncate text-warning">{{ Auth::user()->title }} {{ Auth::user()->last_name }}</div>
            <span class="badge bg-secondary px-2 py-1 small mt-1" style="font-size: 0.7rem;">{{ strtoupper(Auth::user()->role) }}</span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="dashboard-navbar">
            <div class="heading-font fs-4 fw-bold">@yield('page_title')</div>
            <div class="d-flex align-items-center">
                <div class="me-3 text-end d-none d-md-block">
                    <div class="fw-bold">{{ Auth::user()->name }}</div>
                    <span class="badge bg-danger small" style="font-size: 0.7rem;">Administrator</span>
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

    <!-- jQuery and Bootstrap JS via CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables JS & Responsive & Buttons via CDN -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    @yield('scripts')
</body>
</html>
