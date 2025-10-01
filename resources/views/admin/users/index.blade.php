@extends('admin.layouts.master')

@section('title', 'Manage BDs')
@section('page-title', 'Business Developers')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-users me-2"></i>Business Developers</h5>
            <div class="d-flex gap-2">
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-filter me-1"></i>
                        @if(request('show_deleted') === 'only')
                            Deactivated Only
                        @elseif(request('show_deleted') === 'with')
                            All (Active & Deactivated)
                        @else
                            Active Only
                        @endif
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item {{ !request('show_deleted') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                            <i class="fas fa-check-circle me-2"></i>Active Only
                        </a></li>
                        <li><a class="dropdown-item {{ request('show_deleted') === 'only' ? 'active' : '' }}" href="{{ route('admin.users.index', ['show_deleted' => 'only']) }}">
                            <i class="fas fa-ban me-2"></i>Deactivated Only
                        </a></li>
                        <li><a class="dropdown-item {{ request('show_deleted') === 'with' ? 'active' : '' }}" href="{{ route('admin.users.index', ['show_deleted' => 'with']) }}">
                            <i class="fas fa-list me-2"></i>All (Active & Deactivated)
                        </a></li>
                    </ul>
                </div>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Add New BD
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Account Status</th>
                            <th>Proposals</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($user->avatar_url)
                                            <img src="{{ $user->avatar_url }}" alt="Avatar" class="me-2" style="width:32px; height:32px; border-radius:50%; object-fit:cover;">
                                        @else
                                            <div class="rounded-circle avatar-dynamic d-flex align-items-center justify-content-center me-2" data-bs-toggle="tooltip" title="{{ $user->name }}" style="width:32px; height:32px; font-size:.875rem;">
                                                {{ strtoupper(substr($user->name,0,2)) }}
                                            </div>
                                        @endif
                                        {{ $user->name }}
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($user->status) }}</span>
                                </td>
                                <td>
                                    @if($user->trashed())
                                        <span class="badge bg-danger">
                                            <i class="fas fa-ban me-1"></i>Deactivated
                                        </span>
                                    @else
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Active
                                        </span>
                                    @endif
                                </td>
                                <td><strong>{{ $user->proposals()->count() }}</strong></td>
                                <td class="text-end">
                                    @if($user->trashed())
                                        <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success me-1" title="Restore Account">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </form>
                                        <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete(this, '{{ route('admin.users.destroy', $user) }}', 'BD user permanently deleted!')" title="Permanently Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    @else
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit"><i class="fas fa-edit"></i></a>
                                        <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete(this, '{{ route('admin.users.destroy', $user) }}', 'BD user permanently deleted!')" title="Permanently Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No BD users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex flex-column align-items-center mt-4">
                <x-pagination :paginator="$users" />
            </div>
        </div>
    </div>
@endsection
