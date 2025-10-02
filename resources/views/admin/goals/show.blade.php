@extends('admin.layouts.master')

@section('title', 'Goal Details - BD CRM')
@section('page-title', 'Goal Details')

@section('content')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-bullseye me-2"></i>Goal Details</h5>
                    <div class="btn-group">
                        <a href="{{ route('admin.goals.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                        <a href="{{ route('admin.goals.edit', $goal) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            @if ($goal->user)
                                <div class="d-flex align-items-center mb-3">
                                    <x-avatar :user="$goal->user" :size="48" class="me-3" />
                                    <div>
                                        <h6 class="mb-0">
                                            {{ $goal->user->name }}
                                            @if ($goal->user->trashed())
                                                <span class="badge bg-danger ms-1"
                                                    style="font-size: 0.65rem;">Deactivated</span>
                                            @endif
                                        </h6>
                                        <small class="text-muted">{{ $goal->user->email }}</small>
                                    </div>
                                </div>
                            @else
                                <div class="text-muted mb-3">
                                    <i class="fas fa-user-slash me-2"></i>Deleted User
                                </div>
                            @endif
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-primary mb-0">{{ $goal->daily_goal }}</h4>
                                <small class="text-muted">Daily Target</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-info mb-0"><i
                                        class="fas fa-link me-1"></i>{{ $goal->allowed_connects ?? 0 }}</h4>
                                <small class="text-muted">Allowed Connects</small>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="text-center">
                                <h5 class="text-success mb-1">{{ $todayProposalsCount }}</h5>
                                <small class="text-muted">Today's Submissions</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h5 class="text-warning mb-1">{{ $bidsLeft }}</h5>
                                <small class="text-muted">Remaining</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                @php
                                    $progress =
                                        $dailyGoal > 0
                                            ? min(100, round(($todayProposalsCount / $dailyGoal) * 100, 1))
                                            : 0;
                                @endphp
                                <h5 class="text-info mb-1">{{ $progress }}%</h5>
                                <small class="text-muted">Progress</small>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Progress Bar</span>
                            <span>{{ $todayProposalsCount }}/{{ $dailyGoal }}</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-primary" style="width: {{ $progress }}%">
                                {{ $progress }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Status</h6>
                </div>
                <div class="card-body text-center">
                    @if ($todayProposalsCount >= $dailyGoal)
                        <div class="mb-3">
                            <i class="fas fa-trophy fa-3x text-success mb-3"></i>
                            <h5 class="text-success">Goal Achieved!</h5>
                            <p class="text-muted">Congratulations! The daily target has been reached.</p>
                        </div>
                    @elseif($progress >= 75)
                        <div class="mb-3">
                            <i class="fas fa-fire fa-3x text-warning mb-3"></i>
                            <h5 class="text-warning">Almost There!</h5>
                            <p class="text-muted">Great progress! Just {{ $bidsLeft }} more to go.</p>
                        </div>
                    @else
                        <div class="mb-3">
                            <i class="fas fa-clock fa-3x text-danger mb-3"></i>
                            <h5 class="text-danger">Needs Work</h5>
                            <p class="text-muted">{{ $bidsLeft }} more submissions needed to reach the goal.</p>
                        </div>
                    @endif

                    <div class="mt-4">
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>
                            Last updated: {{ $goal->updated_at->diffForHumans() }}
                        </small>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.goals.edit', $goal) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> Edit Goal
                        </a>
                        <form method="POST" action="{{ route('admin.goals.destroy', $goal) }}"
                            onsubmit="return confirm('Are you sure you want to delete this goal?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="fas fa-trash me-1"></i> Delete Goal
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
