@extends('admin.layouts.master')

@section('title', 'Proposal Details')
@section('page-title', 'Proposal Details')

@section('content')
    @if($proposal->deleted_at)
    <div class="alert alert-danger border-0 rounded-3 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
            <div class="flex-grow-1">
                <h5 class="alert-heading mb-1">
                    <i class="fas fa-trash-alt me-2"></i>This Proposal Has Been Deleted
                </h5>
                <p class="mb-0">
                    This proposal was deleted on <strong>{{ $proposal->deleted_at->format('F j, Y \a\t h:i A') }}</strong>
                    @php
                        $deleteLog = \App\Models\ActionLog::where('model_type', \App\Models\Proposal::class)
                            ->where('model_id', $proposal->id)
                            ->where('action', 'proposal_deleted')
                            ->with('user')
                            ->latest()
                            ->first();
                    @endphp
                    @if($deleteLog && $deleteLog->user)
                        by <strong>{{ $deleteLog->user->name }}</strong>
                    @endif
                </p>
            </div>
        </div>
    </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-file-alt me-2 text-primary"></i>Proposal Details
                    @if($proposal->deleted_at)
                        <span class="badge bg-danger ms-2">Deleted</span>
                    @endif
                </h4>
                <div>
                    <button class="btn btn-outline-primary btn-sm me-2" onclick="toggleVersionHistory()">
                        <i class="fas fa-history me-1"></i> Version History
                    </button>
                    <a href="{{ route('admin.proposals.index') }}" class="btn btn-outline-secondary btn-sm">
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
                        <h3 class="mb-2 {{ isset($recentChanges['title']) ? 'bg-warning bg-opacity-25 p-2 rounded' : '' }}">
                            {{ $proposal->title }}
                            @if(isset($recentChanges['title']))
                                <span class="badge bg-warning text-dark ms-2" style="font-size: 0.7rem;">
                                    <i class="fas fa-edit"></i> Recently edited
                                </span>
                            @endif
                        </h3>
                        <div class="d-flex align-items-center text-muted">
                            @if($proposal->user)
                                <x-avatar :user="$proposal->user" :size="32" class="me-2" />
                                <span>Submitted by <strong>{{ $proposal->user->name }}</strong>
                                    @if($proposal->user->trashed())
                                        <span class="badge bg-danger ms-1" style="font-size: 0.65rem;">Deactivated</span>
                                    @endif
                                    on {{ \Carbon\Carbon::parse($proposal->submitted_at)->format('M d, Y \a\t h:i A') }}
                                </span>
                            @else
                                <span>Submitted by <strong class="text-muted">Deleted User</strong> on {{ \Carbon\Carbon::parse($proposal->submitted_at)->format('M d, Y \a\t h:i A') }}</span>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <!-- Job Description -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-briefcase me-2"></i>Job Description
                        </h6>
                        <div class="bg-light p-3 rounded {{ isset($recentChanges['job_description']) ? 'border border-warning border-2' : '' }}">
                            {!! nl2br(e($proposal->job_description)) !!}
                            @if(isset($recentChanges['job_description']))
                                <div class="mt-2">
                                    <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">
                                        <i class="fas fa-edit"></i> Recently edited
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Notes -->
                    @if($proposal->notes)
                    <div class="mb-0">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-sticky-note me-2"></i>Notes
                        </h6>
                        <div class="bg-light p-3 rounded {{ isset($recentChanges['notes']) ? 'border border-warning border-2' : '' }}">
                            {!! nl2br(e($proposal->notes)) !!}
                            @if(isset($recentChanges['notes']))
                                <div class="mt-2">
                                    <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">
                                        <i class="fas fa-edit"></i> Recently edited
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column (4 columns) -->
        <div class="col-lg-4">
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

                    <div class="mb-2">
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

                    <hr class="my-2">

                    <div class="mb-0">
                        <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.75rem;">BD User</label>
                        @if($proposal->user)
                            <div class="d-flex align-items-center p-2 bg-light rounded">
                                <x-avatar :user="$proposal->user" :size="32" class="me-2" />
                                <div style="line-height: 1.3;">
                                    <div class="fw-semibold" style="font-size: 0.813rem;">{{ $proposal->user->name }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $proposal->user->email }}</div>
                                </div>
                            </div>
                        @else
                            <span class="text-muted" style="font-size: 0.813rem;">User not available</span>
                        @endif
                    </div>
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

                    <!-- First Row: 4 columns -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="text-center p-2 bg-light rounded">
                                <div class="fw-bold mb-1" style="font-size: 0.875rem;">
                                    <span class="badge bg-{{ $badgeColor }}">{{ $statusLabel }}</span>
                                </div>
                                <label class="text-muted" style="font-size: 0.7rem;">Status</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 bg-light rounded">
                                <div class="fw-bold mb-1 {{ isset($recentChanges['connects_used']) ? 'text-warning' : 'text-primary' }}" style="font-size: 1.25rem; line-height: 1;">
                                    {{ $proposal->connects_used }}
                                    @if(isset($recentChanges['connects_used']))
                                        <i class="fas fa-edit" style="font-size: 0.6rem;"></i>
                                    @endif
                                </div>
                                <label class="text-muted" style="font-size: 0.7rem;">Connects</label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="text-center p-2 bg-light rounded">
                                <div class="fw-semibold mb-1" style="font-size: 0.813rem;">
                                    {{ \Carbon\Carbon::parse($proposal->submitted_at)->format('M d, Y') }}
                                </div>
                                <label class="text-muted" style="font-size: 0.7rem;">Submitted</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 bg-light rounded">
                                <div class="fw-semibold mb-1" style="font-size: 0.813rem;">
                                    {{ $proposal->created_at->format('M d, Y') }}
                                </div>
                                <label class="text-muted" style="font-size: 0.7rem;">Created</label>
                            </div>
                        </div>
                    </div>

                    <!-- Second Row: Full-width Job URL button -->
                    <div class="mb-0">
                        <a href="{{ $proposal->url }}" target="_blank" class="btn btn-outline-primary btn-sm w-100 py-2">
                            <i class="fas fa-external-link-alt me-1"></i> Open Upwork Job
                        </a>
                    </div>

                    @if(!$proposal->deleted_at && $proposal->status !== 'interviewing')
                    <hr class="my-2">
                    <div>
                        <button class="btn btn-success w-100 py-2" onclick="confirmMoveToInterviewing('{{ route('admin.proposals.moveToInterviewing', $proposal) }}', '{{ $proposal->title }}')">
                            <i class="fas fa-arrow-right me-1"></i> Move to Interviewing
                        </button>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Action History Card -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-2">
                    <h6 class="mb-0 fw-bold" style="font-size: 0.875rem;"><i class="fas fa-history me-2 text-primary"></i>Action History</h6>
                </div>
                <div class="card-body p-0" style="max-height: 350px; overflow-y: auto;">
                    @php
                        $actionLogs = \App\Models\ActionLog::where('model_type', \App\Models\Proposal::class)
                            ->where('model_id', $proposal->id)
                            ->with('user')
                            ->orderByDesc('created_at')
                            ->get();
                    @endphp
                    @if($actionLogs->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($actionLogs as $log)
                                <div class="list-group-item border-0 px-3 py-2">
                                    <div class="d-flex align-items-start">
                                        <div class="me-2">
                                            @if($log->action === 'proposal_deleted')
                                                <div class="rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                                    <i class="fas fa-trash-alt text-danger" style="font-size: 0.75rem;"></i>
                                                </div>
                                            @elseif($log->action === 'proposal_updated')
                                                <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                                    <i class="fas fa-edit text-info" style="font-size: 0.75rem;"></i>
                                                </div>
                                            @elseif($log->action === 'proposal_created')
                                                <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                                    <i class="fas fa-plus text-success" style="font-size: 0.75rem;"></i>
                                                </div>
                                            @else
                                                <div class="rounded-circle bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                                    <i class="fas fa-info-circle text-secondary" style="font-size: 0.75rem;"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold mb-0" style="font-size: 0.813rem;">
                                                {{ $log->user->name ?? 'System' }}
                                            </div>
                                            <div class="text-muted mb-0" style="font-size: 0.75rem;">
                                                {{ $log->description }}
                                            </div>
                                            <div class="text-muted" style="font-size: 0.7rem;">
                                                <i class="far fa-clock me-1"></i>{{ $log->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-inbox fa-2x mb-3 d-block opacity-25"></i>
                            <p class="mb-0">No action logs available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Version History Sidebar -->
    <div id="versionHistorySidebar" class="version-history-sidebar">
        <div class="sidebar-header d-flex justify-content-between align-items-center p-3 border-bottom">
            <h5 class="mb-0"><i class="fas fa-history me-2"></i>Version History</h5>
            <button class="btn btn-sm btn-light" onclick="toggleVersionHistory()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="sidebar-body p-3" id="versionHistoryContent">
            <div class="text-center text-muted py-5">
                <div class="spinner-border spinner-border-sm me-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                Loading versions...
            </div>
        </div>
    </div>

    <!-- Overlay for sidebar -->
    <div id="sidebarOverlay" class="sidebar-overlay" onclick="toggleVersionHistory()"></div>

    <style>
        .version-history-sidebar {
            position: fixed;
            top: 0;
            right: -500px;
            width: 500px;
            height: 100vh;
            background: white;
            box-shadow: -2px 0 10px rgba(0,0,0,0.1);
            z-index: 1050;
            transition: right 0.3s ease;
            overflow-y: auto;
        }

        .version-history-sidebar.active {
            right: 0;
        }

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
            display: none;
        }

        .sidebar-overlay.active {
            display: block;
        }

        .version-item {
            border-left: 3px solid #e9ecef;
            padding-left: 1rem;
            margin-bottom: 1.5rem;
            position: relative;
        }

        .version-item::before {
            content: '';
            position: absolute;
            left: -7px;
            top: 0;
            width: 11px;
            height: 11px;
            background: #fff;
            border: 3px solid #007bff;
            border-radius: 50%;
        }

        .version-item.latest {
            border-left-color: #28a745;
        }

        .version-item.latest::before {
            border-color: #28a745;
        }
    </style>
@endsection

@section('scripts')
<script>
    let versionsLoaded = false;

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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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

    function toggleVersionHistory() {
        const sidebar = document.getElementById('versionHistorySidebar');
        const overlay = document.getElementById('sidebarOverlay');

        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');

        // Load versions if not already loaded
        if (!versionsLoaded && sidebar.classList.contains('active')) {
            loadVersionHistory();
        }
    }

    function loadVersionHistory() {
        const content = document.getElementById('versionHistoryContent');

        fetch('{{ route("admin.proposals.versions", $proposal->id) }}')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.versions.length > 0) {
                    let html = '';
                    data.versions.forEach((version, index) => {
                        const isLatest = index === 0;
                        html += `
                            <div class="version-item ${isLatest ? 'latest' : ''}">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1">Version ${version.version_number}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>${version.user}
                                        </small>
                                    </div>
                                    ${isLatest ? '<span class="badge bg-success">Latest</span>' : ''}
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="far fa-clock me-1"></i>${version.created_at}
                                        <span class="ms-2">(${version.created_at_human})</span>
                                    </small>
                                </div>
                                <div class="mt-2">
                                    <h6 class="small fw-bold mb-2">Changes:</h6>
                                    <ul class="list-unstyled small mb-0">
                        `;

                        for (const [field, change] of Object.entries(version.changes)) {
                            html += `
                                <li class="mb-2">
                                    <span class="badge bg-info me-1">${field}</span>
                                    <div class="mt-1">
                                        <span class="text-muted">Old:</span>
                                        <span class="text-decoration-line-through">${change.old || 'N/A'}</span>
                                    </div>
                                    <div>
                                        <span class="text-muted">New:</span>
                                        <span class="fw-bold">${change.new || 'N/A'}</span>
                                    </div>
                                </li>
                            `;
                        }

                        html += `
                                    </ul>
                                </div>
                            </div>
                        `;
                    });
                    content.innerHTML = html;
                } else {
                    content.innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-inbox fa-2x mb-2"></i><p>No version history available</p></div>';
                }
                versionsLoaded = true;
            })
            .catch(error => {
                content.innerHTML = '<div class="alert alert-danger">Failed to load version history</div>';
            });
    }
</script>
@endsection
