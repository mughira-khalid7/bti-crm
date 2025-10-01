@props([
    'user' => null,
    'size' => 40, // px
    'class' => '',
    'title' => null,
])

@php
    $user = $user ?: auth()->user();
    $sizePx = (int) $size;
    $style = "width:{$sizePx}px; height:{$sizePx}px; object-fit:cover;";
    $alt = $user?->name ? $user->name . " avatar" : 'User avatar';
    $titleAttr = $title ?? ($user?->name ?? 'User');
@endphp

@if($user && $user->avatar_url)
    <img src="{{ $user->avatar_url }}" alt="{{ $alt }}" title="{{ $titleAttr }}" class="rounded-circle {{ $class }}" style="{{ $style }}">
@else
    <div class="avatar-dynamic rounded-circle d-inline-flex align-items-center justify-content-center fw-bold text-white {{ $class }}" style="{{ $style }}" title="{{ $titleAttr }}">
        {{ $user?->initials ?: 'U' }}
    </div>
@endif


