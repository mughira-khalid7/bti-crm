@extends('admin.layouts.master')

@section('title', 'All Proposals')
@section('page-title', 'Proposals Management')

@section('content')

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Proposals</h5>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary me-2">Back</a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
                    <i class="fas fa-users me-1"></i> Manage BDs
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Filter Form -->
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-2">
                    <label class="form-label">Quick Date</label>
                    <select name="date" class="form-select">
                        <option value="">-- Any --</option>
                        <option value="today" {{ request('date') == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="yesterday" {{ request('date') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                        <option value="last_3_days" {{ request('date') == 'last_3_days' ? 'selected' : '' }}>Last 3 days</option>
                        <option value="last_week" {{ request('date') == 'last_week' ? 'selected' : '' }}>Last week</option>
                        <option value="last_month" {{ request('date') == 'last_month' ? 'selected' : '' }}>Last month</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">From</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control" />
                </div>
                <div class="col-md-2">
                    <label class="form-label">To</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">User</label>
                    <select name="user_id" class="form-select">
                        <option value="">-- All BDs --</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})@if($user->trashed()) - Deactivated @endif</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">-- Any --</option>
                        <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="interviewing" {{ request('status') == 'interviewing' ? 'selected' : '' }}>Interviewing
                        </option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button class="btn btn-primary w-100">
                        Filter
                    </button>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>BD</th>
                            <th>Connects</th>
                            <th>Status</th>
                            <th>Submitted At</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proposals as $proposal)
                            <tr @if($proposal->deleted_at) class="table-danger opacity-75" @endif>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <a href="{{ route('admin.proposals.show', $proposal) }}"
                                        class="fw-semibold text-decoration-none">{{ $proposal->title }}</a>
                                    <div class="text-muted small">{{ Str::limit($proposal->job_description, 80) }}</div>
                                </td>
                                <td>
                                    @if($proposal->user)
                                        <div class="d-flex align-items-center">
                                            <x-avatar :user="$proposal->user" :size="32" class="me-2" />
                                            <span>
                                                {{ $proposal->user->name }}
                                                @if($proposal->user->trashed())
                                                    <span class="badge bg-danger ms-1" style="font-size: 0.65rem;">Deactivated</span>
                                                @endif
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-muted">Deleted User</span>
                                    @endif
                                </td>
                                <td><strong>{{ $proposal->connects_used }}</strong></td>
                                <td>
                                    <span
                                        class="badge bg-{{ $proposal->status == 'interviewing' ? 'info' : 'secondary' }}">{{ ucfirst($proposal->status) }}</span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($proposal->submitted_at)->format('M d, Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.proposals.show', $proposal) }}"
                                        class="btn btn-sm btn-outline-primary me-1 @if($proposal->deleted_at) disabled opacity-50 @endif">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if (!$proposal->deleted_at && $proposal->status !== 'interviewing')
                                        <button class="btn btn-sm btn-outline-success"
                                            onclick="confirmMoveToInterviewing('{{ route('admin.proposals.moveToInterviewing', $proposal) }}', '{{ $proposal->title }}')">
                                            <i class="fas fa-arrow-right"></i> Move to Interview
                                        </button>
                                    @endif
                                    @if (!$proposal->deleted_at)
                                        <form method="POST" action="{{ route('admin.proposals.destroy', $proposal) }}" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this proposal?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No proposals found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex flex-column align-items-center mt-4">
                <x-pagination :paginator="$proposals" />
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
