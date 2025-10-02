@extends('admin.layouts.master')

@section('title', 'Create Upwork Profile')
@section('page-title', 'Create Upwork Profile')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Create New Upwork Profile</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.upwork-profiles.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="profile_name" class="form-label">Profile Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('profile_name') is-invalid @enderror"
                                    id="profile_name" name="profile_name" value="{{ old('profile_name') }}" required>
                                @error('profile_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                                <select class="form-select @error('country') is-invalid @enderror"
                                    id="country" name="country" required>
                                    <option value="">Select a Country</option>
                                    <option value="Afghanistan" {{ old('country') == 'Afghanistan' ? 'selected' : '' }}>🇦🇫 Afghanistan</option>
                                    <option value="Albania" {{ old('country') == 'Albania' ? 'selected' : '' }}>🇦🇱 Albania</option>
                                    <option value="Algeria" {{ old('country') == 'Algeria' ? 'selected' : '' }}>🇩🇿 Algeria</option>
                                    <option value="Argentina" {{ old('country') == 'Argentina' ? 'selected' : '' }}>🇦🇷 Argentina</option>
                                    <option value="Australia" {{ old('country') == 'Australia' ? 'selected' : '' }}>🇦🇺 Australia</option>
                                    <option value="Austria" {{ old('country') == 'Austria' ? 'selected' : '' }}>🇦🇹 Austria</option>
                                    <option value="Bangladesh" {{ old('country') == 'Bangladesh' ? 'selected' : '' }}>🇧🇩 Bangladesh</option>
                                    <option value="Belgium" {{ old('country') == 'Belgium' ? 'selected' : '' }}>🇧🇪 Belgium</option>
                                    <option value="Brazil" {{ old('country') == 'Brazil' ? 'selected' : '' }}>🇧🇷 Brazil</option>
                                    <option value="Canada" {{ old('country') == 'Canada' ? 'selected' : '' }}>🇨🇦 Canada</option>
                                    <option value="Chile" {{ old('country') == 'Chile' ? 'selected' : '' }}>🇨🇱 Chile</option>
                                    <option value="China" {{ old('country') == 'China' ? 'selected' : '' }}>🇨🇳 China</option>
                                    <option value="Colombia" {{ old('country') == 'Colombia' ? 'selected' : '' }}>🇨🇴 Colombia</option>
                                    <option value="Denmark" {{ old('country') == 'Denmark' ? 'selected' : '' }}>🇩🇰 Denmark</option>
                                    <option value="Egypt" {{ old('country') == 'Egypt' ? 'selected' : '' }}>🇪🇬 Egypt</option>
                                    <option value="France" {{ old('country') == 'France' ? 'selected' : '' }}>🇫🇷 France</option>
                                    <option value="Germany" {{ old('country') == 'Germany' ? 'selected' : '' }}>🇩🇪 Germany</option>
                                    <option value="Greece" {{ old('country') == 'Greece' ? 'selected' : '' }}>🇬🇷 Greece</option>
                                    <option value="India" {{ old('country') == 'India' ? 'selected' : '' }}>🇮🇳 India</option>
                                    <option value="Indonesia" {{ old('country') == 'Indonesia' ? 'selected' : '' }}>🇮🇩 Indonesia</option>
                                    <option value="Iran" {{ old('country') == 'Iran' ? 'selected' : '' }}>🇮🇷 Iran</option>
                                    <option value="Iraq" {{ old('country') == 'Iraq' ? 'selected' : '' }}>🇮🇶 Iraq</option>
                                    <option value="Ireland" {{ old('country') == 'Ireland' ? 'selected' : '' }}>🇮🇪 Ireland</option>
                                    <option value="Israel" {{ old('country') == 'Israel' ? 'selected' : '' }}>🇮🇱 Israel</option>
                                    <option value="Italy" {{ old('country') == 'Italy' ? 'selected' : '' }}>🇮🇹 Italy</option>
                                    <option value="Japan" {{ old('country') == 'Japan' ? 'selected' : '' }}>🇯🇵 Japan</option>
                                    <option value="Jordan" {{ old('country') == 'Jordan' ? 'selected' : '' }}>🇯🇴 Jordan</option>
                                    <option value="Kenya" {{ old('country') == 'Kenya' ? 'selected' : '' }}>🇰🇪 Kenya</option>
                                    <option value="Malaysia" {{ old('country') == 'Malaysia' ? 'selected' : '' }}>🇲🇾 Malaysia</option>
                                    <option value="Mexico" {{ old('country') == 'Mexico' ? 'selected' : '' }}>🇲🇽 Mexico</option>
                                    <option value="Morocco" {{ old('country') == 'Morocco' ? 'selected' : '' }}>🇲🇦 Morocco</option>
                                    <option value="Netherlands" {{ old('country') == 'Netherlands' ? 'selected' : '' }}>🇳🇱 Netherlands</option>
                                    <option value="New Zealand" {{ old('country') == 'New Zealand' ? 'selected' : '' }}>🇳🇿 New Zealand</option>
                                    <option value="Nigeria" {{ old('country') == 'Nigeria' ? 'selected' : '' }}>🇳🇬 Nigeria</option>
                                    <option value="Norway" {{ old('country') == 'Norway' ? 'selected' : '' }}>🇳🇴 Norway</option>
                                    <option value="Pakistan" {{ old('country') == 'Pakistan' ? 'selected' : '' }}>🇵🇰 Pakistan</option>
                                    <option value="Peru" {{ old('country') == 'Peru' ? 'selected' : '' }}>🇵🇪 Peru</option>
                                    <option value="Philippines" {{ old('country') == 'Philippines' ? 'selected' : '' }}>🇵🇭 Philippines</option>
                                    <option value="Poland" {{ old('country') == 'Poland' ? 'selected' : '' }}>🇵🇱 Poland</option>
                                    <option value="Portugal" {{ old('country') == 'Portugal' ? 'selected' : '' }}>🇵🇹 Portugal</option>
                                    <option value="Romania" {{ old('country') == 'Romania' ? 'selected' : '' }}>🇷🇴 Romania</option>
                                    <option value="Russia" {{ old('country') == 'Russia' ? 'selected' : '' }}>🇷🇺 Russia</option>
                                    <option value="Saudi Arabia" {{ old('country') == 'Saudi Arabia' ? 'selected' : '' }}>🇸🇦 Saudi Arabia</option>
                                    <option value="Singapore" {{ old('country') == 'Singapore' ? 'selected' : '' }}>🇸🇬 Singapore</option>
                                    <option value="South Africa" {{ old('country') == 'South Africa' ? 'selected' : '' }}>🇿🇦 South Africa</option>
                                    <option value="South Korea" {{ old('country') == 'South Korea' ? 'selected' : '' }}>🇰🇷 South Korea</option>
                                    <option value="Spain" {{ old('country') == 'Spain' ? 'selected' : '' }}>🇪🇸 Spain</option>
                                    <option value="Sweden" {{ old('country') == 'Sweden' ? 'selected' : '' }}>🇸🇪 Sweden</option>
                                    <option value="Switzerland" {{ old('country') == 'Switzerland' ? 'selected' : '' }}>🇨🇭 Switzerland</option>
                                    <option value="Thailand" {{ old('country') == 'Thailand' ? 'selected' : '' }}>🇹🇭 Thailand</option>
                                    <option value="Turkey" {{ old('country') == 'Turkey' ? 'selected' : '' }}>🇹🇷 Turkey</option>
                                    <option value="Ukraine" {{ old('country') == 'Ukraine' ? 'selected' : '' }}>🇺🇦 Ukraine</option>
                                    <option value="United Arab Emirates" {{ old('country') == 'United Arab Emirates' ? 'selected' : '' }}>🇦🇪 United Arab Emirates</option>
                                    <option value="United Kingdom" {{ old('country') == 'United Kingdom' ? 'selected' : '' }}>🇬🇧 United Kingdom</option>
                                    <option value="United States" {{ old('country') == 'United States' ? 'selected' : '' }}>🇺🇸 United States</option>
                                    <option value="Venezuela" {{ old('country') == 'Venezuela' ? 'selected' : '' }}>🇻🇪 Venezuela</option>
                                    <option value="Vietnam" {{ old('country') == 'Vietnam' ? 'selected' : '' }}>🇻🇳 Vietnam</option>
                                </select>
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                    id="username" name="username" value="{{ old('username') }}" required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Username will be encrypted before storing</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" value="{{ old('password') }}" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Password will be encrypted before storing</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Assign to BDs</label>

                            <!-- Search and Controls -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="input-group" style="max-width: 300px;">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" id="bdSearch"
                                        placeholder="Search BD users...">
                                </div>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="selectAll">
                                        <i class="fas fa-check-square me-1"></i>Select All
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="selectNone">
                                        <i class="fas fa-square me-1"></i>None
                                    </button>
                                </div>
                            </div>

                            <!-- BD Users Grid -->
                            <div class="bd-users-container"
                                style="max-height: 220px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 10px 25px;">
                                <div class="row g-2">
                                    @php $breakpointCount = 0; @endphp
                                    @forelse($bdUsers as $bd)
                                        @if ($breakpointCount > 0 && $breakpointCount % 4 == 0)
                                </div>
                                <div class="row g-2">
                                    @endif
                                    <div class="col-md-6">
                                        <div class="bd-user-card" data-bd-name="{{ strtolower($bd->name) }}"
                                            data-bd-email="{{ strtolower($bd->email ?? '') }}">
                                            <div class="form-check d-flex align-items-center p-2 rounded hover-bg-light"
                                                style="cursor: pointer; transition: all 0.2s ease; min-height: 80px;">
                                                <input class="form-check-input me-3 bd-checkbox" type="checkbox"
                                                    id="bd_{{ $bd->id }}" name="assigned_bd_ids[]"
                                                    value="{{ $bd->id }}"
                                                    {{ in_array($bd->id, old('assigned_bd_ids', [])) ? 'checked' : '' }}
                                                    style="transform: scale(1.2); flex-shrink: 0;">
                                                <label class="form-check-label d-flex align-items-center flex-grow-1 w-100"
                                                    for="bd_{{ $bd->id }}"
                                                    style="cursor: pointer; margin-bottom: 0;">
                                                    <div class="d-flex align-items-center w-100">
                                                        <div class="bd-avatar me-3" style="flex-shrink: 0;">
                                                            <x-avatar :user="$bd" :size="40" />
                                                        </div>
                                                        <div class="bd-info flex-grow-1">
                                                            <div class="bd-name fw-bold">{{ $bd->name }}</div>
                                                            <small
                                                                class="bd-email text-muted small">{{ $bd->email }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    @php $breakpointCount++; @endphp
                                @empty
                                    <div class="col-12">
                                        <div class="text-center py-4">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No BD Users Available</h5>
                                            <p class="text-muted">There are no Business Developer users in the system yet.
                                            </p>
                                        </div>
                                    </div>
                                    @endforelse
                                </div>
                            </div>

                            @error('assigned_bd_ids')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Select one or more BD users to assign this profile to. Selected users
                                will have access to this Upwork profile.</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.upwork-profiles.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back to Profiles
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Create Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Information</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Profile Name:</strong> A descriptive name for this Upwork profile</p>
                    <p class="mb-2"><strong>Country:</strong> The country associated with this profile</p>
                    <p class="mb-2"><strong>Username & Password:</strong> These will be encrypted before storing in the
                        database</p>
                    <p class="mb-0"><strong>Assigned BDs:</strong> Select which BD users should have access to this
                        profile</p>
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Security Note</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-lock me-2"></i>
                        <strong>Encrypted Fields:</strong> Username and password are encrypted using Laravel's built-in
                        encryption and cannot be decrypted once stored.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Search functionality
            const searchInput = document.getElementById('bdSearch');
            const bdCards = document.querySelectorAll('.bd-user-card');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();

                    bdCards.forEach(card => {
                        const name = card.dataset.bdName;
                        const email = card.dataset.bdEmail;

                        if (name.includes(searchTerm) || email.includes(searchTerm)) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            }

            // Select All functionality
            const selectAllBtn = document.getElementById('selectAll');
            const selectNoneBtn = document.getElementById('selectNone');
            const checkboxes = document.querySelectorAll('.bd-checkbox');

            if (selectAllBtn) {
                selectAllBtn.addEventListener('click', function() {
                    checkboxes.forEach(checkbox => {
                        const card = checkbox.closest('.bd-user-card');
                        if (card && card.style.display !== 'none') {
                            checkbox.checked = true;
                        }
                    });
                });
            }

            if (selectNoneBtn) {
                selectNoneBtn.addEventListener('click', function() {
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                });
            }

            // Click card to toggle checkbox
            bdCards.forEach(card => {
                card.addEventListener('click', function(e) {
                    // Don't trigger if clicking on the checkbox itself or its label
                    if (e.target.type === 'checkbox' || e.target.closest('label')) return;

                    const checkbox = this.querySelector('.bd-checkbox');
                    if (checkbox && this.style.display !== 'none') {
                        checkbox.checked = !checkbox.checked;
                    }
                });
            });
        });
    </script>

    <style>
        .hover-bg-light:hover {
            background-color: rgba(0, 0, 0, 0.02) !important;
        }

        .bd-user-card {
            transition: all 0.2s ease;
        }

        .bd-user-card:hover {
            transform: translateY(-1px);
        }

        .bd-avatar {
            flex-shrink: 0;
        }

        .bd-info {
            min-width: 0;
            /* Allow text to wrap */
            flex: 1;
        }

        .bd-name {
            font-size: 0.95rem;
            margin-bottom: 0.1rem;
            word-break: break-word;
        }

        .bd-email {
            font-size: 0.8rem;
            line-height: 1.2;
            word-break: break-word;
        }

        /* Ensure consistent card heights in the grid */
        .bd-user-card {
            height: 100%;
        }

        .bd-user-card .form-check {
            height: 100%;
        }

        /* Custom scrollbar for the container */
        .bd-users-container::-webkit-scrollbar {
            width: 6px;
        }

        .bd-users-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 0.375rem;
        }

        .bd-users-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 0.375rem;
        }

        .bd-users-container::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
@endsection
