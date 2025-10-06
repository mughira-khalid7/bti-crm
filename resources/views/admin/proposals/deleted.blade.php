@extends('admin.layouts.master')

@section('title', 'Deleted Proposals')
@section('page-title', 'Deleted Proposals')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-trash-alt me-2"></i>Admin-Deleted Proposals</h5>
            <a href="{{ route('admin.proposals.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i> Back to Proposals
            </a>
        </div>
        <div class="card-body">
            <div class="alert alert-info border-0 rounded-3 shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle fa-2x me-3"></i>
                    <div class="flex-grow-1">
                        <h6 class="alert-heading mb-1">Recovery Center</h6>
                        <p class="mb-0">
                            These proposals were deleted by admins and are hidden from all views.
                            You can restore them here if needed.
                        </p>
                    </div>
                </div>
            </div>

            @if($proposals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>BD</th>
                                <th>Connects</th>
                                <th>Status</th>
                                <th>Deleted At</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proposals as $proposal)
                                <tr class="table-danger opacity-75">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $proposal->title }}</div>
                                        <div class="text-muted small">{{ Str::limit($proposal->job_description, 80) }}</div>
                                    </td>
                                    <td>
                                        @if ($proposal->user)
                                            <div class="d-flex align-items-center">
                                                <x-avatar :user="$proposal->user" :size="32" class="me-2" />
                                                <span>
                                                    {{ $proposal->user->name }}
                                                    @if ($proposal->user->trashed())
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
                                        <span class="badge bg-danger">Deleted</span>
                                    </td>
                                    <td>{{ $proposal->deleted_at->format('M d, Y \a\t h:i A') }}</td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('admin.proposals.restore', $proposal) }}" class="d-inline">
                                            @csrf
                                            <button type="button" class="btn btn-sm btn-outline-success"
                                                onclick="confirmRestore('{{ $proposal->title }}')">
                                                <i class="fas fa-undo me-1"></i> Restore
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex flex-column align-items-center mt-4">
                    <x-pagination :paginator="$proposals" />
                </div>
            @else
                <div class="text-center text-muted py-5">
                    <i class="fas fa-inbox fa-3x mb-3 d-block opacity-25"></i>
                    <h5>No Deleted Proposals</h5>
                    <p class="mb-0">There are no admin-deleted proposals to restore.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function confirmRestore(title) {
            Swal.fire({
                title: 'Restore Proposal',
                text: `Are you sure you want to restore "${title}"? This will make it visible again in the proposals list.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, restore it!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Find the form and submit it
                    const form = document.querySelector('form[action*="restore"]');
                    if (form) {
                        form.submit();
                    }
                }
            });
        }
    </script>
@endsection
