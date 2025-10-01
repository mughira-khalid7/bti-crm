@extends('admin.layouts.master')

@section('title', 'Admin Dashboard - BD CRM')
@section('page-title', 'Dashboard Overview')

@section('content')
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Dashboard Overview</h2>
                    <p class="text-muted mb-0">Welcome back! Here's what's happening with your business development.</p>
                </div>
                <div class="text-muted">
                    <i class="fas fa-calendar-alt me-2"></i>{{ now()->format('F j, Y') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics - Top Row -->
    <div class="row g-4 mb-4">
        <!-- Total Proposals -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 rounded-3 shadow-sm h-100 hover-lift">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="flex-grow-1">
                            <p class="text-muted text-uppercase mb-2 fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Proposals</p>
                            <h2 class="fw-bold mb-0 text-primary" style="font-size: 2rem;">{{ $totalProposals ?? 0 }}</h2>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 56px; height: 56px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-file-alt text-white" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Interviewing Leads -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 rounded-3 shadow-sm h-100 hover-lift">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="flex-grow-1">
                            <p class="text-muted text-uppercase mb-2 fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Interviewing</p>
                            <h2 class="fw-bold mb-0 text-info" style="font-size: 2rem;">{{ $interviewingCount ?? 0 }}</h2>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 56px; height: 56px; background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                            <i class="fas fa-video text-white" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- This Month -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 rounded-3 shadow-sm h-100 hover-lift">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="flex-grow-1">
                            <p class="text-muted text-uppercase mb-2 fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">This Month</p>
                            <h2 class="fw-bold mb-0 text-success" style="font-size: 2rem;">{{ $monthCount ?? 0 }}</h2>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 56px; height: 56px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                            <i class="fas fa-calendar-alt text-white" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total BDs -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 rounded-3 shadow-sm h-100 hover-lift">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="flex-grow-1">
                            <p class="text-muted text-uppercase mb-2 fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total BDs</p>
                            <h2 class="fw-bold mb-0 text-warning" style="font-size: 2rem;">{{ $users ?? 0 }}</h2>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 56px; height: 56px; background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);">
                            <i class="fas fa-users text-white" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Metrics -->
    <div class="row g-4 mb-4">
        <!-- Today -->
        <div class="col-xl-4 col-lg-4 col-md-6">
            <div class="card border-0 rounded-3 shadow-sm h-100 hover-lift">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3"
                            style="width: 48px; height: 48px;">
                            <i class="fas fa-calendar-day text-primary" style="font-size: 1.25rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted text-uppercase mb-1 fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Today</p>
                            <h3 class="fw-bold mb-0" style="font-size: 1.75rem;">{{ $todayCount ?? 0 }}</h3>
                            <p class="text-muted mb-0" style="font-size: 0.8rem;">New Proposals</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- This Week -->
        <div class="col-xl-4 col-lg-4 col-md-6">
            <div class="card border-0 rounded-3 shadow-sm h-100 hover-lift">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center me-3"
                            style="width: 48px; height: 48px;">
                            <i class="fas fa-calendar-week text-success" style="font-size: 1.25rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted text-uppercase mb-1 fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">This Week</p>
                            <h3 class="fw-bold mb-0" style="font-size: 1.75rem;">{{ $weekCount ?? 0 }}</h3>
                            <p class="text-muted mb-0" style="font-size: 0.8rem;">New Proposals</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active BDs -->
        <div class="col-xl-4 col-lg-4 col-md-6">
            <div class="card border-0 rounded-3 shadow-sm h-100 hover-lift">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center me-3"
                            style="width: 48px; height: 48px;">
                            <i class="fas fa-user-check text-warning" style="font-size: 1.25rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted text-uppercase mb-1 fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Active BDs</p>
                            <h3 class="fw-bold mb-0" style="font-size: 1.75rem;">{{ $active ?? 0 }}</h3>
                            <p class="text-muted mb-0" style="font-size: 0.8rem;">Currently Active</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Activity Section -->
    <div class="row g-4 mb-4">
        <!-- Proposals Over Time Chart -->
        <div class="col-lg-8">
            <div class="card border-0 rounded-3 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-chart-line me-2 text-primary"></i>Proposals Over Time
                        </h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <ul class="nav nav-pills mb-4" id="chartTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill px-4" id="daily-tab" data-bs-toggle="pill" data-bs-target="#daily"
                                type="button" role="tab">Daily</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill px-4" id="weekly-tab" data-bs-toggle="pill" data-bs-target="#weekly"
                                type="button" role="tab">Weekly</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill px-4" id="monthly-tab" data-bs-toggle="pill" data-bs-target="#monthly"
                                type="button" role="tab">Monthly</button>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="daily" role="tabpanel">
                            <div class="chart-container"><canvas id="dailyChart"></canvas></div>
                        </div>
                        <div class="tab-pane fade" id="weekly" role="tabpanel">
                            <div class="chart-container"><canvas id="weeklyChart"></canvas></div>
                        </div>
                        <div class="tab-pane fade" id="monthly" role="tabpanel">
                            <div class="chart-container"><canvas id="monthlyChart"></canvas></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-lg-4">
            <div class="card border-0 rounded-3 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-history me-2 text-info"></i>Recent Activity
                        </h5>
                        <span class="badge bg-primary rounded-pill px-3" id="activity-count">{{ $actionLogs->count() }}</span>
                    </div>
                    <!-- Date Filter -->
                    <div class="d-flex gap-2">
                        <select id="activity-date-filter" class="form-select form-select-sm" style="font-size: 0.813rem;">
                            <option value="">All Time</option>
                            <option value="today" selected>Today</option>
                            <option value="yesterday">Yesterday</option>
                            <option value="last_7_days">Last 7 Days</option>
                            <option value="last_30_days">Last 30 Days</option>
                            <option value="this_month">This Month</option>
                        </select>
                        <input type="date" id="activity-custom-date" class="form-control form-control-sm"
                               style="font-size: 0.813rem; max-width: 150px;"
                               placeholder="Custom Date">
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="activity-list" style="max-height: 450px; overflow-y: auto;" id="activity-list-container">
                        @include('admin.partials.activity-logs', ['actionLogs' => $actionLogs])
                    </div>
                </div>
                <!-- Loading Indicator -->
                <div class="position-absolute w-100 h-100 d-none align-items-center justify-content-center bg-white bg-opacity-75"
                     id="activity-loading" style="top: 0; left: 0; z-index: 10;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables Section -->
    <div class="row g-4 mb-4">
        <!-- Latest Business Developers -->
        <div class="col-lg-6">
            <div class="card border-0 rounded-3 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-users me-2 text-success"></i>Latest Business Developers
                        </h5>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">View All</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 py-3 px-4 fw-semibold text-muted" style="font-size: 0.813rem;">Name</th>
                                    <th class="border-0 py-3 fw-semibold text-muted" style="font-size: 0.813rem;">Email</th>
                                    <th class="border-0 py-3 fw-semibold text-muted" style="font-size: 0.813rem;">Status</th>
                                    <th class="border-0 py-3 fw-semibold text-muted text-center" style="font-size: 0.813rem;">Proposals</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestBdUsers as $u)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <x-avatar :user="$u" :size="36" class="me-3 shadow-sm" />
                                                <span class="fw-semibold" style="font-size: 0.875rem;">{{ $u->name }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <span class="text-muted" style="font-size: 0.875rem;">{{ $u->email }}</span>
                                        </td>
                                        <td class="py-3">
                                            <span class="badge bg-{{ $u->status === 'active' ? 'success' : 'secondary' }} rounded-pill px-3">
                                                {{ ucfirst($u->status) }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-center">
                                            <span class="fw-bold text-primary" style="font-size: 1rem;">{{ $u->proposals_count }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5">No BD users found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Latest Proposals -->
        <div class="col-lg-6">
            <div class="card border-0 rounded-3 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-file-alt me-2 text-primary"></i>Latest Proposals
                        </h5>
                        <a href="{{ route('admin.proposals.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">View All</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 py-3 px-4 fw-semibold text-muted" style="font-size: 0.813rem;">Title</th>
                                    <th class="border-0 py-3 fw-semibold text-muted" style="font-size: 0.813rem;">BD</th>
                                    <th class="border-0 py-3 fw-semibold text-muted" style="font-size: 0.813rem;">Status</th>
                                    <th class="border-0 py-3 fw-semibold text-muted" style="font-size: 0.813rem;">Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestProposals as $p)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="fw-semibold mb-1" style="font-size: 0.875rem;">{{ $p->title }}</div>
                                            <small class="text-muted" style="font-size: 0.75rem;">{{ Str::limit($p->job_description, 50) }}</small>
                                        </td>
                                        <td class="py-3">
                                            @if($p->user)
                                                <div class="d-flex align-items-center">
                                                    <x-avatar :user="$p->user" :size="32" class="me-2 shadow-sm" />
                                                    <span class="text-muted" style="font-size: 0.813rem;">
                                                        {{ $p->user->name }}
                                                        @if($p->user->trashed())
                                                            <span class="badge bg-danger ms-1" style="font-size: 0.6rem;">Deactivated</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            <span class="badge bg-{{ $p->status == 'interviewing' ? 'info' : 'secondary' }} rounded-pill px-3">
                                                {{ ucfirst($p->status) }}
                                            </span>
                                        </td>
                                        <td class="py-3">
                                            <small class="text-muted" style="font-size: 0.75rem;">
                                                {{ \Carbon\Carbon::parse($p->submitted_at)->diffForHumans() }}
                                            </small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5">No recent proposals</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Goals Overview Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 rounded-3 shadow-sm">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-bullseye me-2 text-danger"></i>Goals Overview
                        </h5>
                        <a href="{{ route('admin.goals.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Manage Goals</a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        @forelse($goalsOverview as $goal)
                            @php
                                $todayCount = $goal->today_proposals_count ?? 0;
                                $progress = $goal->daily_goal > 0 ? min(100, round(($todayCount / $goal->daily_goal) * 100, 1)) : 0;
                                $status = $todayCount >= $goal->daily_goal ? 'success' : ($progress >= 75 ? 'warning' : 'danger');
                            @endphp
                            <div class="col-xl-4 col-lg-6">
                                <div class="card border-0 bg-light h-100 hover-lift">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center mb-3">
                                            @if($goal->user)
                                                <x-avatar :user="$goal->user" :size="44" class="me-3 shadow-sm" />
                                                <div class="flex-grow-1">
                                                    <h6 class="fw-bold mb-1" style="font-size: 0.938rem;">
                                                        {{ $goal->user->name }}
                                                        @if($goal->user->trashed())
                                                            <span class="badge bg-danger ms-1" style="font-size: 0.65rem;">Deactivated</span>
                                                        @endif
                                                    </h6>
                                                    <p class="text-muted mb-0" style="font-size: 0.75rem;">Daily Goal: {{ $goal->daily_goal }} proposals</p>
                                                </div>
                                            @else
                                                <div class="flex-grow-1">
                                                    <h6 class="fw-bold mb-1 text-muted" style="font-size: 0.938rem;">Deleted User</h6>
                                                    <p class="text-muted mb-0" style="font-size: 0.75rem;">Daily Goal: {{ $goal->daily_goal }} proposals</p>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="text-muted" style="font-size: 0.813rem;">Progress</span>
                                                <span class="fw-bold" style="font-size: 0.938rem;">{{ $todayCount }}/{{ $goal->daily_goal }}</span>
                                            </div>
                                            <div class="progress rounded-pill" style="height: 8px;">
                                                <div class="progress-bar bg-{{ $status }}" role="progressbar"
                                                     style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}"
                                                     aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted" style="font-size: 0.75rem;">{{ $progress }}% Complete</span>
                                            @if($todayCount >= $goal->daily_goal)
                                                <span class="badge bg-success rounded-pill px-3">
                                                    <i class="fas fa-check-circle me-1"></i>Completed
                                                </span>
                                            @elseif($progress >= 75)
                                                <span class="badge bg-warning rounded-pill px-3">
                                                    <i class="fas fa-clock me-1"></i>Almost There
                                                </span>
                                            @else
                                                <span class="badge bg-danger rounded-pill px-3">
                                                    <i class="fas fa-exclamation-circle me-1"></i>Needs Work
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center text-muted py-5">
                                    <i class="fas fa-bullseye fa-4x mb-4 d-block opacity-25"></i>
                                    <h5 class="fw-bold mb-2">No goals set yet</h5>
                                    <p class="mb-4">Start by creating goals for your BD users to track their daily performance.</p>
                                    <a href="{{ route('admin.goals.create') }}" class="btn btn-primary rounded-pill px-4 py-2">
                                        <i class="fas fa-plus me-2"></i>Create First Goal
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Chart builder
        function buildChart(canvasId, labels, data, label) {
            const ctx = document.getElementById(canvasId).getContext('2d');
            return new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        data: data,
                        borderColor: '#e67717',
                        backgroundColor: 'rgba(230, 119, 23, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#e67717',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        const dailyLabels = @json($dailyLabels ?? []);
        const dailyCounts = @json($dailyCounts ?? []);
        const weeklyLabels = @json($weeklyLabels ?? []);
        const weeklyCounts = @json($weeklyCounts ?? []);
        const monthlyLabels = @json($monthlyLabels ?? []);
        const monthlyCounts = @json($monthlyCounts ?? []);

        buildChart('dailyChart', dailyLabels, dailyCounts, 'Daily');
        buildChart('weeklyChart', weeklyLabels, weeklyCounts, 'Weekly');
        buildChart('monthlyChart', monthlyLabels, monthlyCounts, 'Monthly');

        // Activity Logs Date Filter - AJAX
        const activityDateFilter = document.getElementById('activity-date-filter');
        const activityCustomDate = document.getElementById('activity-custom-date');
        const activityListContainer = document.getElementById('activity-list-container');
        const activityCount = document.getElementById('activity-count');
        const activityLoading = document.getElementById('activity-loading');

        function loadActivityLogs(dateValue) {
            // Show loading indicator
            activityLoading.classList.remove('d-none');
            activityLoading.classList.add('d-flex');

            const url = new URL('{{ route('admin.activity-logs') }}');
            if (dateValue) {
                url.searchParams.append('date', dateValue);
            }

            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the activity list
                    activityListContainer.innerHTML = data.html;

                    // Update the count badge
                    activityCount.textContent = data.count;

                    // Re-initialize dynamic avatars if needed
                    if (window.initDynamicAvatars) {
                        initDynamicAvatars(activityListContainer);
                    }
                }
            })
            .catch(error => {
                console.error('Error loading activity logs:', error);
                window.toast?.error('Error', 'Failed to load activity logs');
            })
            .finally(() => {
                // Hide loading indicator
                activityLoading.classList.remove('d-flex');
                activityLoading.classList.add('d-none');
            });
        }

        // Event listener for dropdown filter
        activityDateFilter.addEventListener('change', function() {
            const selectedValue = this.value;

            // Clear custom date when using dropdown
            activityCustomDate.value = '';

            // Load filtered activity
            loadActivityLogs(selectedValue);
        });

        // Event listener for custom date picker
        activityCustomDate.addEventListener('change', function() {
            const selectedDate = this.value;

            if (selectedDate) {
                // Clear dropdown selection
                activityDateFilter.value = '';

                // Load activity for custom date
                loadActivityLogs(selectedDate);
            }
        });

        // Load today's activity on page load (matching the default selection)
        document.addEventListener('DOMContentLoaded', function() {
            loadActivityLogs('today');
        });
    </script>
@endsection
