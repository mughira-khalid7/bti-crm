@extends(auth()->user()->role === 'admin' ? 'admin.layouts.master' : 'bd.layouts.master')

@section('title', 'Profile - BD CRM')
@section('page-title', 'My Profile')

@section('content')
    @if (session('status') === 'profile-updated')
        <script>window.toast && window.toast.success('Success!', '{{ session('success') ?? 'Profile updated successfully!' }}');</script>
    @elseif (session('status') === 'password-updated')
        <script>window.toast && window.toast.success('Success!', '{{ session('success') ?? 'Password updated successfully!' }}');</script>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Profile Information</h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-key me-2"></i>Update Password</h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Danger Zone</h6>
                </div>
                <div class="card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
@endsection
