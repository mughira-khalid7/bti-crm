@extends('admin.layouts.master')

@section('title', 'View Upwork Profile')
@section('page-title', 'Upwork Profile Details')

@section('content')
    <!-- Header Section -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1"><i class="fas fa-user-circle text-primary me-2"></i>{{ $upworkProfile->profile_name }}
                    </h4>
                    <p class="text-muted mb-0">Upwork Profile Details & Information</p>
                </div>
                <div>
                    <a href="{{ route('admin.upwork-profiles.edit', $upworkProfile) }}"
                        class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-edit me-2"></i>Edit Profile
                    </a>
                    <a href="{{ route('admin.upwork-profiles.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Profile Information -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="mb-0 text-dark"><i class="fas fa-id-card text-primary me-2"></i>Profile Information
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Profile Details Grid -->
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-card p-3 border rounded bg-light">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-user-tag text-primary fs-5 me-2"></i>
                                    <label class="form-label fw-bold mb-0">Profile Name</label>
                                </div>
                                <div class="info-value fs-6 fw-semibold text-dark">
                                    {{ $upworkProfile->profile_name }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card p-3 border rounded bg-light">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-globe text-success fs-5 me-2"></i>
                                    <label class="form-label fw-bold mb-0">Country</label>
                                </div>
                                <div class="info-value fs-6 fw-semibold text-dark d-flex align-items-center">
                                    <span class="flag-icon me-2"></span>
                                    <span>{{ $upworkProfile->country }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Credentials Section -->
                    <div class="row g-4 mt-2">
                        <div class="col-md-6">
                            <div class="info-card p-3 border rounded bg-light">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-at text-info fs-5 me-2"></i>
                                    <label class="form-label fw-bold mb-0">Username</label>
                                </div>
                                <div class="info-value d-flex align-items-center gap-2">
                                    <code id="usernameField" class="bg-white px-2 py-1 rounded border">********</code>
                                    <button id="toggleUsername" type="button" class="btn btn-sm btn-outline-secondary">Show</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card p-3 border rounded bg-light">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-key text-warning fs-5 me-2"></i>
                                    <label class="form-label fw-bold mb-0">Password</label>
                                </div>
                                <div class="info-value d-flex align-items-center gap-2">
                                    <code id="passwordField" class="bg-white px-2 py-1 rounded border">********</code>
                                    <button id="togglePassword" type="button" class="btn btn-sm btn-outline-secondary">Show</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timestamps -->
                    <div class="row g-4 mt-2">
                        <div class="col-md-6">
                            <div class="info-card p-3 border rounded bg-light">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-calendar-plus text-success fs-5 me-2"></i>
                                    <label class="form-label fw-bold mb-0">Created</label>
                                </div>
                                <div class="info-value text-muted">
                                    <i
                                        class="fas fa-clock me-1"></i>{{ $upworkProfile->created_at->format('M d, Y \a\t H:i') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card p-3 border rounded bg-light">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-calendar-check text-info fs-5 me-2"></i>
                                    <label class="form-label fw-bold mb-0">Last Updated</label>
                                </div>
                                <div class="info-value text-muted">
                                    <i
                                        class="fas fa-clock me-1"></i>{{ $upworkProfile->updated_at->format('M d, Y \a\t H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assigned BD Users -->
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-header bg-white border-bottom-0">
                    <h5 class="mb-0 text-dark"><i class="fas fa-users text-primary me-2"></i>Assigned BD Users</h5>
                </div>
                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                    @if ($upworkProfile->assignedBds->count() > 0)
                        <div class="row g-3">
                            @foreach ($upworkProfile->assignedBds as $bd)
                                <div class="col-md-6">
                                    <div class="bd-user-card border rounded p-3 h-100">
                                        <div class="d-flex align-items-center">
                                            <x-avatar :user="$bd" :size="48" class="me-3" />
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-bold">{{ $bd->name }}</h6>
                                                <p class="text-muted mb-1 small">{{ $bd->email }}</p>
                                                <span class="badge bg-primary">Business Developer</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No BD Users Assigned</h5>
                            <p class="text-muted">This profile doesn't have any Business Developer users assigned yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Information -->
        <div class="col-lg-4">
            <!-- Quick Stats -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-dark">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Profile Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="stat-item d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-hashtag text-primary fs-5 me-2"></i>
                            <span class="fw-bold">Profile ID</span>
                        </div>
                        <span class="badge bg-primary fs-6">{{ $upworkProfile->id }}</span>
                    </div>
                    <div class="stat-item d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-users text-success fs-5 me-2"></i>
                            <span class="fw-bold">Total BDs</span>
                        </div>
                        <span class="badge bg-success fs-6">{{ $upworkProfile->assignedBds->count() }}</span>
                    </div>
                    <div class="stat-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar text-info fs-5 me-2"></i>
                            <span class="fw-bold">Member Since</span>
                        </div>
                        <span class="badge bg-info fs-6">{{ $upworkProfile->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Security Information -->
            <div class="card shadow-sm border-0 mt-3">
                <div class="card-header bg-warning">
                    <h6 class="mb-0 text-dark"><i class="fas fa-shield-alt me-2"></i>Security Information</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning border-0">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-lock text-warning fs-5 me-2 mt-1"></i>
                            <div>
                                <strong class="d-block">Encrypted Credentials</strong>
                                <small>Username and password are encrypted using Laravel's built-in encryption and cannot be
                                    decrypted for security reasons.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Timeline -->
            <div class="card shadow-sm border-0 mt-3">
                <div class="card-header bg-info text-dark">
                    <h6 class="mb-0"><i class="fas fa-history me-2"></i>Recent Activity</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item d-flex mb-3">
                            <div class="timeline-marker bg-success me-3 mt-1">
                                <i class="fas fa-plus text-white"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Profile Created</h6>
                                <small class="text-muted">{{ $upworkProfile->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        @if ($upworkProfile->updated_at != $upworkProfile->created_at)
                            <div class="timeline-item d-flex">
                                <div class="timeline-marker bg-info me-3 mt-1">
                                    <i class="fas fa-edit text-white"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Profile Updated</h6>
                                    <small class="text-muted">{{ $upworkProfile->updated_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .info-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .bd-user-card {
            transition: all 0.2s ease;
        }

        .bd-user-card:hover {
            background-color: #f8f9fa;
            transform: translateY(-1px);
        }

        .stat-item {
            padding: 8px 0;
        }

        .timeline-item {
            position: relative;
        }

        .timeline-marker {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .flag-icon {
            display: inline-block;
            width: 20px;
            height: 15px;
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
            border-radius: 2px;
            margin-right: 8px;
        }
    </style>
    @section('scripts')
    <script>
        (function(){
            const usernameActual = @json($upworkProfile->username);
            const passwordActual = @json($upworkProfile->password);

            const usernameField = document.getElementById('usernameField');
            const passwordField = document.getElementById('passwordField');
            const toggleUsername = document.getElementById('toggleUsername');
            const togglePassword = document.getElementById('togglePassword');

            toggleUsername?.addEventListener('click', function(){
                const isHidden = usernameField.textContent === '********';
                usernameField.textContent = isHidden ? usernameActual : '********';
                this.textContent = isHidden ? 'Hide' : 'Show';
            });

            togglePassword?.addEventListener('click', function(){
                const isHidden = passwordField.textContent === '********';
                passwordField.textContent = isHidden ? passwordActual : '********';
                this.textContent = isHidden ? 'Hide' : 'Show';
            });
        })();
    </script>
    @endsection
@endsection
