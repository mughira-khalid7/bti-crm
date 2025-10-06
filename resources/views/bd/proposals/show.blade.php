@extends('bd.layouts.master')

@section('title', 'Proposal Details')
@section('page-title', 'Proposal Details')

@section('content')
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-file-alt me-2 text-primary"></i>Proposal Details
                </h4>
                <div>
                    <a href="{{ route('bd.proposals.edit', $proposal) }}" class="btn btn-outline-primary btn-sm me-2">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                    <a href="{{ route('bd.proposals.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column (8 columns) - Proposal Details -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-file-text me-2 text-primary"></i>Proposal Details</h5>
                </div>
                <div class="card-body">
                    <!-- Title -->
                    <div class="mb-4">
                        <h3 class="mb-2">{{ $proposal->title }}</h3>
                        <div class="text-muted">
                            <i class="far fa-calendar-alt me-2"></i>
                            Submitted on {{ \Carbon\Carbon::parse($proposal->submitted_at)->format('M d, Y \a\t h:i A') }}
                        </div>
                    </div>

                    <hr>

                    <!-- Job Description -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-briefcase me-2"></i>Job Description
                        </h6>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($proposal->job_description)) !!}
                        </div>
                    </div>

                    <!-- Notes -->
                    @if ($proposal->notes)
                        <div class="mb-0">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-sticky-note me-2"></i>Notes
                            </h6>
                            <div class="bg-light p-3 rounded">
                                {!! nl2br(e($proposal->notes)) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column (4 columns) -->
        <div class="col-lg-4">
            <div class="sidebar-scroll">
            <!-- Client Information Card -->
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white border-bottom py-2">
                    <h6 class="mb-0 fw-bold" style="font-size: 0.875rem;"><i class="fas fa-address-card me-2 text-primary"></i>Client Information</h6>
                </div>
                <div class="card-body p-3">
                    <div class="mb-2">
                        <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.75rem;">Client Name</label>
                        <div class="d-flex align-items-center p-2 bg-light rounded">
                            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                <i class="fas fa-user text-primary" style="font-size: 0.875rem;"></i>
                            </div>
                            <div class="fw-bold" style="font-size: 0.875rem;">John Smith</div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.75rem;">Location</label>
                        <div class="p-2 bg-light rounded" style="font-size: 0.875rem;">
                            <i class="fas fa-map-marker-alt me-2 text-danger"></i>
                            <span>United States</span>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.75rem;">Total Spending</label>
                        <div class="p-2 bg-light rounded" style="font-size: 0.875rem;">
                            <i class="fas fa-dollar-sign me-2 text-success"></i>
                            <span class="fw-bold text-success">$50,000+</span>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.75rem;">Reviews</label>
                        <div class="p-2 bg-light rounded" style="font-size: 0.875rem;">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <span class="ms-1 fw-bold">5.0</span>
                            <span class="text-muted small">(25 reviews)</span>
                        </div>
                    </div>

                    @if ($proposal->upworkProfile)
                        <div class="mb-0">
                            <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.75rem;">Upwork Profile</label>
                            <div class="d-flex align-items-center p-2 bg-light rounded">
                                <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center me-2"
                                    style="width: 32px; height: 32px;">
                                    <i class="fas fa-user-circle text-success" style="font-size: 0.875rem;"></i>
                                </div>
                                <div style="line-height: 1.3;">
                                    <div class="fw-semibold" style="font-size: 0.813rem;">{{ $proposal->upworkProfile->profile_name }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $proposal->upworkProfile->country }} - {{ $proposal->upworkProfile->username }}</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Proposal Information Card -->
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white border-bottom py-2">
                    <h6 class="mb-0 fw-bold" style="font-size: 0.875rem;"><i class="fas fa-info-circle me-2 text-primary"></i>Proposal Information</h6>
                </div>
                <div class="card-body p-3">
                    @php
                        $statusColors = [
                            'copied' => 'warning',
                            'interviewing' => 'info',
                            'submitted' => 'secondary',
                            'deleted' => 'danger',
                            'viewed' => 'primary',
                            'meeting_scheduled' => 'success',
                            'phone_shared' => 'dark',
                        ];
                        $statusLabels = [
                            'meeting_scheduled' => 'Meeting Scheduled',
                            'phone_shared' => 'Phone Shared',
                        ];
                        $badgeColor = $statusColors[$proposal->status] ?? 'secondary';
                        $statusLabel = $statusLabels[$proposal->status] ?? ucfirst($proposal->status);
                    @endphp

                    <!-- First Row: Status, Connects, Submitted, Created -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="text-center p-2 bg-light rounded">
                                <div class="mb-1">
                                    <span class="badge bg-{{ $badgeColor }}" style="font-size: 0.75rem;">{{ $statusLabel }}</span>
                                </div>
                                <label class="text-muted mb-0" style="font-size: 0.7rem;">Status</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 bg-light rounded">
                                <div class="fw-bold text-primary mb-1 {{ isset($recentChanges['connects_used']) ? 'text-warning' : '' }}" style="font-size: 1.25rem; line-height: 1;">
                                    {{ $proposal->connects_used }}
                                </div>
                                <label class="text-muted mb-0" style="font-size: 0.7rem;">Connects</label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="text-center p-2 bg-light rounded">
                                <div class="fw-semibold mb-1" style="font-size: 0.75rem;">
                                    {{ \Carbon\Carbon::parse($proposal->submitted_at)->format('M d, Y') }}
                                </div>
                                <label class="text-muted mb-0" style="font-size: 0.7rem;">Submitted</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 bg-light rounded">
                                <div class="fw-semibold mb-1" style="font-size: 0.75rem;">
                                    {{ $proposal->created_at->format('M d, Y') }}
                                </div>
                                <label class="text-muted mb-0" style="font-size: 0.7rem;">Created</label>
                            </div>
                        </div>
                    </div>

                    <!-- Second Row: Full-width Job URL button -->
                    <div class="mb-0">
                        <a href="{{ $proposal->url }}" target="_blank" class="btn btn-outline-primary w-100 py-2">
                            <i class="fas fa-external-link-alt me-1"></i> Open Upwork Job
                        </a>
                    </div>

                    @if($proposal->status !== 'interviewing')
                    <hr class="my-2">
                    <div>
                        <button class="btn btn-success w-100 py-2" onclick="confirmMoveToInterviewing('{{ route('bd.proposals.moveToInterviewing', $proposal) }}', '{{ $proposal->title }}')">
                            <i class="fas fa-arrow-right me-1"></i> Move to Interviewing
                        </button>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Meeting Information Card (visible to assigned BD/participants) -->
            @if ($proposal->meeting && $proposal->meeting->count() > 0)
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white border-bottom py-2">
                    <h6 class="mb-0 fw-bold" style="font-size: 0.875rem;"><i class="fas fa-calendar-alt me-2 text-primary"></i>Scheduled Meeting</h6>
                </div>
                <div class="card-body p-3">
                    @php $meeting = $proposal->meeting->first(); @endphp

                    <div class="mb-2">
                        <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.75rem;">Meeting Title</label>
                        <div class="p-2 bg-light rounded" style="font-size: 0.875rem;">
                            <i class="fas fa-video me-2 text-primary"></i>
                            <span class="fw-bold">{{ $meeting->title }}</span>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.75rem;">Scheduled Date & Time</label>
                        <div class="p-2 bg-light rounded" style="font-size: 0.875rem;">
                            <i class="fas fa-clock me-2 text-success"></i>
                            <span class="fw-bold">{{ $meeting->scheduled_at->format('M d, Y \a\t h:i A') }}</span>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.75rem;">Assigned BD</label>
                        <div class="d-flex align-items-center p-2 bg-light rounded">
                            <x-avatar :user="$meeting->bdUser" :size="32" class="me-2" />
                            <div style="line-height: 1.3;">
                                <div class="fw-semibold" style="font-size: 0.813rem;">{{ $meeting->bdUser->name }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">{{ $meeting->bdUser->email }}</div>
                            </div>
                        </div>
                    </div>

                    @if ($meeting->meeting_link)
                    <div class="mb-2">
                        <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.75rem;">Meeting Link</label>
                        <div class="p-2 bg-light rounded" style="font-size: 0.875rem;">
                            <a href="{{ $meeting->meeting_link }}" target="_blank" class="text-decoration-none"><i class="fas fa-external-link-alt me-2 text-primary"></i>Join Meeting</a>
                        </div>
                    </div>
                    @endif

                    @if ($meeting->location)
                    <div class="mb-2">
                        <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.75rem;">Location</label>
                        <div class="p-2 bg-light rounded" style="font-size: 0.875rem;">
                            <i class="fas fa-map-marker-alt me-2 text-danger"></i>
                            <span>{{ $meeting->location }}</span>
                        </div>
                    </div>
                    @endif

                    <div class="mb-0">
                        <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.75rem;">Meeting Type</label>
                        <div class="p-2 bg-light rounded" style="font-size: 0.875rem;">
                            @php
                                $typeIcons = ['video_call' => 'fas fa-video','phone_call' => 'fas fa-phone','in_person' => 'fas fa-handshake'];
                                $typeLabels = ['video_call' => 'Video Call','phone_call' => 'Phone Call','in_person' => 'In-Person'];
                            @endphp
                            <i class="{{ $typeIcons[$meeting->meeting_type] ?? 'fas fa-calendar' }} me-2 text-info"></i>
                            <span>{{ $typeLabels[$meeting->meeting_type] ?? ucfirst($meeting->meeting_type) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Quick Actions Card -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-2">
                    <h6 class="mb-0 fw-bold" style="font-size: 0.875rem;"><i class="fas fa-bolt me-2 text-primary"></i>Quick Actions</h6>
                </div>
                <div class="card-body p-3">
                    @if ($proposal->status !== 'interviewing')
                        <button class="btn btn-success w-100 mb-2 py-2"
                            onclick="confirmMoveToInterviewing('{{ route('bd.proposals.moveToInterviewing', $proposal) }}', '{{ $proposal->title }}')">
                            <i class="fas fa-arrow-right me-1"></i> Move to Interviewing
                        </button>
                    @else
                        <div class="alert alert-info mb-2 py-2">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>Already in interviewing status</small>
                        </div>
                    @endif

                    <a href="{{ route('bd.proposals.edit', $proposal) }}" class="btn btn-outline-primary w-100 mb-2 py-2">
                        <i class="fas fa-edit me-1"></i> Edit Proposal
                    </a>

                    <form method="POST" action="{{ route('bd.proposals.destroy', $proposal) }}" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-outline-danger w-100 py-2"
                            onclick="confirmDelete(this.closest('form'))">
                            <i class="fas fa-trash me-1"></i> Delete Proposal
                        </button>
                    </form>
                </div>
            </div>
            </div>
        </div>
    </div>
    <style>
        /* Make right column flow with page (remove internal scroll) */
        .sidebar-scroll { max-height: none; overflow: visible; padding-right: 0; }
    </style>
@endsection

@section('scripts')
    <script>
        function confirmMoveToInterviewing(url, title) {
            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to move "${title}" to interviewing status?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, move it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Moved!', 'Proposal moved to interviewing successfully!', 'success');
                                setTimeout(() => window.location.reload(), 1000);
                            } else {
                                Swal.fire('Error!', data.message || 'Failed to move proposal.', 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error!', 'An unexpected error occurred.', 'error');
                        });
                }
            });
        }

        function confirmDelete(form) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to delete this proposal? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
@endsection

