@extends('bd.layouts.master')

@section('title', 'BD Dashboard - Business Developer CRM')
@section('page-title', 'Dashboard Overview')

@section('content')
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Welcome back, {{ Auth::user()->name }}!</h2>
                    <p class="text-muted mb-0">Here's your business development performance overview.</p>
                </div>
                <div class="text-muted">
                    <i class="fas fa-calendar-alt me-2"></i>{{ now()->format('F j, Y') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row g-4 mb-4">
        <!-- Today's Bids -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 rounded-3 shadow-sm h-100 hover-lift">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="flex-grow-1">
                            <p class="text-muted text-uppercase mb-2 fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Today's Bids</p>
                            <h2 class="fw-bold mb-0 text-primary" style="font-size: 2rem;">{{ $myTodayProposals ?? 0 }}</h2>
                            <p class="text-muted mb-0" style="font-size: 0.8rem;">Submitted today</p>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 56px; height: 56px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-handshake text-white" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Connects Used Today -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 rounded-3 shadow-sm h-100 hover-lift">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="flex-grow-1">
                            <p class="text-muted text-uppercase mb-2 fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Connects Used</p>
                            <h2 class="fw-bold mb-0 text-success" style="font-size: 2rem;">{{ $connectsUsedToday ?? 0 }}</h2>
                            <p class="text-muted mb-0" style="font-size: 0.8rem;">Today's total</p>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 56px; height: 56px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                            <i class="fas fa-plug text-white" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Proposals -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 rounded-3 shadow-sm h-100 hover-lift">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="flex-grow-1">
                            <p class="text-muted text-uppercase mb-2 fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Proposals</p>
                            <h2 class="fw-bold mb-0 text-info" style="font-size: 2rem;">{{ $myTotalProposals ?? 0 }}</h2>
                            <p class="text-muted mb-0" style="font-size: 0.8rem;">All time</p>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 56px; height: 56px; background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
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
                            <h2 class="fw-bold mb-0 text-warning" style="font-size: 2rem;">{{ $myInterviewing ?? 0 }}</h2>
                            <p class="text-muted mb-0" style="font-size: 0.8rem;">Active interviews</p>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 56px; height: 56px; background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);">
                            <i class="fas fa-video text-white" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-4 mb-4">
        <!-- Performance Chart -->
        <div class="col-lg-8">
            <div class="card border-0 rounded-3 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-chart-line me-2 text-primary"></i>My Performance (Last 30 Days)
                        </h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="chart-container">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Summary -->
        <div class="col-lg-4">
            <div class="card border-0 rounded-3 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-chart-pie me-2 text-success"></i>Performance Summary
                        </h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted fw-semibold" style="font-size: 0.875rem;">Success Rate</span>
                            <span class="fw-bold text-dark" style="font-size: 1.25rem;">{{ $myTotalProposals > 0 ? round(($myInterviewing / $myTotalProposals) * 100, 1) : 0 }}%</span>
                        </div>
                        <div class="progress rounded-pill" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar"
                                style="width: {{ $myTotalProposals > 0 ? min(100, round(($myInterviewing / $myTotalProposals) * 100, 1)) : 0 }}%"
                                aria-valuenow="{{ $myTotalProposals > 0 ? min(100, round(($myInterviewing / $myTotalProposals) * 100, 1)) : 0 }}"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <p class="text-muted mb-0" style="font-size: 0.813rem;">
                        <i class="fas fa-info-circle me-1"></i>Interviewing leads vs total proposals
                    </p>
                    <div class="mt-4 p-3 bg-light rounded-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted" style="font-size: 0.813rem;">Total Submitted:</span>
                            <span class="fw-bold" style="font-size: 0.875rem;">{{ $myTotalProposals ?? 0 }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted" style="font-size: 0.813rem;">In Interview:</span>
                            <span class="fw-bold text-success" style="font-size: 0.875rem;">{{ $myInterviewing ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="row g-4 mb-4">
        <!-- Recent Interviewing -->
        <div class="col-lg-6">
            <div class="card border-0 rounded-3 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-video me-2 text-warning"></i>Recent Interviewing
                        </h5>
                        <a href="{{ route('bd.interviewing.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">View All</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 py-3 px-4 fw-semibold text-muted" style="font-size: 0.813rem;">Title</th>
                                    <th class="border-0 py-3 fw-semibold text-muted" style="font-size: 0.813rem;">Connects</th>
                                    <th class="border-0 py-3 fw-semibold text-muted" style="font-size: 0.813rem;">URL</th>
                                    <th class="border-0 py-3 fw-semibold text-muted" style="font-size: 0.813rem;">Submitted</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentInterviewing as $p)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <a href="{{ route('bd.proposals.show', $p) }}" class="fw-semibold text-dark text-decoration-none" style="font-size: 0.875rem;">
                                                {{ $p->title }}
                                            </a>
                                        </td>
                                        <td class="py-3">
                                            <span class="badge bg-primary rounded-pill">{{ $p->connects_used }}</span>
                                        </td>
                                        <td class="py-3">
                                            <a href="{{ $p->url }}" target="_blank" class="btn btn-sm btn-outline-info rounded-pill px-3">
                                                <i class="fas fa-external-link-alt me-1"></i>Open
                                            </a>
                                        </td>
                                        <td class="py-3">
                                            <small class="text-muted" style="font-size: 0.75rem;">
                                                {{ $p->created_at->diffForHumans() }}
                                            </small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5">
                                            <i class="fas fa-video fa-2x mb-2 d-block opacity-25"></i>
                                            No recent interviewing items
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Proposals -->
        <div class="col-lg-6">
            <div class="card border-0 rounded-3 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-file-alt me-2 text-primary"></i>Recent Proposals
                        </h5>
                        <a href="{{ route('bd.proposals.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">View All</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 py-3 px-4 fw-semibold text-muted" style="font-size: 0.813rem;">Title</th>
                                    <th class="border-0 py-3 fw-semibold text-muted" style="font-size: 0.813rem;">Status</th>
                                    <th class="border-0 py-3 fw-semibold text-muted" style="font-size: 0.813rem;">Submitted</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentProposals as $p)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <a href="{{ route('bd.proposals.show', $p) }}" class="fw-semibold text-dark text-decoration-none" style="font-size: 0.875rem;">
                                                {{ $p->title }}
                                            </a>
                                            <div class="text-muted" style="font-size: 0.75rem;">{{ Str::limit($p->job_description, 50) }}</div>
                                        </td>
                                        <td class="py-3">
                                            <span class="badge bg-{{ $p->status == 'interviewing' ? 'info' : 'secondary' }} rounded-pill px-3">
                                                {{ ucfirst($p->status) }}
                                            </span>
                                        </td>
                                        <td class="py-3">
                                            <small class="text-muted" style="font-size: 0.75rem;">
                                                {{ $p->created_at->diffForHumans() }}
                                            </small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-5">
                                            <i class="fas fa-file-alt fa-2x mb-2 d-block opacity-25"></i>
                                            No recent proposals
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Performance Chart (last 30 days)
        const perfLabels = @json($perfLabels ?? []);
        const perfCounts = @json($perfCounts ?? []);
        const ctx = document.getElementById('performanceChart').getContext('2d');
        const performanceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: perfLabels,
                datasets: [{
                    label: 'Proposals Submitted',
                    data: perfCounts,
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

    </script>
@endsection
