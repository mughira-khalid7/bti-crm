@extends('bd.layouts.master')

@section('title', 'My Proposals')
@section('page-title', 'My Proposals')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>My Proposals</h5>
            <a href="{{ route('bd.proposals.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Add Proposal
            </a>
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
                        <option value="last_3_days" {{ request('date') == 'last_3_days' ? 'selected' : '' }}>Last 3 days
                        </option>
                        <option value="last_week" {{ request('date') == 'last_week' ? 'selected' : '' }}>Last week</option>
                        <option value="last_month" {{ request('date') == 'last_month' ? 'selected' : '' }}>Last month
                        </option>
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
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">-- Any --</option>
                        <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted
                        </option>
                        <option value="interviewing" {{ request('status') == 'interviewing' ? 'selected' : '' }}>
                            Interviewing
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
                            <th>Connects</th>
                            <th>URL</th>
                            <th>Notes</th>
                            <th>Submitted At</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proposals as $proposal)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <a href="{{ route('bd.proposals.show', $proposal) }}"
                                        class="fw-semibold text-decoration-none">{{ $proposal->title }}</a>
                                    <div class="text-muted small">{{ Str::limit($proposal->job_description, 80) }}</div>
                                </td>
                                <td><strong>{{ $proposal->connects_used }}</strong></td>
                                <td>
                                    <a href="{{ $proposal->url }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </td>
                                <td>{{ Str::limit($proposal->notes, 40) }}</td>
                                <td>{{ \Carbon\Carbon::parse($proposal->submitted_at)->format('M d, Y') }}</td>
                                <td>
                                    <span
                                        class="badge bg-{{ $proposal->status == 'interviewing' ? 'info' : 'secondary' }}">{{ ucfirst($proposal->status) }}</span>
                                </td>
                                <td class="text-end">
                                    @if ($proposal->status !== 'interviewing')
                                        <button class="btn btn-sm btn-outline-success"
                                            onclick="confirmMoveToInterviewing('{{ route('bd.proposals.moveToInterviewing', $proposal) }}', '{{ $proposal->title }}')">
                                            <i class="fas fa-arrow-right me-1"></i> Move to Interview
                                        </button>
                                    @endif
                                    <a href="{{ route('bd.proposals.show', $proposal) }}"
                                        class="btn btn-sm btn-outline-primary me-1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('bd.proposals.edit', $proposal) }}"
                                        class="btn btn-sm btn-outline-secondary me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('bd.proposals.destroy', $proposal) }}"
                                        class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="confirmDelete(this.closest('form'))">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No proposals found.</td>
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
        function confirmDelete(form) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to delete this proposal?',
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

        function confirmMoveToInterviewing(url, title) {
            Swal.fire({
                title: `Are you sure?`,
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
    </script>
@endsection
