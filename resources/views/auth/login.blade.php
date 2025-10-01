@extends('layouts.auth')

@section('content')
<div class="auth-header">
    <a href="{{ route('dashboard') }}" class="auth-logo">
        <img src="{{ asset('bti logo 2.png') }}" alt="BTI Logo">
    </a>
    <p class="auth-subtitle">Sign In to Your Account</p>
</div>

@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf

    <!-- Email Address -->
    <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <input
            id="email"
            type="email"
            class="form-control @error('email') is-invalid @enderror"
            name="email"
            value="{{ old('email') }}"
            required
            autofocus
            autocomplete="username"
            placeholder="Enter your email"
        >
        @error('email')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <!-- Password -->
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input
            id="password"
            type="password"
            class="form-control @error('password') is-invalid @enderror"
            name="password"
            required
            autocomplete="current-password"
            placeholder="Enter your password"
        >
        @error('password')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <!-- Remember Me -->
    <div class="mb-3 form-check">
        <input
            type="checkbox"
            class="form-check-input"
            id="remember_me"
            name="remember"
            {{ old('remember') ? 'checked' : '' }}
        >
        <label class="form-check-label" for="remember_me">
            Remember me
        </label>
    </div>

    <!-- Login Button -->
    <div class="d-grid mb-4">
        <button type="submit" class="btn btn-primary btn-lg">
            Sign In
        </button>
    </div>

    {{-- <!-- Register Link -->
    <div class="text-center">
        <p class="text-muted mb-0">
            Don't have an account?
            <a href="{{ route('register') }}" class="auth-link">Register</a>
        </p>
    </div> --}}

    <!-- Forgot Password Link -->
    @if (Route::has('password.request'))
        <div class="text-center mt-3">
            <a href="{{ route('password.request') }}" class="auth-link">
                Forgot your password?
            </a>
        </div>
    @endif
</form>
@endsection
