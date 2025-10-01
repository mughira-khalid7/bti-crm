@extends('bd.layouts.master')

@section('title', 'My Goals')
@section('page-title', 'My Goals')

@section('content')
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-bullseye me-2"></i>Daily Goal</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('bd.goals.store') }}" id="goalForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Daily Proposals Goal <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">#</span>
                                <input type="number" name="daily_goal" min="0" max="1000" value="{{ old('daily_goal', $dailyGoal) }}" class="form-control @error('daily_goal') is-invalid @enderror" required placeholder="Enter daily goal">
                            </div>
                            @error('daily_goal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Set your daily target for proposal submissions</small>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Save Goal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Today's Progress</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold">Submitted Today</span>
                            <span class="badge bg-primary fs-6">{{ $todayProposalsCount }} / {{ $dailyGoal }}</span>
                        </div>
                        <div class="progress" style="height: 12px;">
                            <div class="progress-bar bg-primary" style="width: {{ $dailyGoal > 0 ? min(100, round(($todayProposalsCount / max($dailyGoal,1)) * 100)) : 0 }}%"></div>
                        </div>
                        <div class="text-center mt-2">
                            <small class="text-muted">
                                {{ $dailyGoal > 0 ? round(($todayProposalsCount / max($dailyGoal,1)) * 100) : 0 }}% Complete
                            </small>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="h4 text-primary mb-1">{{ $bidsLeft }}</div>
                                <div class="small text-muted">Bids Left Today</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="h4 text-success mb-1">{{ $todayProposalsCount }}</div>
                                <div class="small text-muted">Completed</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Progress updates automatically as you submit proposals
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Initialize form with toast notifications
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('goalForm');
        if (form) {
            submitFormWithToast(form, 'Daily goal saved successfully!', 'Failed to save goal.');
        }
    });
</script>
@endsection


