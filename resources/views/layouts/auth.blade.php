<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BD CRM') }}</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('faviconfd.ico') }}" sizes="any">
    <link rel="shortcut icon" href="{{ asset('faviconfd.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border-radius: 20px;
            max-width: 400px;
            width: 100%;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-logo {
            display: block;
            margin-bottom: 1rem;
            text-align: center;
        }

        .auth-logo img {
            max-width: 80px;
            height: auto;
        }

        .auth-subtitle {
            color: #6c757d;
            font-size: 1rem;
            margin: 0;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #e67717;
            box-shadow: 0 0 0 0.2rem rgba(230, 119, 23, 0.25);
        }

        .btn-primary {
            background: linear-gradient(45deg, #e67717, #d4690e);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(45deg, #d4690e, #c15d0a);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(230, 119, 23, 0.4);
        }

        .form-check-input:checked {
            background-color: #e67717;
            border-color: #e67717;
        }

        .auth-link { color: #e67717; text-decoration: none; font-weight: 500; transition: color 0.3s ease; }

        .auth-link:hover {
            color: #d4690e;
            text-decoration: underline;
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .invalid-feedback {
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        .text-muted {
            font-size: 0.875rem;
        }

        /* Additional brand color enhancements */
        .form-label {
            color: #495057;
            font-weight: 600;
        }

        .text-muted {
            color: #6c757d !important;
        }

        .form-text { color: #e67717; font-size: 0.8rem; font-weight: 500; }

        /* Enforce brand primary for Bootstrap utility classes across auth */
        .bg-primary { background-color: #e67717 !important; }
        .text-bg-primary { background-color: #e67717 !important; color: #fff !important; }
        .border-primary { border-color: #e67717 !important; }
        .btn-outline-primary { color: #e67717 !important; border-color: #e67717 !important; }
        .btn-outline-primary:hover, .btn-outline-primary:focus { background-color: #e67717 !important; border-color: #e67717 !important; color: #fff !important; }

        .alert-success {
            background-color: rgba(230, 119, 23, 0.1);
            border-left: 4px solid #e67717;
            color: #d4690e;
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            border-left: 4px solid #dc3545;
            color: #721c24;
        }

        @media (max-width: 576px) {
            .auth-card {
                margin: 1rem;
                max-width: none;
            }

            .auth-logo {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="min-vh-100 d-flex align-items-center justify-content-center py-4">
        <div class="card auth-card p-4 p-md-5" style="margin: 0 1rem;">
            @yield('content')
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1060;">
        @if(session('success'))
            <x-toast type="success" title="Success!" message="{{ session('success') }}" />
        @endif

        @if(session('error'))
            <x-toast type="error" title="Error!" message="{{ session('error') }}" />
        @endif

        @if(session('warning'))
            <x-toast type="warning" title="Warning!" message="{{ session('warning') }}" />
        @endif

        @if(session('info'))
            <x-toast type="info" title="Info" message="{{ session('info') }}" />
        @endif

        @if($errors->any())
            @foreach($errors->all() as $error)
                <x-toast type="error" title="Validation Error" message="{{ $error }}" />
            @endforeach
        @endif
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Toast Notification System for Auth -->
    <script>
        // Initialize existing toasts from session
        document.addEventListener('DOMContentLoaded', function() {
            const existingToasts = document.querySelectorAll('.toast');
            existingToasts.forEach(toast => {
                const bsToast = new bootstrap.Toast(toast);
                bsToast.show();
            });
        });
    </script>
</body>
</html>
