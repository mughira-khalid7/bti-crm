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

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-file-alt me-2"></i>Proposal Details
                @if($proposal->deleted_at)
                    <span class="badge bg-danger ms-2">Deleted</span>
                @endif
            </h5>
            <a href="{{ route('admin.proposals.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-4">
                        <h4 class="mb-2">{{ $proposal->title }}</h4>
                        <div class="d-flex align-items-center text-muted mb-3">
                            @if($proposal->user)
                                <div class="rounded-circle avatar-dynamic d-flex align-items-center justify-content-center me-2" data-bs-toggle="tooltip" title="{{ $proposal->user->name }}" style="width:32px; height:32px; font-size:.875rem;">
                                    {{ strtoupper(substr($proposal->user->name, 0, 2)) }}
                                </div>
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

                    <div class="mb-4">
                        <h6 class="text-primary mb-3"><i class="fas fa-briefcase me-2"></i>Job Description</h6>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($proposal->job_description)) !!}
                        </div>
                    </div>

                    @if($proposal->notes)
                    <div class="mb-4">
                        <h6 class="text-primary mb-3"><i class="fas fa-sticky-note me-2"></i>Notes</h6>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($proposal->notes)) !!}
                        </div>
                    </div>
                    @endif
                </div>

                <div class="col-md-4">
                    <div class="card border">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Proposal Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Status</label>
                                <div>
                                    <span class="badge bg-{{ $proposal->status=='interviewing'?'info':'secondary' }} fs-6">{{ ucfirst($proposal->status) }}</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Connects Used</label>
                                <div class="text-primary fw-bold fs-5">{{ $proposal->connects_used }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Job URL</label>
                                <div>
                                    <a href="{{ $proposal->url }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-external-link-alt me-1"></i> Open Link
                                    </a>
                                </div>
                            </div>

                            @if(!$proposal->deleted_at && $proposal->status !== 'interviewing')
                            <div class="mb-3">
                                <button class="btn btn-success w-100" onclick="confirmMoveToInterviewing('{{ route('admin.proposals.moveToInterviewing', $proposal) }}', '{{ $proposal->title }}')">
                                    <i class="fas fa-arrow-right me-1"></i> Move to Interviewing
                                </button>
                            </div>
                            @endif

                            <div class="mt-4">
                                <h6 class="fw-bold mb-3"><i class="fas fa-history me-2"></i>Action History</h6>
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
                                            <div class="list-group-item px-0 py-2 border-0">
                                                <div class="d-flex align-items-start">
                                                    @if($log->action === 'proposal_deleted')
                                                        <i class="fas fa-trash-alt text-danger me-2 mt-1"></i>
                                                    @elseif($log->action === 'proposal_updated')
                                                        <i class="fas fa-edit text-info me-2 mt-1"></i>
                                                    @else
                                                        <i class="fas fa-info-circle text-muted me-2 mt-1"></i>
                                                    @endif
                                                    <div class="flex-grow-1">
                                                        <div class="small">
                                                            <strong>{{ $log->user->name ?? 'System' }}</strong>
                                                        </div>
                                                        <div class="small text-muted">
                                                            {{ $log->description }}
                                                        </div>
                                                        <div class="small text-muted">
                                                            <i class="far fa-clock me-1"></i>{{ $log->created_at->format('M d, Y h:i A') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-muted small">
                                        <i class="fas fa-info-circle me-1"></i>No action logs available.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function confirmMoveToInterviewing(url, title) {
        if (confirm(`Are you sure you want to move "${title}" to interviewing status?`)) {
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
                    window.toast.success('Success!', 'Proposal moved to interviewing successfully!');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    window.toast.error('Error!', data.message || 'Failed to move proposal.');
                }
            })
            .catch(error => {
                window.toast.error('Error!', 'An unexpected error occurred.');
            });
        }
    }
</script>
@endsection


