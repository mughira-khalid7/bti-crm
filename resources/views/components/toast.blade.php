@props([
    'id' => 'toast-notification',
    'type' => 'success',
    'title' => 'Success',
    'message' => '',
    'duration' => 5000,
    'position' => 'top-end'
])

@php
    $typeClasses = [
        'success' => 'text-bg-success',
        'error' => 'text-bg-danger',
        'warning' => 'text-bg-warning',
        'info' => 'text-bg-info',
        'primary' => 'text-bg-primary'
    ];

    $typeIcons = [
        'success' => 'fas fa-check-circle',
        'error' => 'fas fa-exclamation-circle',
        'warning' => 'fas fa-exclamation-triangle',
        'info' => 'fas fa-info-circle',
        'primary' => 'fas fa-bell'
    ];

    $typeClass = $typeClasses[$type] ?? $typeClasses['success'];
    $typeIcon = $typeIcons[$type] ?? $typeIcons['success'];
@endphp

<div id="{{ $id }}" class="toast align-items-center {{ $typeClass }}" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="{{ $duration }}">
    <div class="d-flex">
        <div class="toast-body">
            <div class="d-flex align-items-center">
                <i class="{{ $typeIcon }} me-2"></i>
                <div>
                    <strong>{{ $title }}</strong>
                    @if($message)
                        <div class="small">{{ $message }}</div>
                    @endif
                </div>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
</div>
