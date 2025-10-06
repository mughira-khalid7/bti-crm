<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BD CRM - Admin Dashboard')</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('faviconfd.ico') }}" sizes="any">
    <link rel="shortcut icon" href="{{ asset('faviconfd.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --primary-color: #e67717;
            --primary-dark: #d4690e;
            --primary-light: #f8b84d;
            --sidebar-width: 250px;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            color: white;
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-align: left;
        }

        .sidebar-brand {
            display: block;
            text-decoration: none;
            margin: 0;
            padding: 0;
            cursor: pointer;
            transition: none;
        }

        .sidebar-brand:hover {
            background-color: transparent !important;
            border-left-color: transparent !important;
            transform: none !important;
        }

        .sidebar-brand img {
            max-width: 100%;
            height: auto;
            max-height: 80px;
            margin: 0;
            padding: 0;
            transition: none;
        }

        .sidebar-brand img:hover {
            opacity: 1;
            transform: none;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            margin: 0.25rem 0;
            padding: 0.75rem 1.5rem;
            border-radius: 0;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .sidebar a:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            border-left-color: var(--primary-light);
        }

        .sidebar a.active {
            color: white;
            background-color: rgba(230, 119, 23, 0.2);
            border-left-color: var(--primary-color);
        }

        .sidebar a i {
            width: 20px;
            margin-right: 0.75rem;
            text-align: center;
        }

        .main-content {
            /* margin-left: var(--sidebar-width); */
            /* min-height: 100vh; */
            position: relative;
            /* width: calc(100% - var(--sidebar-width)); */
        }

        .navbar-custom {
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid #e9ecef;
            height: 60px;
            display: flex;
            align-items: center;
            padding: 0 2rem;
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            z-index: 999;
        }

        .navbar-brand {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary-color);
            margin: 0;
        }

        .content {
            padding-top: calc(60px + 2rem);
            margin-left: 0;
            margin-right: 0;
            width: 100%;
            padding-left: var(--sidebar-width);
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        /* Unified dashboard widget card sizing */
        .card.widget {
            min-height: 120px;
        }

        .card.widget .card-body {
            display: flex;
            align-items: center;
            padding: 1rem !important;
            gap: .75rem;
        }

        .card.widget .circular-icon,
        .card.widget .rounded-circle {
            width: 48px !important;
            height: 48px !important;
            font-size: 1rem !important;
            margin-right: .75rem !important;
        }

        .card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        /* Widget Cards */
        .widget-card {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .widget-card .card-title {
            color: white;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .widget-card .card-text {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0;
        }

        .widget-icon {
            font-size: 2.5rem;
            opacity: 0.8;
            float: right;
            margin-top: -0.5rem;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(45deg, var(--primary-color), var(--primary-dark));
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(45deg, var(--primary-dark), #c15d0a);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(230, 119, 23, 0.3);
        }

        /* Enforce brand primary for Bootstrap utility classes */
        .bg-primary {
            background-color: var(--primary-color) !important;
        }

        .text-bg-primary {
            background-color: var(--primary-color) !important;
            color: #fff !important;
        }

        .border-primary {
            border-color: var(--primary-color) !important;
        }

        .btn-outline-primary {
            color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
        }

        .btn-outline-primary:hover,
        .btn-outline-primary:focus {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: #fff !important;
        }

        /* Tables */
        .table {
            border-radius: 8px;
            overflow: hidden;
        }

        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
            color: #495057;
        }

        .table tbody tr:hover {
            background-color: rgba(230, 119, 23, 0.05);
        }

        /* Improve spacing between avatar circle and text inside tables */
        .table .circular-icon {
            margin-bottom: 0;
            margin-right: .75rem;
            width: 32px;
            height: 32px;
            font-size: 0.875rem;
            flex: 0 0 32px;
        }

        /* Modern Status Badges */
        .badge {
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.2rem 0.5rem;
            border-radius: 5px;
            border: none;
            letter-spacing: 0.3px;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        /* Badge Color Variants - Modern Soft Colors */
        .badge.bg-primary {
            background-color: #e8eaf6 !important;
            color: #5e35b1 !important;
        }

        .badge.bg-success {
            background-color: #e8f5e9 !important;
            color: #2e7d32 !important;
        }

        .badge.bg-danger {
            background-color: #ffebee !important;
            color: #c62828 !important;
        }

        .badge.bg-warning {
            background-color: #fff8e1 !important;
            color: #f57c00 !important;
        }

        .badge.bg-info {
            background-color: #e1f5fe !important;
            color: #0277bd !important;
        }

        .badge.bg-secondary {
            background-color: #f5f5f5 !important;
            color: #616161 !important;
        }

        .badge.bg-light {
            background-color: #fafafa !important;
            color: #424242 !important;
        }

        .badge.bg-dark {
            background-color: #37474f !important;
            color: #ffffff !important;
        }

        /* Badge with rounded-pill class */
        .badge.rounded-pill {
            border-radius: 50rem !important;
        }

        /* Profile Avatar */
        .profile-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Circular Icon Cards */
        .circular-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #fff;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .circular-icon.primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        }

        .circular-icon.success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }

        .circular-icon.info {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        }

        .circular-icon.warning {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        }

        .circular-icon.danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }

        /* Sidebar Toggle */
        .sidebar-toggle {
            display: none;
        }

        /* Pagination Styles */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin: 0;
            padding: 1rem 0;
        }

        .pagination .page-link {
            color: var(--primary-color);
            background-color: transparent;
            border: 1px solid #dee2e6;
            padding: 0.5rem 0.75rem;
            margin: 0 0.125rem;
            border-radius: 0.375rem;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
            min-width: 38px;
            text-align: center;
        }

        .pagination .page-link:hover {
            color: #fff;
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            transform: translateY(-1px);
        }

        .pagination .page-item.active .page-link {
            color: #fff;
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            background-color: transparent;
            border-color: #dee2e6;
            cursor: not-allowed;
        }

        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link {
            border-radius: 0.375rem;
        }

        /* Override any default pagination styling */
        .pagination * {
            box-sizing: border-box;
        }

        .pagination .page-link {
            position: relative;
            display: block;
            padding: 0.5rem 0.75rem;
            margin: 0;
            line-height: 1.25;
            text-decoration: none;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            background-color: transparent;
            transition: all 0.3s ease;
        }

        /* Hide any default icons or content */
        .pagination .page-link svg,
        .pagination .page-link i,
        .pagination .page-link::before,
        .pagination .page-link::after {
            display: none !important;
        }

        /* Custom navigation icons for first/last page */
        .pagination .page-item:first-child .page-link {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        .pagination .page-item:last-child .page-link {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        /* Ensure text content is properly displayed */
        .pagination .page-link span,
        .pagination .page-link {
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Previous/Next text styling */
        .pagination .page-item:first-child .page-link:not([aria-label*="Previous"]):not([aria-label*="previous"]) {
            content: "Previous";
        }

        .pagination .page-item:last-child .page-link:not([aria-label*="Next"]):not([aria-label*="next"]) {
            content: "Next";
        }

        /* Pagination wrapper */
        .pagination-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 1rem 0;
        }

        /* Pagination info text */
        .pagination-info {
            color: #6c757d;
            font-size: 0.875rem;
            margin-top: 0.75rem;
            text-align: center;
            font-weight: 500;
        }

        /* Ensure pagination is properly contained */
        .pagination {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
        }

        .pagination .page-item {
            margin: 0;
            padding: 0;
        }

        /* Modern Dashboard Enhancements */
        .hover-lift {
            transition: all 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12) !important;
        }

        .hover-bg-light:hover {
            background-color: rgba(0, 0, 0, 0.02) !important;
        }

        /* Activity List Styling */
        .activity-list {
            scrollbar-width: thin;
            scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
        }

        .activity-list::-webkit-scrollbar {
            width: 6px;
        }

        .activity-list::-webkit-scrollbar-track {
            background: transparent;
        }

        .activity-list::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }

        .activity-item {
            transition: all 0.2s ease;
        }

        .activity-item:last-child {
            border-bottom: none !important;
        }

        /* Card Headers */
        .card-header {
            background-color: #fff !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        }

        /* Rounded Pills */
        .rounded-pill {
            border-radius: 50rem !important;
        }

        /* Nav Pills Modern Style */
        .nav-pills .nav-link {
            color: #6c757d;
            transition: all 0.3s ease;
        }

        .nav-pills .nav-link.active {
            background-color: var(--primary-color) !important;
            color: white;
        }

        .nav-pills .nav-link:hover:not(.active) {
            background-color: rgba(230, 119, 23, 0.1);
            color: var(--primary-color);
        }

        /* Progress bars modern styling */
        .progress {
            background-color: rgba(0, 0, 0, 0.08);
        }

        /* Badge modern styling */
        .badge {
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        /* Table improvements */
        .table thead {
            background-color: #f8f9fa !important;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(230, 119, 23, 0.03);
            transform: scale(1.01);
        }

        /* Gradient backgrounds for icons */
        .gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .gradient-info {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        }

        .gradient-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }

        .gradient-warning {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        }

        .gradient-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }

        /* Chart container */
        .chart-container {
            position: relative;
            height: 320px;
            margin: 0.5rem 0;
        }

        /* Ensure containers are always fluid for admin */
        .container,
        .container-lg,
        .container-md,
        .container-sm,
        .container-xl,
        .container-xxl {
            max-width: 100% !important;
            width: 100% !important;
            padding-left: 1rem;
            padding-right: 1rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                z-index: 1050;
                transition: transform 0.3s ease;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }

            .navbar-custom {
                left: 0;
            }

            .sidebar-toggle {
                display: block;
            }

            .pagination {
                flex-wrap: wrap;
                gap: 0.25rem;
            }

            .pagination .page-link {
                padding: 0.375rem 0.5rem;
                font-size: 0.875rem;
                min-width: 32px;
            }

            .chart-container {
                height: 250px;
            }

            .activity-list {
                max-height: 350px !important;
            }
        }

        @media (max-width: 576px) {
            .card-body.p-4 {
                padding: 1.25rem !important;
            }

            .card-header.px-4 {
                padding-left: 1.25rem !important;
                padding-right: 1.25rem !important;
            }
        }
    </style>
</head>

<body>
    <div>
        <div>
            <nav class="sidebar">
                <div class="sidebar-header">
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
                        <img src="{{ asset('bti logo 1.png') }}" alt="BTI Logo">
                    </a>
                </div>
                <div class="sidebar-nav">
                    <a href="{{ route('admin.dashboard') }}"
                        class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.users.index') }}"
                        class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        BD Users
                    </a>
                    <a href="{{ route('admin.goals.index') }}"
                        class="{{ request()->routeIs('admin.goals.*') ? 'active' : '' }}">
                        <i class="fas fa-bullseye"></i>
                        Goals
                    </a>
                    <a href="{{ route('admin.proposals.index') }}"
                        class="{{ request()->routeIs('admin.proposals.index') ? 'active' : '' }}">
                        <i class="fas fa-file-alt"></i>
                        Proposals
                    </a>
                    {{-- <a href="{{ route('admin.proposals.deleted') }}"
                        class="{{ request()->routeIs('admin.proposals.deleted') ? 'active' : '' }}">
                        <i class="fas fa-trash-alt"></i>
                        Deleted Proposals
                    </a> --}}
                    <a href="{{ route('admin.upwork-profiles.index') }}"
                        class="{{ request()->routeIs('admin.upwork-profiles.*') ? 'active' : '' }}">
                        <i class="fas fa-user-circle"></i>
                        Profile (Up-work)
                    </a>
                    <a href="{{ route('admin.calendar.index') }}"
                        class="{{ request()->routeIs('admin.calendar.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i>
                        Calendar
                    </a>
                    <a href="{{ route('admin.notes.index') }}"
                        class="{{ request()->routeIs('admin.notes.*') ? 'active' : '' }}">
                        <i class="fas fa-sticky-note"></i>
                        Resource (Notes)
                    </a>


                </div>
            </nav>
        </div>
        <div>
            <!-- Main Content -->
            <div class="main-content">
                <div class="container-fluid">
                    <!-- Navbar -->
                    <nav class="navbar navbar-custom">
                        <div class="d-flex align-items-center">
                            <button class="btn btn-link text-decoration-none me-3 d-md-none" onclick="toggleSidebar()">
                                <i class="fas fa-bars"></i>
                            </button>
                            <h1 class="navbar-brand mb-0">BD CRM – Admin Dashboard</h1>
                        </div>
                        <div class="ms-auto">
                            <div class="dropdown">
                                <button
                                    class="btn btn-link dropdown-toggle d-flex align-items-center text-decoration-none"
                                    type="button" data-bs-toggle="dropdown">
                                    @php($me = Auth::user())
                                    <x-avatar :user="$me" :size="40" class="me-2" />
                                    <span class="text-dark">{{ $me->name ?? 'Admin User' }}</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                            <i class="fas fa-user me-2"></i>Edit Profile
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </nav>

                    <!-- Content -->
                    <div class="content">
                        @yield('content')
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Sidebar -->




    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1060;">
        @if (session('success'))
            <x-toast type="success" title="Success!" message="{{ session('success') }}" />
        @endif

        @if (session('error'))
            <x-toast type="error" title="Error!" message="{{ session('error') }}" />
        @endif

        @if (session('warning'))
            <x-toast type="warning" title="Warning!" message="{{ session('warning') }}" />
        @endif

        @if (session('info'))
            <x-toast type="info" title="Info" message="{{ session('info') }}" />
        @endif

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <x-toast type="error" title="Validation Error" message="{{ $error }}" />
            @endforeach
        @endif
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom JS -->
    <script>
        // Toast Notification System
        class ToastNotification {
            constructor() {
                this.container = document.querySelector('.toast-container');
                this.initExistingToasts();
            }

            initExistingToasts() {
                // Initialize existing toasts from session
                const existingToasts = document.querySelectorAll('.toast');
                existingToasts.forEach(toast => {
                    const bsToast = new bootstrap.Toast(toast);
                    bsToast.show();
                });
            }

            show(type, title, message, duration = 5000) {
                const toastId = 'toast-' + Date.now();
                const typeClasses = {
                    'success': 'text-bg-success',
                    'error': 'text-bg-danger',
                    'warning': 'text-bg-warning',
                    'info': 'text-bg-info',
                    'primary': 'text-bg-primary'
                };

                const typeIcons = {
                    'success': 'fas fa-check-circle',
                    'error': 'fas fa-exclamation-circle',
                    'warning': 'fas fa-exclamation-triangle',
                    'info': 'fas fa-info-circle',
                    'primary': 'fas fa-bell'
                };

                const toastHtml = `
                    <div id="${toastId}" class="toast align-items-center ${typeClasses[type]}" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="${duration}">
                        <div class="d-flex">
                            <div class="toast-body">
                                <div class="d-flex align-items-center">
                                    <i class="${typeIcons[type]} me-2"></i>
                                    <div>
                                        <strong>${title}</strong>
                                        ${message ? `<div class="small">${message}</div>` : ''}
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                `;

                this.container.insertAdjacentHTML('beforeend', toastHtml);
                const toastElement = document.getElementById(toastId);
                const bsToast = new bootstrap.Toast(toastElement);
                bsToast.show();

                // Remove toast element after it's hidden
                toastElement.addEventListener('hidden.bs.toast', () => {
                    toastElement.remove();
                });
            }

            // Convenience methods
            success(title, message) {
                this.show('success', title, message);
            }

            error(title, message) {
                this.show('error', title, message);
            }

            warning(title, message) {
                this.show('warning', title, message);
            }

            info(title, message) {
                this.show('info', title, message);
            }

            primary(title, message) {
                this.show('primary', title, message);
            }
        }

        // Initialize toast system
        window.toast = new ToastNotification();

        // Dynamic avatar colors + tooltips
        function initDynamicAvatars(root = document) {
            const palette = ['#e67717', '#1e88e5', '#43a047', '#8e24aa', '#fb8c00', '#00acc1', '#f4511e', '#3949ab'];
            const avatars = root.querySelectorAll('.avatar-dynamic, .profile-avatar');
            avatars.forEach((el) => {
                const color = palette[Math.floor(Math.random() * palette.length)];
                el.style.background = color;
                el.style.color = '#fff';
                el.style.fontWeight = '700';
                if (el.getAttribute('data-bs-toggle') === 'tooltip') {
                    new bootstrap.Tooltip(el);
                }
            });
        }
        initDynamicAvatars(document);

        // Mobile sidebar toggle
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('show');
        }

        // Add mobile menu button for smaller screens
        if (window.innerWidth <= 768) {
            const navbar = document.querySelector('.navbar-custom');
            const toggleBtn = document.createElement('button');
            toggleBtn.className = 'btn btn-outline-primary me-2';
            toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
            toggleBtn.onclick = toggleSidebar;
            navbar.insertBefore(toggleBtn, navbar.querySelector('.navbar-brand'));
        }

        // AJAX helper for form submissions with toast notifications
        function submitFormWithToast(form, successMessage, errorMessage) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const url = this.action;
                const method = this.method;

                fetch(url, {
                        method: method,
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    })
                    .then(async response => {
                        const contentType = response.headers.get('content-type') || '';
                        const data = contentType.includes('application/json') ? await response.json() : {};
                        if (response.ok && data.success) {
                            window.toast.success('Success!', successMessage);
                            if (data.redirect) {
                                setTimeout(() => window.location.href = data.redirect, 800);
                            }
                            return;
                        }
                        if (response.status === 422 && data && data.errors) {
                            const firstField = Object.keys(data.errors)[0];
                            const firstMsg = data.errors[firstField][0];
                            window.toast.error('Validation Error', firstMsg || errorMessage ||
                                'Please correct the form.');
                            return;
                        }
                        window.toast.error('Error!', (data && data.message) || errorMessage ||
                            'An unexpected error occurred.');
                    })
                    .catch(error => {
                        window.toast.error('Error!', 'An unexpected error occurred.');
                    });
            });
        }

        // Global helper for delete confirmations with SweetAlert
        function confirmDelete(element, url, successMessage = 'Item deleted successfully!') {
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to delete this item?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Deleted!', successMessage, 'success');
                                // Remove the element from DOM and reload after a delay
                                setTimeout(() => {
                                    element.closest('tr')?.remove();
                                    window.location.reload();
                                }, 1000);
                            } else {
                                Swal.fire('Error!', data.message || 'Failed to delete item.', 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error!', 'An unexpected error occurred.', 'error');
                        });
                }
            });
        }

        // Fix pagination styling issues
        function fixPaginationStyling() {
            // Remove any problematic chevron icons
            document.querySelectorAll('.pagination .page-link svg, .pagination .page-link i').forEach(el => {
                el.remove();
            });

            // Ensure proper text content for navigation links
            document.querySelectorAll('.pagination .page-item:first-child .page-link').forEach(el => {
                if (!el.textContent.trim()) {
                    el.textContent = 'Previous';
                }
            });

            document.querySelectorAll('.pagination .page-item:last-child .page-link').forEach(el => {
                if (!el.textContent.trim()) {
                    el.textContent = 'Next';
                }
            });
        }

        // Run pagination fix on page load and after any dynamic content changes
        document.addEventListener('DOMContentLoaded', fixPaginationStyling);

        // Also run after any AJAX content updates
        const originalFetch = window.fetch;
        window.fetch = function(...args) {
            return originalFetch.apply(this, args).then(response => {
                if (response.ok) {
                    setTimeout(fixPaginationStyling, 100);
                }
                return response;
            });
        };

        // Toggle sidebar collapse with Sweet Alert
        function toggleSidebarCollapse() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            const navbar = document.querySelector('.navbar-custom');

            sidebar.classList.toggle('collapsed');
            if (sidebar.classList.contains('collapsed')) {
                mainContent.style.marginLeft = '0';
                navbar.style.marginLeft = '0';
                Swal.fire('Sidebar Collapsed', 'The sidebar has been collapsed.', 'info');
            } else {
                mainContent.style.marginLeft = 'var(--sidebar-width)';
                navbar.style.marginLeft = 'var(--sidebar-width)';
                Swal.fire('Sidebar Expanded', 'The sidebar has been expanded.', 'info');
            }
        }
    </script>

    @yield('scripts')
</body>

</html>
