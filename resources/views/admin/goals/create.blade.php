@extends('admin.layouts.master')

@section('title', 'Create Goal - BD CRM')
@section('page-title', 'Create Goal')

@section('content')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Create New Goal</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.goals.store') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="user_id" class="form-label fw-semibold">Select User <span class="text-danger">*</span></label>
                                <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                    <option value="">Choose a user...</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="daily_goal" class="form-label fw-semibold">Daily Goal <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">#</span>
                                    <input type="number" name="daily_goal" id="daily_goal"
                                           class="form-control @error('daily_goal') is-invalid @enderror"
                                           value="{{ old('daily_goal') }}"
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
                                           value="{{ old('allowed_connects') }}"
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
                                        <i class="fas fa-save me-1"></i> Create Goal
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
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Goal Guidelines</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>Each user can have only one goal</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>Daily goal should be realistic (0-1000)</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>Set allowed connects per user</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>Progress is tracked daily</small>
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>Goals can be edited anytime</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
