@extends('admin.layouts.master')

@section('title', 'BD Notes - ' . $user->name . ' - Admin Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 print-area">
                    <div class="card-header p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <x-avatar :user="$user" :size="40" class="me-3" />
                                <div class="d-flex flex-column">
                                    <h4 class="mb-1 text-dark fw-bold">{{ $user->name }}'s Notes</h4>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="window.print()">
                                    <i class="fas fa-print me-1"></i>
                                    Print
                                </button>
                                <a href="{{ route('admin.notes.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-arrow-left me-1"></i>
                                    Back to All Notes
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        @if ($note)
                            <div class="notes-content">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <h6 class="text-muted mb-1">Notes Content <span class="text-danger fw-bold">*</span>
                                        </h6>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            Last updated: {{ $note->updated_at->format('M d, Y \a\t h:i A') }}
                                            ({{ $note->updated_at->diffForHumans() }})
                                        </small>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-edit me-1"></i>
                                            {{ Str::wordCount(strip_tags($note->content)) }} words
                                        </span>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-file-alt me-1"></i>
                                            {{ $user->proposals->count() }} proposals
                                        </span>
                                    </div>
                                </div>

                                <div class="notes-display border border-2 border-primary rounded-1 p-3"
                                    style="max-height: 350px;overflow-y: auto;">
                                    {!! $note->content !!}
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="fas fa-sticky-note text-muted" style="font-size: 4rem;"></i>
                                </div>
                                <h5 class="text-muted mb-2">No Notes Found</h5>
                                <p class="text-muted mb-4">{{ $user->name }} hasn't created any notes yet.</p>
                                <div class="alert alert-info border-0 bg-light d-inline-block">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-info-circle text-info me-2"></i>
                                        <div class="text-start">
                                            <strong>Note:</strong> This BD can create notes in their "Resource (Notes)" tab.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <hr>
                        <!-- BD Statistics -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title text-dark mb-3">
                                            <i class="fas fa-chart-line text-primary me-2"></i>
                                            BD Performance
                                        </h6>
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <div class="border-end">
                                                    <h4 class="text-primary mb-1">{{ $user->proposals->count() }}</h4>
                                                    <small class="text-muted">Total Proposals</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <h4 class="text-success mb-1">
                                                    {{ $user->proposals->where('status', 'interviewing')->count() }}
                                                </h4>
                                                <small class="text-muted">Interviewing</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title text-dark mb-3">
                                            <i class="fas fa-user-circle text-primary me-2"></i>
                                            BD Profile
                                        </h6>
                                        <div class="d-flex align-items-center mb-2">
                                            <strong class="me-2">Status:</strong>
                                            <span
                                                class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($user->status) }}
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <strong class="me-2">Joined:</strong>
                                            <span class="text-muted">{{ $user->created_at->format('M d, Y') }}</span>
                                        </div>
                                        @if ($user->goal)
                                            <div class="d-flex align-items-center">
                                                <strong class="me-2">Goal:</strong>
                                                <span class="text-muted">{{ $user->goal->target_connects }}
                                                    connects/month</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .notes-content {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 2rem;
            border: 1px solid #e9ecef;
        }

        .notes-display {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            border: 2px solid #cfd4da;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.7;
            color: #495057;
        }

        .notes-display h1,
        .notes-display h2,
        .notes-display h3,
        .notes-display h4,
        .notes-display h5,
        .notes-display h6 {
            color: #212529;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .notes-display h1:first-child,
        .notes-display h2:first-child,
        .notes-display h3:first-child {
            margin-top: 0;
        }

        .notes-display p {
            margin-bottom: 1rem;
        }

        .notes-display ul,
        .notes-display ol {
            margin-bottom: 1rem;
            padding-left: 2rem;
        }

        .notes-display li {
            margin-bottom: 0.5rem;
        }

        .notes-display blockquote {
            border-left: 4px solid #007bff;
            padding-left: 1rem;
            margin: 1.5rem 0;
            font-style: italic;
            color: #6c757d;
        }

        .notes-display code {
            background-color: #f8f9fa;
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 0.875em;
        }

        .notes-display pre {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            overflow-x: auto;
            margin: 1rem 0;
        }

        .notes-display a {
            color: #007bff;
            text-decoration: none;
        }

        .notes-display a:hover {
            text-decoration: underline;
        }

        .notes-display img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 1rem 0;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }

        @media print {

            /* Hide global chrome when printing */
            body *:not(.print-area):not(.print-area *),
            .sidebar,
            .navbar,
            .navbar-custom,
            .main-content>.content>*:not(.print-area) {
                display: none !important;
                visibility: hidden !important;
            }

            .print-area {
                display: block !important;
                visibility: visible !important;
                position: static !important;
                width: 100% !important;
            }

            @page {
                size: A4;
                margin: 16mm;
            }

            html,
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color: #000 !important;
            }

            .card-header .btn,
            .card-header .dropdown {
                display: none !important;
            }

            .notes-content {
                background: white !important;
                border: none !important;
            }

            .notes-display {
                box-shadow: none !important;
                border: 2px solid #000 !important;
                padding: 18mm !important;
                border-radius: 6px !important;
            }

            /* Prevent awkward page breaks inside headline/list areas */
            .notes-display h1,
            .notes-display h2,
            .notes-display h3,
            .notes-display h4,
            .notes-display h5,
            .notes-display h6,
            .notes-display p,
            .notes-display ul,
            .notes-display ol,
            .notes-display blockquote,
            .notes-display pre {
                break-inside: avoid;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add smooth animation to notes display
            const notesDisplay = document.querySelector('.notes-display');
            if (notesDisplay) {
                notesDisplay.style.opacity = '0';
                notesDisplay.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    notesDisplay.style.transition = 'all 0.6s ease';
                    notesDisplay.style.opacity = '1';
                    notesDisplay.style.transform = 'translateY(0)';
                }, 300);
            }
        });
    </script>
@endpush
