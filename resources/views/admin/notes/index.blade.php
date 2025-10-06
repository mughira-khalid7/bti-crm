@extends('admin.layouts.master')

@section('title', 'BD Notes - Admin Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-0 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1 text-dark fw-bold">
                                    <i class="fas fa-sticky-note text-primary me-2"></i>
                                    BD Resource Notes
                                </h4>
                                <p class="text-muted mb-0">Review notes and resources from all Business Developers</p>
                            </div>
                            <div class="d-flex gap-2 justify-content-end">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="refreshNotes()">
                                    <i class="fas fa-sync-alt me-1"></i>
                                    Refresh
                                </button>
                            </div>
                        </div>
                        <hr>
                    </div>
                    <div class="card-body">
                        <!-- Admin's Own Notes Card -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow admin-notes-card ">
                                    <div class="card-header bg-gradient-primary border-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5 class="mb-1 fw-bold">
                                                    <i class="fas fa-user-cog me-2"></i>
                                                    My Admin Notes
                                                </h5>
                                                <p class="text-dark-50 mb-0">Your personal notes and resources</p>
                                            </div>
                                            <div class="d-flex gap-2">
                                                @if ($adminNote)
                                                    <a href="{{ route('admin.notes.my') }}" class="btn btn-light btn-sm">
                                                        <i class="fas fa-edit me-1"></i>
                                                        Edit Notes
                                                    </a>
                                                @else
                                                    <a href="{{ route('admin.notes.my') }}" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-plus me-1"></i>
                                                        Create Notes
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @if ($adminNote)
                                            <div class="d-flex align-items-start">
                                                <x-avatar :user="$admin" :size="50" class="me-3" />
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-2 fw-bold text-dark">{{ $admin->name }}</h6>
                                                    <div class="notes-preview">
                                                        {!! Str::limit(strip_tags($adminNote->content), 200) !!}
                                                    </div>
                                                    @if (strlen(strip_tags($adminNote->content)) > 200)
                                                        <div class="mt-3">
                                                            <a href="{{ route('admin.notes.my') }}"
                                                                class="btn btn-outline-primary btn-sm">
                                                                <i class="fas fa-arrow-right me-1"></i>
                                                                Read More
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="mt-3 pt-3 border-top">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        <i class="fas fa-edit me-1"></i>
                                                        Last updated:
                                                        {{ $adminNote->updated_at->format('M d, Y \a\t h:i A') }}
                                                    </small>
                                                    <span class="badge bg-primary">
                                                        Admin Notes
                                                    </span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-center py-4">
                                                <div class="mb-3">
                                                    <i class="fas fa-sticky-note text-muted" style="font-size: 3rem;"></i>
                                                </div>
                                                <h6 class="text-muted mb-2">No Notes Created Yet</h6>
                                                <p class="text-muted mb-3">Start creating your personal notes and resources.
                                                </p>
                                                <a href="{{ route('admin.notes.my') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus me-1"></i>
                                                    Create Your First Note
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <!-- BD Resource Notes Section -->
                        <div class="mb-3">
                            <h5 class="text-dark fw-bold mb-3">
                                <i class="fas fa-users text-secondary me-2"></i>
                                Business Developer Notes
                            </h5>
                        </div>

                        @if ($bdNotes->count() > 0)
                            <div class="row">
                                @foreach ($bdNotes as $note)
                                    <div class="col-lg-6 col-xl-4 mb-4">
                                        <div class="card h-100 border-0 shadow notes-card">
                                            <div class="card-header border-0">
                                                <div class="d-flex align-items-center">
                                                    <x-avatar :user="$note->user" :size="40" class="me-3" />
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-0 fw-bold text-dark">{{ $note->user->name }}</h6>
                                                        <small class="text-muted">
                                                            <i class="fas fa-clock me-1"></i>
                                                            {{ $note->updated_at->diffForHumans() }}
                                                        </small>
                                                    </div>
                                                    <a class="btn btn-outline-primary btn-sm"
                                                        href="{{ route('admin.notes.show', $note->user) }}">
                                                        <i class="fas fa-eye me-1"></i>View Full Notes
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="notes-preview">
                                                    {!! Str::limit(strip_tags($note->content), 200) !!}
                                                </div>
                                                @if (strlen(strip_tags($note->content)) > 200)
                                                    <div class="mt-3">
                                                        <a href="{{ route('admin.notes.show', $note->user) }}"
                                                            class="btn btn-outline-primary btn-sm">
                                                            <i class="fas fa-arrow-right me-1"></i>
                                                            Read More
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="card-footer bg-transparent border-0 pt-0">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        <i class="fas fa-edit me-1"></i>
                                                        Last updated: {{ $note->updated_at->format('M d, Y \a\t h:i A') }}
                                                    </small>
                                                    <span class="badge bg-light text-dark">
                                                        {{ $note->user->proposals->count() }} proposals
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="fas fa-sticky-note text-muted" style="font-size: 4rem;"></i>
                                </div>
                                <h5 class="text-muted mb-2">No Notes Available</h5>
                                <p class="text-muted mb-4">Business Developers haven't created any notes yet.</p>
                                <div class="alert alert-info border-0 bg-light d-inline-block">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-info-circle text-info me-2"></i>
                                        <div class="text-start">
                                            <strong>Note:</strong> BDs can create personal notes in their "Resource (Notes)"
                                            tab.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .admin-notes-card {
            transition: all 0.3s ease;
            border-left: 4px solid #6f42c1 !important;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        }

        /* Compact spacing for admin card */
        .admin-notes-card .card-header { padding: .75rem 1rem !important; }
        .admin-notes-card .card-body { padding: 1rem !important; }

        .admin-notes-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(111, 66, 193, 0.15) !important;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%) !important;
        }

        .notes-card {
            transition: all 0.3s ease;
            border-left: 4px solid #007bff !important;
        }

        .notes-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
        }

        .notes-preview {
            font-size: 14px;
            line-height: 1.6;
            color: #495057;
            max-height: 120px;
            overflow: hidden;
            position: relative;
            border: 1px dashed #ced4da;
            border-radius: 6px;
            padding: .75rem;
        }

        .notes-preview::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 20px;
            background: linear-gradient(transparent, white);
            pointer-events: none;
        }

        .card-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }

        .dropdown-toggle::after {
            display: none;
        }

        .btn-link {
            color: #6c757d;
            text-decoration: none;
            border: none;
            background: none;
            padding: 0.25rem 0.5rem;
        }

        .btn-link:hover {
            color: #495057;
            background: rgba(0, 0, 0, 0.05);
            border-radius: 4px;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            padding: 0.5rem 0;
        }

        .dropdown-item {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #007bff;
        }

        .dropdown-item i {
            width: 16px;
            text-align: center;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function refreshNotes() {
            const btn = event.target;
            const originalContent = btn.innerHTML;

            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Refreshing...';
            btn.disabled = true;

            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }

        // Add animation to cards on load
        document.addEventListener('DOMContentLoaded', function() {
            // Animate admin notes card first
            const adminCard = document.querySelector('.admin-notes-card');
            if (adminCard) {
                adminCard.style.opacity = '0';
                adminCard.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    adminCard.style.transition = 'all 0.5s ease';
                    adminCard.style.opacity = '1';
                    adminCard.style.transform = 'translateY(0)';
                }, 100);
            }

            // Animate BD notes cards
            const cards = document.querySelectorAll('.notes-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 300 + (index * 100)); // Start after admin card
            });
        });
    </script>
@endpush
