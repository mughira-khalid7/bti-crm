@extends('admin.layouts.master')

@section('title', 'Add BD User')
@section('page-title', 'Add New Business Developer')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Add New Business Developer</h5>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.store') }}" method="POST" id="create-user-form" class="row g-3" enctype="multipart/form-data">
                @csrf
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" required value="{{ old('name') }}" placeholder="Enter full name">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="form-control @error('email') is-invalid @enderror" required placeholder="Enter email address">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required placeholder="Enter password">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                            <i class="fas fa-eye" id="password-toggle"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Password must be at least 8 characters long</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Profile Picture (optional)</label>
                    <input type="file" name="avatar" accept="image/*" class="form-control @error('avatar') is-invalid @enderror">
                    @error('avatar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">JPG, PNG, or WEBP up to 10MB.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror">
                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                            <i class="fas fa-save me-1"></i> Create BD User
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
        const form = document.getElementById('create-user-form');
        if (form) {
            submitFormWithToast(form, 'BD user created successfully!', 'Failed to create BD user.');
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
