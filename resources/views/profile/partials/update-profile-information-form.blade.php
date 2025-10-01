<form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
    @csrf
    @method('patch')

    <div class="mb-3">
        <label for="name" class="form-label fw-semibold">Name</label>
        <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autocomplete="name">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- <div class="mb-3">
        <label for="email" class="form-label fw-semibold">Email</label>
        <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div> --}}

    <div class="mb-3">
        <label for="avatar" class="form-label fw-semibold">Avatar</label>
        <div class="d-flex align-items-center gap-3">
            @if($user->avatar_url)
                <img src="{{ $user->avatar_url }}" alt="Avatar" style="height:48px; width:48px; border-radius:50%; object-fit:cover;">
            @else
                <div class="rounded-circle avatar-dynamic d-flex align-items-center justify-content-center" style="height:48px; width:48px;">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
            @endif
            <input id="avatar" name="avatar" type="file" class="form-control" accept="image/png,image/jpeg">
        </div>
        <small class="text-muted">PNG/JPG up to 2MB.</small>
        @error('avatar')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i> Save
        </button>
    </div>
</form>
