@extends('admin.layouts.master')

@section('title', 'Proposal Details')
@section('page-title', 'Proposal Details')

@php
    function highlightWordDifferences($oldText, $newText) {
        if (!$oldText || !$newText || $oldText === $newText) {
            return e($newText);
        }

        // Split texts into words while preserving whitespace
        $oldWords = preg_split('/(\s+)/', $oldText, -1, PREG_SPLIT_DELIM_CAPTURE);
        $newWords = preg_split('/(\s+)/', $newText, -1, PREG_SPLIT_DELIM_CAPTURE);

        $result = '';
        $oldIndex = 0;
        $newIndex = 0;

        while ($newIndex < count($newWords)) {
            $newWord = $newWords[$newIndex];

            // Skip whitespace
            if (preg_match('/^\s+$/', $newWord)) {
                $result .= e($newWord);
                $newIndex++;
                continue;
            }

            // Check if this word exists in the old text at the current position
            $found = false;
            for ($i = $oldIndex; $i < count($oldWords); $i++) {
                if ($oldWords[$i] === $newWord) {
                    $result .= e($newWord);
                    $oldIndex = $i + 1;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                // This is a new word, highlight it
                $result .= '<span class="bg-warning bg-opacity-50 px-1 rounded">' . e($newWord) . '</span>';
            }

            $newIndex++;
        }

        return $result;
    }

    // Get the previous version data for comparison
    $previousVersion = null;
    if (isset($recentChanges) && !empty($recentChanges)) {
        $latestVersion = $proposal->versions()->with('user')->first();
        if ($latestVersion && isset($latestVersion->snapshot)) {
            $previousVersion = $latestVersion->snapshot;
        }
    }
@endphp

@section('content')
    @if ($proposal->deleted_at)
        @php
            $deleteLog = \App\Models\ActionLog::where('model_type', \App\Models\Proposal::class)
                ->where('model_id', $proposal->id)
                ->where('action', 'proposal_deleted')
                ->with('user')
                ->latest()
                ->first();
        @endphp

        @if ($proposal->deletion_type === 'admin')
            <div class="alert alert-danger border-0 rounded-3 shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading mb-1">
                            <i class="fas fa-trash-alt me-2"></i>This Proposal Has Been Deleted by Admin
                        </h5>
                        <p class="mb-0">
                            This proposal was deleted by an admin on
                            <strong>{{ $proposal->deleted_at->format('F j, Y \a\t h:i A') }}</strong>
                            @if ($deleteLog && $deleteLog->user)
                                by <strong>{{ $deleteLog->user->name }}</strong>
                            @endif
                            and is hidden from all views.
                        </p>
                    </div>
                </div>
            </div>
        @elseif ($proposal->deletion_type === 'bd')
            <div class="alert alert-warning border-0 rounded-3 shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-eye-slash fa-2x me-3"></i>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading mb-1">
                            <i class="fas fa-user-slash me-2"></i>This Proposal Was Deleted by BD User
                        </h5>
                        <p class="mb-0">
                            This proposal was deleted by the BD user on
                            <strong>{{ $proposal->deleted_at->format('F j, Y \a\t h:i A') }}</strong>
                            @if ($deleteLog && $deleteLog->user)
                                by <strong>{{ $deleteLog->user->name }}</strong>
                            @endif
                            and is hidden from BD view but visible to admins for review.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-file-alt me-2 text-primary"></i>Proposal Details
                    @if ($proposal->deleted_at)
                        <span class="badge bg-danger ms-2">Deleted</span>
                    @endif
                </h4>
                <div>
                    <button class="btn btn-outline-primary btn-sm me-2" onclick="toggleVersionHistory()">
                        <i class="fas fa-history me-1"></i> Version History
                    </button>
                    @if (!$proposal->deleted_at)
                        <button class="btn btn-outline-success btn-sm me-2" onclick="generateAIProposal({{ $proposal->id }})">
                            <i class="fas fa-robot me-1"></i> Generate AI Proposal
                        </button>
                    @endif
                    @if ($proposal->deleted_at && $proposal->deletion_type === 'bd')
                        <form method="POST" action="{{ route('admin.proposals.destroy', $proposal) }}" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="permanent" value="1">
                            <button type="button" class="btn btn-outline-danger btn-sm me-2"
                                onclick="confirmPermanentDelete('{{ $proposal->title }}')">
                                <i class="fas fa-trash-alt me-1"></i> Delete Permanently
                            </button>
                        </form>
                    @endif
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
                        <h3 class="mb-2">
                            @if (isset($recentChanges['title']) && $previousVersion)
                                {!! highlightWordDifferences($previousVersion['title'] ?? '', $proposal->title) !!}
                                <span class="badge bg-warning text-dark ms-2" style="font-size: 0.7rem;">
                                    <i class="fas fa-edit"></i> Recently edited
                                </span>
                            @else
                                {{ $proposal->title }}
                            @endif
                        </h3>
                        <div class="d-flex align-items-center text-muted">
                            @if ($proposal->user)
                                <x-avatar :user="$proposal->user" :size="32" class="me-2" />
                                <span>Submitted by <strong>{{ $proposal->user->name }}</strong>
                                    @if ($proposal->user->trashed())
                                        <span class="badge bg-danger ms-1" style="font-size: 0.65rem;">Deactivated</span>
                                    @endif
                                    on {{ \Carbon\Carbon::parse($proposal->submitted_at)->format('M d, Y \a\t h:i A') }}
                                </span>
                            @else
                                <span>Submitted by <strong class="text-muted">Deleted User</strong> on
                                    {{ \Carbon\Carbon::parse($proposal->submitted_at)->format('M d, Y \a\t h:i A') }}</span>
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
                            @if (isset($recentChanges['job_description']) && $previousVersion)
                                {!! nl2br(highlightWordDifferences($previousVersion['job_description'] ?? '', $proposal->job_description)) !!}
                                <div class="mt-2">
                                    <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">
                                        <i class="fas fa-edit"></i> Recently edited
                                    </span>
                                </div>
                            @else
                                {!! nl2br(e($proposal->job_description)) !!}
                            @endif
                        </div>
                    </div>

                    <!-- Notes -->
                    @if ($proposal->notes)
                        <div class="mb-0">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-sticky-note me-2"></i>Notes
                            </h6>
                            <div class="bg-light p-3 rounded {{ isset($recentChanges['notes']) ? 'border border-warning border-2' : '' }}">
                                @if (isset($recentChanges['notes']) && $previousVersion)
                                    {!! nl2br(highlightWordDifferences($previousVersion['notes'] ?? '', $proposal->notes)) !!}
                                    <div class="mt-2">
                                        <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">
                                            <i class="fas fa-edit"></i> Recently edited
                                        </span>
                                    </div>
                                @else
                                    {!! nl2br(e($proposal->notes)) !!}
                                @endif
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
                    <h6 class="mb-0 fw-bold" style="font-size: 0.875rem;"><i
                            class="fas fa-address-card me-2 text-primary"></i>Client Information</h6>
                </div>
                <div class="card-body p-3">
                    <div class="mb-2">
                        <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.75rem;">Client
                            Name</label>
                        <div class="d-flex align-items-center p-2 bg-light rounded">
                            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-2"
                                style="width: 32px; height: 32px;">
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
                        <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.75rem;">Total
                            Spending</label>
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

                    <div class="mb-2">
                        <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.75rem;">BD User</label>
                        @if ($proposal->user)
                            <div class="d-flex align-items-center p-2 bg-light rounded">
                                <x-avatar :user="$proposal->user" :size="32" class="me-2" />
                                <div style="line-height: 1.3;">
                                    <div class="fw-semibold" style="font-size: 0.813rem;">{{ $proposal->user->name }}
                                    </div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $proposal->user->email }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <span class="text-muted" style="font-size: 0.813rem;">User not available</span>
                        @endif
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
                    <h6 class="mb-0 fw-bold" style="font-size: 0.875rem;"><i
                            class="fas fa-info-circle me-2 text-primary"></i>Proposal Information</h6>
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
                        <div class="col-12 col-md-6">
                            <div class="p-2 bg-light rounded">
                                <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.75rem;">Status</label>
                                <div class="d-flex flex-wrap align-items-center gap-2 status-actions">
                                    <span id="currentStatusBadge" class="badge bg-{{ $badgeColor }} px-2 py-1">{{ $statusLabel }}</span>
                                    <select id="statusSelect" class="form-select form-select-sm" style="max-width: 230px; min-width: 170px;">
                                        @php $statuses=['submitted'=>'Submitted','interviewing'=>'Interviewing','meeting_scheduled'=>'Meeting Scheduled','phone_shared'=>'Phone Shared','viewed'=>'Viewed','copied'=>'Copied']; @endphp
                                        @foreach($statuses as $key=>$label)
                                            <option value="{{ $key }}" {{ $proposal->status===$key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <button id="updateStatusBtn" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-check me-1"></i> Update
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 bg-light rounded">
                                <div class="fw-bold mb-1 {{ isset($recentChanges['connects_used']) ? 'text-warning' : 'text-primary' }}"
                                    style="font-size: 1.25rem; line-height: 1;">
                                    {{ $proposal->connects_used }}
                                    @if (isset($recentChanges['connects_used']))
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
                        <a href="{{ $proposal->url }}" target="_blank" class="btn btn-outline-primary w-100 py-2">
                            <i class="fas fa-external-link-alt me-1"></i> Open Upwork Job
                        </a>
                    </div>

                    @if (!$proposal->deleted_at && $proposal->status !== 'interviewing')
                        <hr class="my-2">
                        <div>
                            <button class="btn btn-success w-100 py-2"
                                onclick="confirmMoveToInterviewing('{{ route('admin.proposals.moveToInterviewing', $proposal) }}', '{{ $proposal->title }}')">
                                <i class="fas fa-arrow-right me-1"></i> Move to Interviewing
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Meeting Information Card -->
            @if ($proposal->meeting && $proposal->meeting->count() > 0)
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-white border-bottom py-2">
                        <h6 class="mb-0 fw-bold" style="font-size: 0.875rem;"><i
                                class="fas fa-calendar-alt me-2 text-primary"></i>Scheduled Meeting</h6>
                    </div>
                    <div class="card-body p-3">
                        @php
                            $meeting = $proposal->meeting->first();
                        @endphp
                        <div class="mb-2">
                            <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.75rem;">Meeting
                                Title</label>
                            <div class="p-2 bg-light rounded" style="font-size: 0.875rem;">
                                <i class="fas fa-video me-2 text-primary"></i>
                                <span class="fw-bold">{{ $meeting->title }}</span>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.75rem;">Scheduled
                                Date & Time</label>
                            <div class="p-2 bg-light rounded" style="font-size: 0.875rem;">
                                <i class="fas fa-clock me-2 text-success"></i>
                                <span class="fw-bold">{{ $meeting->scheduled_at->format('M d, Y \a\t h:i A') }}</span>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.75rem;">Assigned
                                BD</label>
                            <div class="d-flex align-items-center p-2 bg-light rounded">
                                <x-avatar :user="$meeting->bdUser" :size="32" class="me-2" />
                                <div style="line-height: 1.3;">
                                    <div class="fw-semibold" style="font-size: 0.813rem;">{{ $meeting->bdUser->name }}
                                    </div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $meeting->bdUser->email }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($meeting->meeting_link)
                            <div class="mb-2">
                                <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.75rem;">Meeting
                                    Link</label>
                                <div class="p-2 bg-light rounded" style="font-size: 0.875rem;">
                                    <a href="{{ $meeting->meeting_link }}" target="_blank" class="text-decoration-none">
                                        <i class="fas fa-external-link-alt me-2 text-primary"></i>
                                        Join Meeting
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if ($meeting->location)
                            <div class="mb-2">
                                <label class="form-label fw-semibold text-muted mb-1"
                                    style="font-size: 0.75rem;">Location</label>
                                <div class="p-2 bg-light rounded" style="font-size: 0.875rem;">
                                    <i class="fas fa-map-marker-alt me-2 text-danger"></i>
                                    <span>{{ $meeting->location }}</span>
                                </div>
                            </div>
                        @endif

                        <div class="mb-0">
                            <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.75rem;">Meeting
                                Type</label>
                            <div class="p-2 bg-light rounded" style="font-size: 0.875rem;">
                                @php
                                    $typeIcons = [
                                        'video_call' => 'fas fa-video',
                                        'phone_call' => 'fas fa-phone',
                                        'in_person' => 'fas fa-handshake',
                                    ];
                                    $typeLabels = [
                                        'video_call' => 'Video Call',
                                        'phone_call' => 'Phone Call',
                                        'in_person' => 'In-Person',
                                    ];
                                @endphp
                                <i
                                    class="{{ $typeIcons[$meeting->meeting_type] ?? 'fas fa-calendar' }} me-2 text-info"></i>
                                <span>{{ $typeLabels[$meeting->meeting_type] ?? ucfirst($meeting->meeting_type) }}</span>
                            </div>
                        </div>

                        <hr class="my-2">

                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm flex-fill"
                                onclick="editMeeting({{ $meeting->id }})">
                                <i class="fas fa-edit me-1"></i> Edit
                            </button>
                            <button class="btn btn-outline-danger btn-sm flex-fill"
                                onclick="cancelMeeting({{ $proposal->id }})">
                                <i class="fas fa-times me-1"></i> Cancel
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <!-- Schedule Meeting Card -->
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-white border-bottom py-2">
                        <h6 class="mb-0 fw-bold" style="font-size: 0.875rem;"><i
                                class="fas fa-calendar-plus me-2 text-primary"></i>Schedule Meeting</h6>
                    </div>
                    <div class="card-body p-3">
                        <p class="text-muted small mb-3">Schedule a meeting with the client and assign a BD representative.
                        </p>
                        <button class="btn btn-primary w-100 py-2" onclick="openScheduleMeetingModal()">
                            <i class="fas fa-calendar-plus me-1"></i> Schedule Meeting
                        </button>
                    </div>
                </div>
            @endif

            <!-- Action History Card -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-2">
                    <h6 class="mb-0 fw-bold" style="font-size: 0.875rem;"><i
                            class="fas fa-history me-2 text-primary"></i>Action History</h6>
                </div>
                <div class="card-body p-0" style="max-height: 350px; overflow-y: auto;">
                    @php
                        $actionLogs = \App\Models\ActionLog::where('model_type', \App\Models\Proposal::class)
                            ->where('model_id', $proposal->id)
                            ->with('user')
                            ->orderByDesc('created_at')
                            ->get();
                    @endphp
                    @if ($actionLogs->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach ($actionLogs as $log)
                                <div class="list-group-item border-0 px-3 py-2">
                                    <div class="d-flex align-items-start">
                                        <div class="me-2">
                                            @if ($log->action === 'proposal_deleted')
                                                <div class="rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center"
                                                    style="width: 28px; height: 28px;">
                                                    <i class="fas fa-trash-alt text-danger"
                                                        style="font-size: 0.75rem;"></i>
                                                </div>
                                            @elseif($log->action === 'proposal_updated')
                                                <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center"
                                                    style="width: 28px; height: 28px;">
                                                    <i class="fas fa-edit text-info" style="font-size: 0.75rem;"></i>
                                                </div>
                                            @elseif($log->action === 'proposal_created')
                                                <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center"
                                                    style="width: 28px; height: 28px;">
                                                    <i class="fas fa-plus text-success" style="font-size: 0.75rem;"></i>
                                                </div>
                                            @else
                                                <div class="rounded-circle bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center"
                                                    style="width: 28px; height: 28px;">
                                                    <i class="fas fa-info-circle text-secondary"
                                                        style="font-size: 0.75rem;"></i>
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

    <!-- Meeting Scheduling Modal -->
    <div class="modal fade" id="meetingModal" tabindex="-1" aria-labelledby="meetingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="meetingModalLabel">
                        <i class="fas fa-calendar-plus me-2"></i>Schedule Meeting
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="meetingForm">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="title" class="form-label">Meeting Title <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title"
                                    placeholder="e.g., Client Discussion - {{ $proposal->title }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="meeting_type" class="form-label">Meeting Type <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="meeting_type" name="meeting_type" required>
                                    <option value="video_call">Video Call</option>
                                    <option value="phone_call">Phone Call</option>
                                    <option value="in_person">In-Person</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                    placeholder="Brief description of the meeting agenda..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="scheduled_at" class="form-label">Date & Time <span
                                        class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="scheduled_at" name="scheduled_at"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label for="meeting_link" class="form-label">Meeting Link</label>
                                <input type="url" class="form-control" id="meeting_link" name="meeting_link"
                                    placeholder="https://meet.google.com/..." style="display: none;">
                            </div>
                            <div class="col-md-6" id="location_field" style="display: none;">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location"
                                    placeholder="Office address or meeting room...">
                            </div>
                            <div class="col-12">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2"
                                    placeholder="Additional notes or preparation instructions..."></textarea>
                            </div>
                            <!-- Primary and additional BD selection (Upwork Profile layout) -->
                            <div class="col-12">
                                <label class="form-label">Assign BDs</label>
                                <div class="text-muted small mb-1">Select one <strong>Primary BD</strong> and optionally
                                    add other BD participants.</div>

                                <!-- Search and Controls -->
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="input-group" style="max-width: 300px;">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" class="form-control" id="bdParticipantsSearch"
                                            placeholder="Search BD users...">
                                    </div>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            id="bdParticipantsSelectAll">
                                            <i class="fas fa-check-square me-1"></i>Select All
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm"
                                            id="bdParticipantsSelectNone">
                                            <i class="fas fa-square me-1"></i>None
                                        </button>
                                    </div>
                                </div>

                                <!-- BD Users Grid -->
                                <div class="bd-users-container border rounded px-4 pt-1"
                                    style="max-height: 130px; overflow-y: auto;">
                                    <div class="row g-2" id="bdParticipantsGrid">
                                        @php $breakpointCount = 0; @endphp
                                        @forelse($bdUsers as $bd)
                                            @if ($breakpointCount > 0 && $breakpointCount % 4 == 0)
                                    </div>
                                    <div class="row g-2">
                                        @endif
                                        @php $breakpointCount++; @endphp
                                        <div class="col-md-6">
                                            <div class="bd-user-card" data-bd-name="{{ strtolower($bd->name) }}"
                                                data-bd-email="{{ strtolower($bd->email ?? '') }}">
                                                <div class="d-flex align-items-center p-2 rounded hover-bg-light"
                                                    style="cursor: pointer; transition: all 0.2s ease; min-height: 72px;">

                                                    <div class="form-check me-3" title="Include as participant">
                                                        <input class="form-check-input bd-participant-checkbox"
                                                            type="checkbox" id="bd_participant_{{ $bd->id }}"
                                                            name="assigned_bd_ids[]" value="{{ $bd->id }}"
                                                            style="transform: scale(1.1);">
                                                    </div>
                                                    <label class="d-flex align-items-center flex-grow-1 w-100"
                                                        for="bd_participant_{{ $bd->id }}"
                                                        style="cursor: pointer; margin-bottom: 0;">
                                                        <div class="d-flex align-items-center w-100">
                                                            <div class="bd-avatar me-3" style="flex-shrink: 0;">
                                                                <x-avatar :user="$bd" :size="36" />
                                                            </div>
                                                            <div class="bd-info flex-grow-1">
                                                                <div class="bd-name fw-bold">{{ $bd->name }}</div>
                                                                <small
                                                                    class="bd-email text-muted small">{{ $bd->email }}</small>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 text-muted small">No BD users available.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-calendar-plus me-1"></i> Schedule Meeting
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* Scrollable right sidebar */
        .sidebar-scroll {
            max-height: none;
            overflow: visible;
            padding-right: 0;
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #c7c7c7;
            border-radius: 4px;
        }

        .version-history-sidebar {
            position: fixed;
            top: 0;
            right: -500px;
            width: 500px;
            height: 100vh;
            background: white;
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1050;
            transition: right 0.3s ease;
            overflow-y: auto;
        }

        .version-history-sidebar.active {
            right: 0;
        }

        /* Minor UI refinements */
        .status-actions select { min-width: 170px; }
        .status-actions #updateStatusBtn { white-space: nowrap; }

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
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
        document.addEventListener('DOMContentLoaded', function(){
            const updateBtn = document.getElementById('updateStatusBtn');
            const select = document.getElementById('statusSelect');
            const badge = document.getElementById('currentStatusBadge');
            const badgeColorMap = {copied:'warning', interviewing:'info', submitted:'secondary', deleted:'danger', viewed:'primary', meeting_scheduled:'success', phone_shared:'dark'};

            updateBtn?.addEventListener('click', async function(){
                try{
                    const res = await fetch('{{ route('admin.proposals.updateStatus', $proposal) }}', {method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').getAttribute('content')}, body: JSON.stringify({status: select.value})});
                    const data = await res.json();
                    if(data?.success){
                        window.toast?.success('Updated', 'Status updated successfully.');
                        const labelMap = {meeting_scheduled:'Meeting Scheduled', phone_shared:'Phone Shared'};
                        badge.textContent = labelMap[select.value] ?? (select.options[select.selectedIndex]?.text || select.value);
                        badge.className = 'badge bg-' + (badgeColorMap[select.value] || 'secondary');
                    } else {
                        window.toast?.error('Failed', data?.message || 'Could not update status');
                    }
                }catch(e){ window.toast?.error('Failed', 'Could not update status'); }
            });
        });

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

            fetch('{{ route('admin.proposals.versions', $proposal->id) }}')
                .then(response => response.json())
                .then(data => {
                    console.log('Version history data:', data); // Debug log

                    if (data.success && data.versions && data.versions.length > 0) {
                        let html = '';
                        try {
                            data.versions.forEach((version, index) => {
                                const isLatest = index === 0;
                                html += `
                                <div class="version-item ${isLatest ? 'latest' : ''}">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1">Version ${version.version_number}</h6>
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>${version.user || 'Unknown User'}
                                            </small>
                                        </div>
                                        ${isLatest ? '<span class="badge bg-success">Latest</span>' : ''}
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="far fa-clock me-1"></i>${version.created_at || 'Unknown Date'}
                                            <span class="ms-2">(${version.created_at_human || 'Unknown'})</span>
                                        </small>
                                    </div>
                                    <div class="mt-2">
                                        <h6 class="small fw-bold mb-2">Changes:</h6>
                                        <ul class="list-unstyled small mb-0">
                                `;

                                if (version.changes && typeof version.changes === 'object') {
                                    for (const [field, change] of Object.entries(version.changes)) {
                                        try {
                                            const oldValue = change.old || 'N/A';
                                            const newValue = change.new || 'N/A';
                                            const highlightedNew = highlightDifferences(oldValue, newValue);

                                            html += `
                                            <li class="mb-2">
                                                <span class="badge bg-info me-1">${field}</span>
                                                <div class="mt-1">
                                                    <span class="text-muted">Old:</span>
                                                    <span class="text-decoration-line-through">${escapeHtml(oldValue)}</span>
                                                </div>
                                                <div>
                                                    <span class="text-muted">New:</span>
                                                    <span class="fw-bold">${highlightedNew}</span>
                                                </div>
                                            </li>
                                        `;
                                        } catch (error) {
                                            console.error('Error processing change for field:', field, error);
                                            html += `
                                            <li class="mb-2">
                                                <span class="badge bg-info me-1">${field}</span>
                                                <div class="text-danger">Error displaying change</div>
                                            </li>
                                            `;
                                        }
                                    }
                                }

                                html += `
                                        </ul>
                                    </div>
                                </div>
                            `;
                            });
                        } catch (error) {
                            console.error('Error processing versions:', error);
                            html = '<div class="alert alert-danger">Error processing version data</div>';
                        }
                        content.innerHTML = html;
                    } else {
                        content.innerHTML =
                            '<div class="text-center text-muted py-5"><i class="fas fa-inbox fa-2x mb-2"></i><p>No version history available</p></div>';
                    }
                    versionsLoaded = true;
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    content.innerHTML = '<div class="alert alert-danger">Failed to load version history</div>';
                });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function highlightDifferences(oldText, newText) {
            try {
                if (!oldText || !newText || oldText === 'N/A' || newText === 'N/A') {
                    return escapeHtml(newText);
                }

                // Convert to strings and escape HTML
                const oldStr = String(oldText);
                const newStr = String(newText);

                // Split texts into words while preserving whitespace
                const oldWords = oldStr.split(/(\s+)/);
                const newWords = newStr.split(/(\s+)/);

                let result = '';
                let oldIndex = 0;
                let newIndex = 0;

                while (newIndex < newWords.length) {
                    const newWord = newWords[newIndex];

                    // Skip whitespace
                    if (/^\s+$/.test(newWord)) {
                        result += escapeHtml(newWord);
                        newIndex++;
                        continue;
                    }

                    // Check if this word exists in the old text at the current position
                    let found = false;
                    for (let i = oldIndex; i < oldWords.length; i++) {
                        if (oldWords[i] === newWord) {
                            result += escapeHtml(newWord);
                            oldIndex = i + 1;
                            found = true;
                            break;
                        }
                    }

                    if (!found) {
                        // This is a new word, highlight it
                        result += `<span class="bg-warning bg-opacity-50 px-1 rounded">${escapeHtml(newWord)}</span>`;
                    }

                    newIndex++;
                }

                return result;
            } catch (error) {
                console.error('Error in highlightDifferences:', error);
                return escapeHtml(newText);
            }
        }

        // Meeting scheduling functions
        function openScheduleMeetingModal() {
            // Set default title
            document.getElementById('title').value = 'Client Discussion - {{ $proposal->title }}';

            // Set minimum date to today
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('scheduled_at').min = `${year}-${month}-${day}T${hours}:${minutes}`;

            // Ensure meeting link visibility matches current type
            const meetingTypeEl = document.getElementById('meeting_type');
            const meetingLinkField = document.getElementById('meeting_link');
            const locationField = document.getElementById('location_field');
            if (meetingTypeEl.value === 'video_call') {
                meetingLinkField.style.display = 'block';
                meetingLinkField.required = true;
                locationField.style.display = 'none';
                document.getElementById('location').required = false;
            } else if (meetingTypeEl.value === 'in_person') {
                meetingLinkField.style.display = 'none';
                meetingLinkField.required = false;
                locationField.style.display = 'block';
                document.getElementById('location').required = true;
            } else {
                meetingLinkField.style.display = 'none';
                meetingLinkField.required = false;
                locationField.style.display = 'none';
                document.getElementById('location').required = false;
            }

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('meetingModal'));
            modal.show();
        }

        function editMeeting(meetingId) {
            // For now, just open the schedule modal
            // In a full implementation, you'd load the meeting data and populate the form
            openScheduleMeetingModal();
        }

        function cancelMeeting(proposalId) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to cancel this meeting?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, cancel it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/proposals/${proposalId}/cancel-meeting`, {
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
                                Swal.fire('Cancelled!', 'Meeting cancelled successfully!', 'success');
                                setTimeout(() => window.location.reload(), 1000);
                            } else {
                                Swal.fire('Error!', data.message || 'Failed to cancel meeting.', 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error!', 'An unexpected error occurred.', 'error');
                        });
                }
            });
        }

        // Handle meeting type change
        document.getElementById('meeting_type').addEventListener('change', function() {
            const meetingLinkField = document.getElementById('meeting_link');
            const locationField = document.getElementById('location_field');

            if (this.value === 'video_call') {
                meetingLinkField.style.display = 'block';
                meetingLinkField.required = true;
                locationField.style.display = 'none';
                document.getElementById('location').required = false;
            } else if (this.value === 'in_person') {
                meetingLinkField.style.display = 'none';
                meetingLinkField.required = false;
                locationField.style.display = 'block';
                document.getElementById('location').required = true;
            } else {
                meetingLinkField.style.display = 'none';
                meetingLinkField.required = false;
                locationField.style.display = 'none';
                document.getElementById('location').required = false;
            }
        });

        // Participants grid helpers (search/select all/none)
        const bdSearch = document.getElementById('bdParticipantsSearch');
        if (bdSearch) {
            bdSearch.addEventListener('input', function() {
                const term = this.value.toLowerCase();
                document.querySelectorAll('#bdParticipantsGrid .bd-user-card').forEach(card => {
                    const name = card.getAttribute('data-bd-name') || '';
                    const email = card.getAttribute('data-bd-email') || '';
                    const match = name.includes(term) || email.includes(term);
                    card.parentElement.style.display = match ? '' : 'none';
                });
            });
        }

        const selectAllBtn = document.getElementById('bdParticipantsSelectAll');
        const selectNoneBtn = document.getElementById('bdParticipantsSelectNone');
        if (selectAllBtn && selectNoneBtn) {
            selectAllBtn.addEventListener('click', function() {
                document.querySelectorAll('.bd-participant-checkbox').forEach(cb => cb.checked = true);
            });
            selectNoneBtn.addEventListener('click', function() {
                document.querySelectorAll('.bd-participant-checkbox').forEach(cb => cb.checked = false);
            });
        }

        // Handle meeting form submission
        document.getElementById('meetingForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            // Ensure at least one BD is selected; first checked becomes primary on backend
            const checkedBds = Array.from(document.querySelectorAll('.bd-participant-checkbox:checked'));
            if (checkedBds.length === 0) {
                Swal.fire('Missing BD', 'Please select at least one BD.', 'warning');
                return;
            }
            // Append all selected BDs
            checkedBds.forEach(cb => formData.append('assigned_bd_ids[]', cb.value));
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Scheduling...';
            submitBtn.disabled = true;

            fetch('{{ route('admin.proposals.scheduleMeeting', $proposal->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Success!', 'Meeting scheduled successfully!', 'success');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        Swal.fire('Error!', data.message || 'Failed to schedule meeting.', 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'An unexpected error occurred.', 'error');
                })
                .finally(() => {
                    // Reset button state
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        });

        function confirmPermanentDelete(title) {
            Swal.fire({
                title: 'Permanent Deletion',
                text: `Are you sure you want to PERMANENTLY delete "${title}"? This action cannot be undone and will remove all data including versions and logs.`,
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete permanently!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Find the form and submit it
                    const form = document.querySelector('form[action*="destroy"]');
                    if (form) {
                        form.submit();
                    }
                }
            });
        }

        // AI Proposal Generation
        async function generateAIProposal(proposalId) {
            // Show loading state
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Generating...';
            button.disabled = true;

            try {
                // Call the AI generation API
                const response = await fetch(`/admin/proposals/${proposalId}/generate-ai`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Show the AI proposal modal
                    const modal = new bootstrap.Modal(document.getElementById('aiProposalModal'));
                    modal.show();

                    // Load the generated proposal
                    loadGeneratedProposal(data.proposal);
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to generate AI proposal. Please try again.',
                        icon: 'error'
                    });
                }
            } catch (error) {
                console.error('Error generating AI proposal:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred while generating the AI proposal.',
                    icon: 'error'
                });
            } finally {
                // Reset button
                button.innerHTML = originalText;
                button.disabled = false;
            }
        }

        function loadGeneratedProposal(proposalData) {
            // Display the AI-generated proposal content
            document.getElementById('aiProposalContent').innerHTML = proposalData.content;
            
            // Update modal title with generation info
            const modalTitle = document.getElementById('aiProposalModalLabel');
            modalTitle.innerHTML = `
                <i class="fas fa-robot me-2"></i>AI-Generated Proposal
                <small class="text-light ms-2">(${proposalData.word_count} words)</small>
            `;
        }

        function copyAIProposal() {
            const content = document.getElementById('aiProposalContent').innerText;
            navigator.clipboard.writeText(content).then(() => {
                Swal.fire({
                    title: 'Copied!',
                    text: 'AI proposal has been copied to clipboard',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        }

        function useAIProposal() {
            Swal.fire({
                title: 'Use AI Proposal?',
                text: 'This will replace the current job description with the AI-generated content. Are you sure?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, use it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Here you would implement the actual replacement logic
                    Swal.fire({
                        title: 'Success!',
                        text: 'AI proposal has been applied to the job description',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        }
    </script>

    <!-- AI Proposal Modal -->
    <div class="modal fade" id="aiProposalModal" tabindex="-1" aria-labelledby="aiProposalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="aiProposalModalLabel">
                        <i class="fas fa-robot me-2"></i>AI-Generated Proposal
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div id="aiProposalContent">
                        <!-- AI-generated content will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Close
                    </button>
                    <button type="button" class="btn btn-outline-primary" onclick="copyAIProposal()">
                        <i class="fas fa-copy me-1"></i>Copy to Clipboard
                    </button>
                    <button type="button" class="btn btn-success" onclick="useAIProposal()">
                        <i class="fas fa-check me-1"></i>Use This Proposal
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
