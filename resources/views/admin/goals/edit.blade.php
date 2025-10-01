@extends('admin.layouts.master')

@section('title', 'Edit Goal - BD CRM')
@section('page-title', 'Edit Goal')

@section('content')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Goal for {{ $goal->user ? $goal->user->name : 'Deleted User' }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.goals.update', $goal) }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">User</label>
                                <div class="form-control-plaintext">
                                    @if($goal->user)
                                        <div class="d-flex align-items-center">
                                            <x-avatar :user="$goal->user" :size="32" class="me-2" />
                                            <div>
                                                <div class="fw-bold">
                                                    {{ $goal->user->name }}
                                                    @if($goal->user->trashed())
                                                        <span class="badge bg-danger ms-1" style="font-size: 0.65rem;">Deactivated</span>
                                                    @endif
                                                </div>
                                                <small class="text-muted">{{ $goal->user->email }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-muted">
                                            <i class="fas fa-user-slash me-2"></i>Deleted User
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="daily_goal" class="form-label fw-semibold">Daily Goal <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">#</span>
                                    <input type="number" name="daily_goal" id="daily_goal"
                                           class="form-control @error('daily_goal') is-invalid @enderror"
                                           value="{{ old('daily_goal', $goal->daily_goal) }}"
                                           min="0" max="1000" required
                                           placeholder="Enter daily goal">
                                </div>
                                @error('daily_goal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Set the daily target for proposal submissions</small>
                            </div>
                            <div class="col-md-6">
                                <label for="allowed_connects" class="form-label fw-semibold">Allowed Connects <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-link"></i></span>
                                    <input type="number" name="allowed_connects" id="allowed_connects"
                                           class="form-control @error('allowed_connects') is-invalid @enderror"
                                           value="{{ old('allowed_connects', $goal->allowed_connects) }}"
                                           min="0" max="1000" required
                                           placeholder="Enter allowed connects">
                                </div>
                                @error('allowed_connects')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Set the maximum number of connects allowed</small>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.goals.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Update Goal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Current Progress</h6>
                </div>
                <div class="card-body">
                    @php
                        $today = now()->toDateString();
                        $todayProposalsCount = \App\Models\Proposal::where('user_id', $goal->user_id)
                            ->whereDate('submitted_at', $today)
                            ->count();
                        $progress = $goal->daily_goal > 0 ? min(100, round(($todayProposalsCount / $goal->daily_goal) * 100, 1)) : 0;
                    @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Today's Progress</span>
                            <span>{{ $todayProposalsCount }}/{{ $goal->daily_goal }}</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-primary" style="width: {{ $progress }}%"></div>
                        </div>
                        <small class="text-muted">{{ $progress }}% completed</small>
                    </div>
                    <div class="text-center">
                        @if($todayProposalsCount >= $goal->daily_goal)
                            <span class="badge bg-success fs-6">Goal Achieved!</span>
                        @else
                            <span class="badge bg-warning fs-6">{{ $goal->daily_goal - $todayProposalsCount }} more needed</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
