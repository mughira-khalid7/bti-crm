@extends('bd.layouts.master')

@section('title', 'Proposal Details')
@section('page-title', 'Proposal Details')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Proposal Details</h5>
            <a href="{{ route('bd.proposals.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-4">
                        <h4 class="mb-2">{{ $proposal->title }}</h4>
                        <div class="text-muted mb-3">
                            Submitted on {{ \Carbon\Carbon::parse($proposal->submitted_at)->format('M d, Y \a\t h:i A') }}
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

                            @if($proposal->status !== 'interviewing')
                            <div class="mb-3">
                                <button class="btn btn-success w-100" onclick="confirmMoveToInterviewing('{{ route('bd.proposals.moveToInterviewing', $proposal) }}', '{{ $proposal->title }}')">
                                    <i class="fas fa-arrow-right me-1"></i> Move to Interviewing
                                </button>
                            </div>
                            @endif
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


