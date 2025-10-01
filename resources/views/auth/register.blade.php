@extends('layouts.auth')

@section('content')
<div class="auth-header">
    <a href="{{ route('dashboard') }}" class="auth-logo">
        <img src="{{ asset('bti logo 2.png') }}" alt="BTI Logo">
    </a>
    <p class="auth-subtitle">Create an Account</p>
</div>

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('register') }}">
    @csrf

    <!-- Name -->
    <div class="mb-3">
        <label for="name" class="form-label">Full Name</label>
        <input
            id="name"
            type="text"
            class="form-control @error('name') is-invalid @enderror"
            name="name"
            value="{{ old('name') }}"
            required
            autofocus
            autocomplete="name"
            placeholder="Enter your full name"
        >
        @error('name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

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
            autocomplete="username"
            placeholder="your.email@browntech.co"
        >
        <div class="form-text">Must end with @browntech.co for Business Developer accounts</div>
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
            autocomplete="new-password"
            placeholder="Minimum 8 characters"
        >
        @error('password')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <!-- Confirm Password -->
    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input
            id="password_confirmation"
            type="password"
            class="form-control @error('password_confirmation') is-invalid @enderror"
            name="password_confirmation"
            required
            autocomplete="new-password"
            placeholder="Confirm your password"
        >
        @error('password_confirmation')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <!-- Hidden Role Field -->
    <input type="hidden" name="role" value="bd">

    <!-- Register Button -->
    <div class="d-grid mb-4">
        <button type="submit" class="btn btn-primary btn-lg">
            Create Account
        </button>
    </div>

    <!-- Login Link -->
    <div class="text-center">
        <p class="text-muted mb-0">
            Already have an account?
            <a href="{{ route('login') }}" class="auth-link">Login</a>
        </p>
    </div>

    <!-- Terms Notice -->
    <div class="text-center mt-3">
        <small class="text-muted">
            By creating an account, you agree to our Terms of Service and Privacy Policy.
        </small>
    </div>
</form>
@endsection
