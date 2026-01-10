<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - MK Hotel Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #3b82f6;
            --secondary: #64748b;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #06b6d4;
            --dark: #1e293b;
            --light: #f8fafc;
            --white: #ffffff;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --sidebar-width: 260px;
            --header-height: 64px;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--gray-100);
            color: var(--gray-800);
            line-height: 1.5;
        }

        /* Layout */
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--dark) 0%, var(--gray-800) 100%);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1000;
            overflow-y: auto;
            transition: var(--transition);
        }

        .sidebar-header {
            padding: 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-logo {
            width: 40px;
            height: 40px;
            background: var(--primary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.25rem;
        }

        .sidebar-brand {
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-section {
            margin-bottom: 0.5rem;
        }

        .nav-section-title {
            color: var(--gray-400);
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0.75rem 1.25rem 0.5rem;
            font-weight: 600;
        }

        .nav-item {
            display: block;
            padding: 0.625rem 1.25rem;
            color: var(--gray-300);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-left: 3px solid transparent;
        }

        .nav-item:hover {
            color: white;
            background: rgba(255,255,255,0.05);
        }

        .nav-item.active {
            color: white;
            background: rgba(37, 99, 235, 0.2);
            border-left-color: var(--primary);
        }

        .nav-item i {
            width: 20px;
            text-align: center;
            font-size: 1rem;
        }

        .nav-badge {
            margin-left: auto;
            background: var(--danger);
            color: white;
            font-size: 0.65rem;
            padding: 0.125rem 0.5rem;
            border-radius: 999px;
            font-weight: 600;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        /* Header */
        .header {
            background: white;
            height: var(--header-height);
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--gray-600);
            cursor: pointer;
            padding: 0.5rem;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: var(--gray-500);
        }

        .breadcrumb a {
            color: var(--gray-500);
            text-decoration: none;
        }

        .breadcrumb a:hover {
            color: var(--primary);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-btn {
            background: none;
            border: none;
            font-size: 1.1rem;
            color: var(--gray-500);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 8px;
            transition: var(--transition);
            position: relative;
        }

        .header-btn:hover {
            background: var(--gray-100);
            color: var(--gray-700);
        }

        .header-btn .badge {
            position: absolute;
            top: 0;
            right: 0;
            width: 18px;
            height: 18px;
            background: var(--danger);
            color: white;
            font-size: 0.65rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            padding: 0.375rem 0.75rem;
            border-radius: 8px;
            transition: var(--transition);
        }

        .user-menu:hover {
            background: var(--gray-100);
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .user-info {
            text-align: left;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--gray-800);
        }

        .user-role {
            font-size: 0.75rem;
            color: var(--gray-500);
        }

        /* Page Content */
        .page-content {
            padding: 1.5rem;
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-900);
        }

        .page-subtitle {
            font-size: 0.875rem;
            color: var(--gray-500);
            margin-top: 0.25rem;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: var(--transition);
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-secondary {
            background: var(--gray-100);
            color: var(--gray-700);
            border: 1px solid var(--gray-200);
        }

        .btn-secondary:hover {
            background: var(--gray-200);
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.8125rem;
        }

        .btn-icon {
            width: 36px;
            height: 36px;
            padding: 0;
            justify-content: center;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid var(--gray-200);
        }

        .card-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title {
            font-weight: 600;
            font-size: 1rem;
            color: var(--gray-800);
        }

        .card-body {
            padding: 1.25rem;
        }

        /* Stat Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            border: 1px solid var(--gray-200);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .stat-icon.blue { background: rgba(37, 99, 235, 0.1); color: var(--primary); }
        .stat-icon.green { background: rgba(34, 197, 94, 0.1); color: var(--success); }
        .stat-icon.orange { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
        .stat-icon.red { background: rgba(239, 68, 68, 0.1); color: var(--danger); }
        .stat-icon.cyan { background: rgba(6, 182, 212, 0.1); color: var(--info); }

        .stat-content {
            flex: 1;
        }

        .stat-label {
            font-size: 0.8125rem;
            color: var(--gray-500);
            margin-bottom: 0.25rem;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-900);
        }

        .stat-change {
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }

        .stat-change.up { color: var(--success); }
        .stat-change.down { color: var(--danger); }

        /* Tables */
        .table-responsive {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 0.875rem 1rem;
            text-align: left;
            border-bottom: 1px solid var(--gray-200);
        }

        .table th {
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--gray-500);
            background: var(--gray-50);
        }

        .table td {
            font-size: 0.875rem;
            color: var(--gray-700);
        }

        .table tbody tr:hover {
            background: var(--gray-50);
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.625rem;
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 999px;
        }

        .badge-primary { background: rgba(37, 99, 235, 0.1); color: var(--primary); }
        .badge-success { background: rgba(34, 197, 94, 0.1); color: var(--success); }
        .badge-warning { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
        .badge-danger { background: rgba(239, 68, 68, 0.1); color: var(--danger); }
        .badge-info { background: rgba(6, 182, 212, 0.1); color: var(--info); }
        .badge-secondary { background: var(--gray-100); color: var(--gray-600); }

        /* Forms */
        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--gray-700);
            margin-bottom: 0.375rem;
        }

        .form-control {
            width: 100%;
            padding: 0.625rem 0.875rem;
            font-size: 0.875rem;
            border: 1px solid var(--gray-300);
            border-radius: 8px;
            background: white;
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-control.is-invalid {
            border-color: var(--danger);
        }

        .invalid-feedback {
            font-size: 0.8125rem;
            color: var(--danger);
            margin-top: 0.25rem;
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            padding-right: 2.5rem;
        }

        /* Grid */
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: -0.5rem;
        }

        .col {
            flex: 1;
            padding: 0.5rem;
        }

        .col-6 { flex: 0 0 50%; max-width: 50%; }
        .col-4 { flex: 0 0 33.333%; max-width: 33.333%; }
        .col-3 { flex: 0 0 25%; max-width: 25%; }
        .col-8 { flex: 0 0 66.666%; max-width: 66.666%; }

        /* Alerts */
        .alert {
            padding: 0.875rem 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            color: #166534;
            border: 1px solid rgba(34, 197, 94, 0.2);
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #991b1b;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            color: #92400e;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        /* Dropdown */
        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            border: 1px solid var(--gray-200);
            min-width: 180px;
            padding: 0.5rem;
            display: none;
            z-index: 1000;
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            color: var(--gray-700);
            text-decoration: none;
            border-radius: 6px;
            transition: var(--transition);
        }

        .dropdown-item:hover {
            background: var(--gray-100);
        }

        .dropdown-divider {
            height: 1px;
            background: var(--gray-200);
            margin: 0.5rem 0;
        }

        /* Pagination */
        .pagination {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            list-style: none;
        }

        .pagination .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            font-size: 0.875rem;
            color: var(--gray-600);
            text-decoration: none;
            border-radius: 8px;
            transition: var(--transition);
        }

        .pagination .page-link:hover {
            background: var(--gray-100);
        }

        .pagination .active .page-link {
            background: var(--primary);
            color: white;
        }

        /* Charts container */
        .chart-container {
            position: relative;
            height: 300px;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .menu-toggle {
                display: block;
            }

            .col-6, .col-4, .col-3, .col-8 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }

        /* Utility classes */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-muted { color: var(--gray-500); }
        .text-success { color: var(--success); }
        .text-danger { color: var(--danger); }
        .text-warning { color: var(--warning); }
        .mb-0 { margin-bottom: 0; }
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-3 { margin-bottom: 1rem; }
        .mb-4 { margin-bottom: 1.5rem; }
        .mt-3 { margin-top: 1rem; }
        .mt-4 { margin-top: 1.5rem; }
        .d-flex { display: flex; }
        .justify-between { justify-content: space-between; }
        .align-center { align-items: center; }
        .gap-2 { gap: 0.5rem; }
        .gap-3 { gap: 1rem; }
        .w-100 { width: 100%; }
    </style>

    @stack('styles')
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">MK</div>
                <span class="sidebar-brand">MK Hotel</span>
            </div>

            <nav class="sidebar-nav">
                <!-- Dashboard -->
                <div class="nav-section">
                    <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-th-large"></i>
                        <span>Dashboard</span>
                    </a>
                </div>

                <!-- Hotel Operations -->
                <div class="nav-section">
                    <div class="nav-section-title">Hotel Operations</div>

                    @if(auth()->user()->hasPermission('bookings.view'))
                    <a href="{{ route('admin.bookings.index') }}" class="nav-item {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check"></i>
                        <span>Bookings</span>
                    </a>
                    @endif

                    @if(auth()->user()->hasPermission('calendar.view'))
                    <a href="{{ route('admin.calendar.index') }}" class="nav-item {{ request()->routeIs('admin.calendar.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Calendar</span>
                    </a>
                    @endif

                    @if(auth()->user()->hasPermission('rooms.view'))
                    <a href="{{ route('admin.rooms.index') }}" class="nav-item {{ request()->routeIs('admin.rooms.*') ? 'active' : '' }}">
                        <i class="fas fa-door-open"></i>
                        <span>Rooms</span>
                    </a>
                    @endif

                    @if(auth()->user()->hasPermission('room_types.view'))
                    <a href="{{ route('admin.room-types.index') }}" class="nav-item {{ request()->routeIs('admin.room-types.*') ? 'active' : '' }}">
                        <i class="fas fa-bed"></i>
                        <span>Room Types</span>
                    </a>
                    @endif

                    @if(auth()->user()->hasPermission('guests.view'))
                    <a href="{{ route('admin.guests.index') }}" class="nav-item {{ request()->routeIs('admin.guests.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        <span>Guests</span>
                    </a>
                    @endif
                </div>

                <!-- CMS -->
                <div class="nav-section">
                    <div class="nav-section-title">Content Management</div>

                    @if(auth()->user()->hasPermission('pages.view'))
                    <a href="{{ route('admin.pages.index') }}" class="nav-item {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                        <i class="fas fa-file-alt"></i>
                        <span>Pages</span>
                    </a>
                    @endif

                    @if(auth()->user()->hasPermission('news.view'))
                    <a href="{{ route('admin.news.index') }}" class="nav-item {{ request()->routeIs('admin.news.*') ? 'active' : '' }}">
                        <i class="fas fa-newspaper"></i>
                        <span>News & Blog</span>
                    </a>
                    @endif

                    @if(auth()->user()->hasPermission('gallery.view'))
                    <a href="{{ route('admin.gallery.index') }}" class="nav-item {{ request()->routeIs('admin.gallery.*') ? 'active' : '' }}">
                        <i class="fas fa-images"></i>
                        <span>Gallery</span>
                    </a>
                    @endif

                    @if(auth()->user()->hasPermission('amenities.view'))
                    <a href="{{ route('admin.amenities.index') }}" class="nav-item {{ request()->routeIs('admin.amenities.*') ? 'active' : '' }}">
                        <i class="fas fa-concierge-bell"></i>
                        <span>Amenities</span>
                    </a>
                    @endif
                </div>

                <!-- Reports -->
                <div class="nav-section">
                    <div class="nav-section-title">Analytics</div>

                    @if(auth()->user()->hasPermission('reports.view'))
                    <a href="{{ route('admin.reports.index') }}" class="nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i>
                        <span>Reports</span>
                    </a>
                    @endif
                </div>

                <!-- Administration -->
                <div class="nav-section">
                    <div class="nav-section-title">Administration</div>

                    @if(auth()->user()->hasPermission('users.view'))
                    <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="fas fa-user-shield"></i>
                        <span>Users</span>
                    </a>
                    @endif

                    @if(auth()->user()->hasPermission('roles.view'))
                    <a href="{{ route('admin.roles.index') }}" class="nav-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                        <i class="fas fa-user-tag"></i>
                        <span>Roles & Permissions</span>
                    </a>
                    @endif

                    @if(auth()->user()->hasPermission('settings.view'))
                    <a href="{{ route('admin.settings.index') }}" class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                    @endif
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="header-left">
                    <button class="menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">
                        <i class="fas fa-bars"></i>
                    </button>
                    <nav class="breadcrumb">
                        @yield('breadcrumb')
                    </nav>
                </div>

                <div class="header-right">
                    <button class="header-btn" title="Notifications">
                        <i class="fas fa-bell"></i>
                        <span class="badge">3</span>
                    </button>

                    <div class="dropdown">
                        <div class="user-menu" onclick="this.nextElementSibling.classList.toggle('show')">
                            <div class="user-avatar">
                                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                            </div>
                            <div class="user-info">
                                <div class="user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
                                <div class="user-role">{{ auth()->user()->getPrimaryRole()?->name ?? 'Administrator' }}</div>
                            </div>
                            <i class="fas fa-chevron-down" style="font-size: 0.75rem; color: var(--gray-400);"></i>
                        </div>
                        <div class="dropdown-menu">
                            <a href="{{ route('admin.profile') }}" class="dropdown-item">
                                <i class="fas fa-user"></i>
                                My Profile
                            </a>
                            <a href="{{ route('admin.settings.index') }}" class="dropdown-item">
                                <i class="fas fa-cog"></i>
                                Settings
                            </a>
                            <div class="dropdown-divider"></div>
                            <form action="{{ route('admin.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item w-100" style="background: none; border: none; cursor: pointer;">
                                    <i class="fas fa-sign-out-alt"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="page-content">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <script>
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown-menu').forEach(function(menu) {
                    menu.classList.remove('show');
                });
            }
        });

        // CSRF token for AJAX requests
        window.csrfToken = '{{ csrf_token() }}';
    </script>

    @stack('scripts')
</body>
</html>
