@extends('admin.layouts.master')

@section('title', 'Edit BD User')
@section('page-title', 'Edit Business Developer')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Edit Business Developer</h5>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user) }}" method="POST" id="edit-user-form" class="row g-3" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" required placeholder="Enter full name">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Profile Picture (optional)</label>
                    <input type="file" name="avatar" accept="image/*" class="form-control @error('avatar') is-invalid @enderror">
                    @error('avatar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if($user->avatar)
                        <div class="mt-2">
                            <img src="{{ $user->avatar_url }}" alt="Current Avatar" class="rounded" style="height:56px; width:56px; object-fit:cover;">
                        </div>
                    @endif
                    <small class="text-muted">Upload to replace current picture. JPG, PNG, or WEBP up to 10MB.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" required placeholder="Enter email address">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">New Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Leave blank if not changing">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                            <i class="fas fa-eye" id="password-toggle"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Leave blank to keep current password</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror">
                        <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12">
                    <hr class="my-3">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-save me-1"></i> Update BD User
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Initialize form with toast notifications
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('edit-user-form');
        if (form) {
            submitFormWithToast(form, 'BD user updated successfully!', 'Failed to update BD user.');
        }
    });

    // Password toggle functionality
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const toggle = document.getElementById(inputId + '-toggle');

        if (input.type === 'password') {
            input.type = 'text';
            toggle.classList.remove('fa-eye');
            toggle.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            toggle.classList.remove('fa-eye-slash');
            toggle.classList.add('fa-eye');
        }
    }
</script>
@endsection
