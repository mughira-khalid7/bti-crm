@if(auth()->user()->role === 'bd')
    <div class="text-muted mb-3">
        <i class="fas fa-info-circle me-1"></i>
        When you deactivate your account, your data will be retained. You can contact an administrator to restore your account later.
    </div>
@else
    <div class="text-muted mb-3">
        <i class="fas fa-exclamation-triangle me-1"></i>
        Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.
    </div>
@endif

<button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
    <i class="fas fa-trash-alt me-1"></i> {{ auth()->user()->role === 'bd' ? 'Deactivate Account' : 'Delete Account' }}
</button>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteAccountModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ auth()->user()->role === 'bd' ? 'Deactivate Account' : 'Delete Account' }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    @if(auth()->user()->role === 'bd')
                        <p>Are you sure you want to deactivate your account? Your data will be retained and can be restored by an administrator.</p>
                    @else
                        <p>Are you sure you want to permanently delete your account? This action cannot be undone.</p>
                    @endif

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Confirm with your password:</label>
                        <input
                            type="password"
                            class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                            id="password"
                            name="password"
                            required
                            placeholder="Enter your password"
                        >
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-1"></i> {{ auth()->user()->role === 'bd' ? 'Deactivate' : 'Delete' }} Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($errors->userDeletion->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteAccountModal'));
            deleteModal.show();
        });
    </script>
@endif
