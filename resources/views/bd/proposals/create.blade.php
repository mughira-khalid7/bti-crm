@extends('bd.layouts.master')

@section('title', 'Add Proposal')
@section('page-title', 'Add New Proposal')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Add New Proposal</h5>
            <a href="{{ route('bd.proposals.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('bd.proposals.store') }}" id="create-proposal-form" class="row g-3">
                @csrf
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" required placeholder="Enter proposal title">
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Connects Used <span class="text-danger">*</span></label>
                    <input type="number" name="connects_used" id="connects_used" value="{{ old('connects_used') }}" class="form-control @error('connects_used') is-invalid @enderror" min="0" required placeholder="0">
                    <small id="remaining_hint" class="form-text text-muted"></small>
                    @error('connects_used')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Submitted At <span class="text-danger">*</span></label>
                    <input type="date" name="submitted_at" id="submitted_at" value="{{ old('submitted_at', now()->toDateString()) }}" class="form-control @error('submitted_at') is-invalid @enderror" required>
                    @error('submitted_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Job URL <span class="text-danger">*</span></label>
                    <input type="url" name="url" value="{{ old('url') }}" class="form-control @error('url') is-invalid @enderror" required placeholder="https://www.upwork.com/nx/proposals/...">
                    @error('url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Enter a valid Upwork URL (e.g., https://www.upwork.com/nx/proposals/...)</small>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Job Description <span class="text-danger">*</span></label>
                    <textarea name="job_description" rows="6" class="form-control @error('job_description') is-invalid @enderror" required placeholder="Enter detailed job description">{{ old('job_description') }}</textarea>
                    @error('job_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Notes</label>
                    <textarea name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror" placeholder="Add any additional notes or comments">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Optional: Add any relevant notes about this proposal</small>
                </div>
                <div class="col-12">
                    <hr class="my-3">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('bd.proposals.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-save me-1"></i> Submit Proposal
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
        const form = document.getElementById('create-proposal-form');
        const dateInput = document.getElementById('submitted_at');
        const connectsInput = document.getElementById('connects_used');
        const hint = document.getElementById('remaining_hint');

        async function refreshRemaining() {
            if (!dateInput) return;
            try {
                const url = `${window.APP_URL ?? ''}/bd/proposals/remaining-connects?date=${encodeURIComponent(dateInput.value)}`;
                const res = await fetch(url, { credentials: 'same-origin' });
                const data = await res.json();
                const today = new Date().toISOString().slice(0,10);
                if (data && data.allowed !== null && dateInput.value === today) {
                    connectsInput.max = Math.max(data.remaining, 0);
                    const remaining = data.remaining;
                    const used = data.used;
                    hint.textContent = `Remaining today: ${remaining} of ${data.allowed} (used: ${used})`;
                    hint.style.display = '';
                } else {
                    connectsInput.removeAttribute('max');
                    hint.textContent = '';
                    hint.style.display = 'none';
                }
            } catch (e) {
                // Fallback silently
            }
        }

        if (form) {
            submitFormWithToast(form, 'Proposal created successfully!', 'Failed to create proposal.');
        }
        refreshRemaining();
        dateInput?.addEventListener('change', refreshRemaining);
    });
</script>
@endsection


