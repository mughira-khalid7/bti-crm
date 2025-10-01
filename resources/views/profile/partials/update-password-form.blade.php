<form method="post" action="{{ route('password.update') }}">
    @csrf
    @method('put')

    <div class="mb-3">
        <label for="update_password_current_password" class="form-label fw-semibold">Current Password</label>
        <input id="update_password_current_password" name="current_password" type="password" class="form-control {{ $errors->updatePassword->has('current_password') ? 'is-invalid' : '' }}" autocomplete="current-password">
        @if ($errors->updatePassword->has('current_password'))
            <div class="invalid-feedback">{{ $errors->updatePassword->first('current_password') }}</div>
        @endif
    </div>

    <div class="mb-3">
        <label for="update_password_password" class="form-label fw-semibold">New Password</label>
        <input id="update_password_password" name="password" type="password" class="form-control {{ $errors->updatePassword->has('password') ? 'is-invalid' : '' }}" autocomplete="new-password">
        @if ($errors->updatePassword->has('password'))
            <div class="invalid-feedback">{{ $errors->updatePassword->first('password') }}</div>
        @endif
    </div>

    <div class="mb-3">
        <label for="update_password_password_confirmation" class="form-label fw-semibold">Confirm Password</label>
        <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control {{ $errors->updatePassword->has('password_confirmation') ? 'is-invalid' : '' }}" autocomplete="new-password">
        @if ($errors->updatePassword->has('password_confirmation'))
            <div class="invalid-feedback">{{ $errors->updatePassword->first('password_confirmation') }}</div>
        @endif
    </div>

    <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i> Save
        </button>
    </div>
</form>
