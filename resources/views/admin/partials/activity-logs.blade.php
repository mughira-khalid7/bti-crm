@forelse($actionLogs as $log)
    <div class="activity-item d-flex align-items-start p-0 py-2 px-3 border-bottom hover-bg-light">
        <div class="flex-shrink-0 me-3">
            @if($log->user)
                <x-avatar :user="$log->user" :size="40" class="shadow-sm" />
            @else
                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="fas fa-user-slash text-white"></i>
                </div>
            @endif
        </div>
        <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="mb-1 fw-bold" style="font-size: 0.875rem;">
                        {{ $log->user ? $log->user->name : 'Deleted User' }}
                        @if($log->user && $log->user->trashed())
                            <span class="badge bg-danger ms-1" style="font-size: 0.65rem;">Deactivated</span>
                        @endif
                    </h6>
                    <p class="mb-1 text-dark" style="font-size: 0.813rem;">{{ $log->description }}</p>
                    <small class="text-muted" style="font-size: 0.75rem;">
                        <i class="fas fa-tag me-1"></i>{{ ucfirst(str_replace('_', ' ', $log->action)) }}
                    </small>
                </div>
                <small class="text-muted text-nowrap ms-2" style="font-size: 0.75rem;">
                    {{ $log->created_at->diffForHumans(null, true) }}
                </small>
            </div>
        </div>
    </div>
@empty
    <div class="text-center text-muted py-5">
        <i class="fas fa-inbox fa-3x mb-3 d-block opacity-25"></i>
        <p class="mb-0">No activity found for the selected date</p>
    </div>
@endforelse
