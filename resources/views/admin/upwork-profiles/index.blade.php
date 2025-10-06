@extends('admin.layouts.master')

@section('title', 'Upwork Profiles')
@section('page-title', 'Upwork Profiles')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-user-circle me-2"></i>Upwork Profiles</h5>
            <a href="{{ route('admin.upwork-profiles.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Add New Profile
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Profile Name</th>
                            <th>Country</th>
                            <th>Username</th>
                            <th>Password</th>
                            <th>Assigned BDs</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($profiles as $profile)
                            <tr>
                                <td>{{ $profiles->firstItem() + $loop->index }}</td>
                                <td>
                                    <a href="{{ route('admin.upwork-profiles.show', $profile) }}" class="fw-semibold text-decoration-none">
                                        {{ $profile->profile_name }}
                                    </a>
                                </td>
                                <td>{{ $profile->country }}</td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">********</code>
                                </td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">********</code>
                                </td>
                                <td>
                                    @if($profile->assignedBds->count() > 0)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($profile->assignedBds as $bd)
                                                <span class="badge bg-primary">{{ $bd->name }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">No BDs assigned</span>
                                    @endif
                                </td>
                                <td>{{ $profile->created_at->format('M d, Y') }}</td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.upwork-profiles.show', $profile) }}"
                                           class="btn btn-sm btn-outline-info"
                                           title="View Profile">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.upwork-profiles.edit', $profile) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Edit Profile">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.upwork-profiles.destroy', $profile) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Delete Profile"
                                                    onclick="return confirm('Are you sure you want to delete this profile?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-user-circle fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No Upwork Profiles Found</h5>
                                        <p class="text-muted">Get started by creating your first Upwork profile.</p>
                                        <a href="{{ route('admin.upwork-profiles.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-1"></i> Add New Profile
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($profiles->hasPages())
            <div class="card-footer">
                {{ $profiles->links() }}
            </div>
        @endif
    </div>
@endsection
