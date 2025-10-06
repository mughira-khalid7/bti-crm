@extends('admin.layouts.master')

@section('title', 'Add Proposal')
@section('page-title', 'Add New Proposal')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Add New Proposal</h5>
            <a href="{{ route('admin.proposals.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.proposals.store') }}" id="admin-create-proposal-form" class="row g-3">
                @csrf
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Assign to BD <span class="text-danger">*</span></label>
                    <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                        <option value="">-- Select BD --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                    @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" required placeholder="Enter proposal title">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Connects Used <span class="text-danger">*</span></label>
                    <input type="number" name="connects_used" id="connects_used" value="{{ old('connects_used') }}" class="form-control @error('connects_used') is-invalid @enderror" min="0" required placeholder="0">
                    <small id="remaining_hint" class="form-text text-muted"></small>
                    @error('connects_used')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Submitted At <span class="text-danger">*</span></label>
                    <input type="date" name="submitted_at" id="submitted_at" value="{{ old('submitted_at', now()->toDateString()) }}" class="form-control @error('submitted_at') is-invalid @enderror" required>
                    @error('submitted_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Upwork Profile <span class="text-danger">*</span></label>
                    <select name="upwork_profile_id" class="form-control @error('upwork_profile_id') is-invalid @enderror" required>
                        <option value="">Select Upwork Profile</option>
                        @foreach($upworkProfiles as $profile)
                            <option value="{{ $profile->id }}" {{ old('upwork_profile_id') == $profile->id ? 'selected' : '' }}>
                                {{ $profile->profile_name }} ({{ $profile->country }})
                            </option>
                        @endforeach
                    </select>
                    @error('upwork_profile_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="form-text text-muted">Select the Upwork profile to use for this proposal</small>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Job URL <span class="text-danger">*</span></label>
                    <input type="url" name="url" value="{{ old('url') }}" class="form-control @error('url') is-invalid @enderror" required placeholder="https://www.upwork.com/nx/proposals/...">
                    @error('url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="form-text text-muted">Enter a valid Upwork URL (e.g., https://www.upwork.com/nx/proposals/...)</small>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Job Description <span class="text-danger">*</span></label>
                    <textarea name="job_description" rows="6" class="form-control @error('job_description') is-invalid @enderror" required placeholder="Enter detailed job description">{{ old('job_description') }}</textarea>
                    @error('job_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Notes</label>
                    <textarea name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror" placeholder="Add any additional notes or comments">{{ old('notes') }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <hr class="my-3">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.proposals.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times me-1"></i> Cancel</a>
                        <button class="btn btn-primary" type="submit"><i class="fas fa-save me-1"></i> Submit Proposal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('admin-create-proposal-form');
    const dateInput = document.getElementById('submitted_at');
    const connectsInput = document.getElementById('connects_used');
    const userSelect = document.getElementById('user_id');
    const hint = document.getElementById('remaining_hint');

    async function refreshRemaining(){
        if(!dateInput || !userSelect || !userSelect.value){ hint.textContent=''; hint.style.display='none'; return; }
        try{
            const params = new URLSearchParams({date: dateInput.value, user_id: userSelect.value});
            const res = await fetch(`{{ route('admin.proposals.remainingConnects') }}?${params.toString()}`);
            const data = await res.json();
            const today = new Date().toISOString().slice(0,10);
            if (data && data.allowed !== null && dateInput.value === today){
                connectsInput.max = Math.max(data.remaining, 0);
                hint.textContent = `Remaining today for selected BD: ${data.remaining} of ${data.allowed} (used: ${data.used})`;
                hint.style.display='';
            } else {
                connectsInput.removeAttribute('max');
                hint.textContent='';
                hint.style.display='none';
            }
        }catch(e){ /* silent */ }
    }

    if(form){ submitFormWithToast(form, 'Proposal created successfully!', 'Failed to create proposal.'); }
    refreshRemaining();
    dateInput?.addEventListener('change', refreshRemaining);
    userSelect?.addEventListener('change', refreshRemaining);
});
</script>
@endsection


