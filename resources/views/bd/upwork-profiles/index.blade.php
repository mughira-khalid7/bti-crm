@extends('bd.layouts.master')

@section('title', 'My Upwork Profiles')
@section('page-title', 'My Upwork Profiles')

@section('content')
    <!-- Header Section -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1"><i class="fas fa-user-circle text-primary me-2"></i>My Upwork Profiles</h4>
                    <p class="text-muted mb-0">Manage your assigned Upwork profiles and credentials</p>
                </div>
                <div class="badge bg-info fs-6 px-3 py-2">
                        <i class="fas fa-info-circle me-1"></i>{{ $profiles->count() }}
                    Profile{{ $profiles->count() !== 1 ? 's' : '' }}
                </div>
            </div>
        </div>
    </div>

    @if ($profiles->count() > 0)
        <div class="row g-4">
            @foreach ($profiles as $profile)
                <div class="col-xl-4 col-lg-6">
                    <div class="profile-card card h-100 shadow-sm border-0 hover-lift">
                        <!-- Card Header -->
                        <div class="card-header bg-gradient-primary text-white position-relative">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="profile-avatar me-3">
                                        <i class="fas fa-user-circle fa-2x"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $profile->profile_name }}</h6>
                                        <small class="opacity-75">Upwork Profile</small>
                                    </div>
                                </div>
                                <div class="country-badge">
                                    @php
                                        $countryFlags = [
                                            'Afghanistan' => '🇦🇫',
                                            'Albania' => '🇦🇱',
                                            'Algeria' => '🇩🇿',
                                            'Argentina' => '🇦🇷',
                                            'Australia' => '🇦🇺',
                                            'Austria' => '🇦🇹',
                                            'Bangladesh' => '🇧🇩',
                                            'Belgium' => '🇧🇪',
                                            'Brazil' => '🇧🇷',
                                            'Canada' => '🇨🇦',
                                            'Chile' => '🇨🇱',
                                            'China' => '🇨🇳',
                                            'Colombia' => '🇨🇴',
                                            'Denmark' => '🇩🇰',
                                            'Egypt' => '🇪🇬',
                                            'France' => '🇫🇷',
                                            'Germany' => '🇩🇪',
                                            'Greece' => '🇬🇷',
                                            'India' => '🇮🇳',
                                            'Indonesia' => '🇮🇩',
                                            'Iran' => '🇮🇷',
                                            'Iraq' => '🇮🇶',
                                            'Ireland' => '🇮🇪',
                                            'Israel' => '🇮🇱',
                                            'Italy' => '🇮🇹',
                                            'Japan' => '🇯🇵',
                                            'Jordan' => '🇯🇴',
                                            'Kenya' => '🇰🇪',
                                            'Malaysia' => '🇲🇾',
                                            'Mexico' => '🇲🇽',
                                            'Morocco' => '🇲🇦',
                                            'Netherlands' => '🇳🇱',
                                            'New Zealand' => '🇳🇿',
                                            'Nigeria' => '🇳🇬',
                                            'Norway' => '🇳🇴',
                                            'Pakistan' => '🇵🇰',
                                            'Peru' => '🇵🇪',
                                            'Philippines' => '🇵🇭',
                                            'Poland' => '🇵🇱',
                                            'Portugal' => '🇵🇹',
                                            'Romania' => '🇷🇴',
                                            'Russia' => '🇷🇺',
                                            'Saudi Arabia' => '🇸🇦',
                                            'Singapore' => '🇸🇬',
                                            'South Africa' => '🇿🇦',
                                            'South Korea' => '🇰🇷',
                                            'Spain' => '🇪🇸',
                                            'Sweden' => '🇸🇪',
                                            'Switzerland' => '🇨🇭',
                                            'Thailand' => '🇹🇭',
                                            'Turkey' => '🇹🇷',
                                            'Ukraine' => '🇺🇦',
                                            'United Arab Emirates' => '🇦🇪',
                                            'United Kingdom' => '🇬🇧',
                                            'United States' => '🇺🇸',
                                            'Venezuela' => '🇻🇪',
                                            'Vietnam' => '🇻🇳',
                                        ];
                                        $flag = $countryFlags[$profile->country] ?? '🏳️';
                                    @endphp
                                    <span class="flag-emoji">{{ $flag }}</span>
                                    <small class="fw-medium">{{ $profile->country }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body">
                            <!-- Username Field -->
                            <div class="credential-field mb-4">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-at text-primary me-2"></i>
                                    <label class="form-label fw-bold mb-0">Username</label>
                                </div>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm bg-light border-end-0"
                                        value="{{ $profile->username }}" readonly>
                                    <button class="btn btn-outline-secondary btn-sm copy-btn border-start-0" type="button"
                                        data-clipboard="{{ $profile->username }}" title="Copy username">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Password Field -->
                            <div class="credential-field mb-4">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-key text-warning me-2"></i>
                                    <label class="form-label fw-bold mb-0">Password</label>
                                </div>
                                <div class="input-group">
                                    <input type="password"
                                        class="form-control form-control-sm bg-light border-end-0 password-field"
                                        value="{{ $profile->password }}" readonly>
                                    <button
                                        class="btn btn-outline-secondary btn-sm toggle-password border-start-0 border-end-0"
                                        type="button" title="Show/Hide password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm copy-btn border-start-0" type="button"
                                        data-clipboard="{{ $profile->password }}" title="Copy password">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Profile Stats -->
                            <div class="profile-stats row text-center mb-3 g-2">
                                <div class="col-6">
                                    <div class="credential-field stat-item border rounded-3 p-2">
                                        <i class="fas fa-calendar-plus text-success mb-1"></i>
                                        <small class="d-block text-muted">Created</small>
                                        <span class="fw-medium small">{{ $profile->created_at->format('M d, Y') }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="credential-field stat-item border rounded-3 p-2">
                                        <i class="fas fa-clock text-info mb-1"></i>
                                        <small class="d-block text-muted">Updated</small>
                                        <span class="fw-medium small">{{ $profile->updated_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Security Badge -->
                            <div class="security-badge text-center">
                                <span class="badge bg-success-subtle text-success border border-success-subtle p-3 w-100">
                                    <i class="fas fa-shield-alt me-2"></i>Secure & Encrypted
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if ($profiles->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $profiles->links() }}
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="empty-state text-center py-5">
            <div class="empty-icon mb-4">
                <i class="fas fa-user-circle fa-5x text-muted"></i>
            </div>
            <h4 class="text-muted mb-3">No Upwork Profiles Assigned</h4>
            <p class="text-muted mb-4">You don't have any Upwork profiles assigned to you yet.</p>
            <div class="alert alert-info d-inline-block">
                <i class="fas fa-info-circle me-2"></i>
                Contact your administrator to get profiles assigned to your account.
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Copy to clipboard functionality
            const copyButtons = document.querySelectorAll('.copy-btn');
            copyButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const textToCopy = this.getAttribute('data-clipboard');
                    navigator.clipboard.writeText(textToCopy).then(() => {
                        // Show success feedback
                        const originalIcon = this.innerHTML;
                        this.innerHTML = '<i class="fas fa-check text-success"></i>';
                        setTimeout(() => {
                            this.innerHTML = originalIcon;
                        }, 1000);
                    }).catch(err => {
                        console.error('Failed to copy: ', err);
                    });
                });
            });

            // Toggle password visibility
            const toggleButtons = document.querySelectorAll('.toggle-password');
            toggleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const passwordField = this.closest('.input-group').querySelector(
                        '.password-field');
                    const icon = this.querySelector('i');

                    if (passwordField.type === 'password') {
                        passwordField.type = 'text';
                        icon.className = 'fas fa-eye-slash';
                        this.title = 'Hide password';
                    } else {
                        passwordField.type = 'password';
                        icon.className = 'fas fa-eye';
                        this.title = 'Show password';
                    }
                });
            });
        });
    </script>

    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .hover-lift:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .profile-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
        }

        .profile-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
        }

        .profile-avatar {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .country-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 4px 8px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
        }

        .flag-emoji {
            font-size: 1.2em;
            margin-right: 4px;
        }

        .credential-field {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 16px;
            border: 1px solid #e9ecef;
        }

        .credential-field:hover {
            background: #fff;
            border-color: #667eea;
        }

        .copy-btn {
            border-left: none;
            background: transparent;
        }

        .copy-btn:hover {
            background-color: #e9ecef;
            color: #667eea;
        }

        .password-field {
            border-right: none;
            background: transparent;
        }

        .toggle-password {
            border-left: none;
            border-right: none;
            background: transparent;
        }

        .toggle-password:hover {
            background-color: #e9ecef;
            color: #667eea;
        }

        .stat-item {
            padding: 8px;
        }

        .security-badge {
            margin-top: 16px;
        }

        .empty-state {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 16px;
            margin: 2rem 0;
        }

        .empty-icon {
            opacity: 0.6;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .profile-card {
                margin-bottom: 1rem;
            }

            .country-badge {
                font-size: 0.75rem;
            }
        }
    </style>
@endsection
