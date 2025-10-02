@extends('admin.layouts.master')

@section('title', 'Goals Management - BD CRM')
@section('page-title', 'Goals Management')

@section('content')
    <div class="row g-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-bullseye me-2"></i>User Goals</h5>
                    <a href="{{ route('admin.goals.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Add Goal
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Daily Goal</th>
                                    <th>Allowed Connects</th>
                                    <th>Today's Progress</th>
                                    <th>Progress %</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($goals as $goal)
                                    @php
                                        $todayCount = $todayProposals[$goal->user_id] ?? 0;
                                        $progress =
                                            $goal->daily_goal > 0
                                                ? min(100, round(($todayCount / $goal->daily_goal) * 100, 1))
                                                : 0;
                                        $status =
                                            $todayCount >= $goal->daily_goal
                                                ? 'success'
                                                : ($progress >= 75
                                                    ? 'warning'
                                                    : 'danger');
                                    @endphp
                                    <tr>
                                        <td>
                                            @if ($goal->user)
                                                <div class="d-flex align-items-center">
                                                    <x-avatar :user="$goal->user" :size="32" class="me-2" />
                                                    <div>
                                                        <div class="fw-bold">
                                                            {{ $goal->user->name }}
                                                            @if ($goal->user->trashed())
                                                                <span class="badge bg-danger ms-1"
                                                                    style="font-size: 0.65rem;">Deactivated</span>
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
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $goal->daily_goal }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><i
                                                    class="fas fa-link me-1"></i>{{ $goal->allowed_connects ?? 0 }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $todayCount }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress me-2" style="width: 100px; height: 8px;">
                                                    <div class="progress-bar bg-{{ $status }}"
                                                        style="width: {{ $progress }}%"></div>
                                                </div>
                                                <small class="text-muted">{{ $progress }}%</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($todayCount >= $goal->daily_goal)
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($progress >= 75)
                                                <span class="badge bg-warning">Almost There</span>
                                            @else
                                                <span class="badge bg-danger">Needs Work</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.goals.show', $goal) }}" class="btn btn-outline-info"
                                                title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.goals.edit', $goal) }}"
                                                class="btn btn-outline-primary" title="Edit Goal">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.goals.destroy', $goal) }}"
                                                class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this goal?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Delete Goal">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-bullseye fa-3x mb-3 d-block"></i>
                                            No goals set yet. <a href="{{ route('admin.goals.create') }}">Create the first
                                                goal</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
