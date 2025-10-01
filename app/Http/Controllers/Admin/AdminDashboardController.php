<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Proposal;
use App\Models\ActionLog;
use App\Models\Goal;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $users  = User::where('role', 'bd')->count();
        $active = User::where('role', 'bd')->where('status', 'active')->count();

        $totalProposals = Proposal::count();
        $interviewingCount = Proposal::where('status', 'interviewing')->count();

        $today = now()->toDateString();
        $todayCount = Proposal::whereDate('submitted_at', $today)->count();
        $weekCount = Proposal::whereBetween('submitted_at', [now()->startOfWeek()->toDateString(), $today])->count();
        $monthCount = Proposal::whereBetween('submitted_at', [now()->startOfMonth()->toDateString(), $today])->count();

        // Chart datasets
        // Daily: last 14 days
        $dailyLabels = [];
        $dailyCounts = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $label = $date->format('M d');
            $count = Proposal::whereDate('submitted_at', $date->toDateString())->count();
            $dailyLabels[] = $label;
            $dailyCounts[] = $count;
        }

        // Weekly: last 8 weeks (Mon-Sun)
        $weeklyLabels = [];
        $weeklyCounts = [];
        for ($i = 7; $i >= 0; $i--) {
            $start = now()->startOfWeek()->subWeeks($i);
            $end = (clone $start)->endOfWeek();
            $label = 'Wk ' . $start->format('W');
            $count = Proposal::whereBetween('submitted_at', [$start->toDateString(), $end->toDateString()])->count();
            $weeklyLabels[] = $label;
            $weeklyCounts[] = $count;
        }

        // Monthly: last 12 months
        $monthlyLabels = [];
        $monthlyCounts = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->startOfMonth()->subMonths($i);
            $label = $month->format('M Y');
            $count = Proposal::whereBetween('submitted_at', [$month->toDateString(), $month->endOfMonth()->toDateString()])->count();
            $monthlyLabels[] = $label;
            $monthlyCounts[] = $count;
        }

        // Latest lists
        $latestBdUsers = User::where('role', 'bd')
            ->withCount('proposals')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $latestProposals = Proposal::with(['user' => function($query) {
                $query->withTrashed();
            }])
            ->orderByDesc('submitted_at')
            ->limit(5)
            ->get();

        // Get action logs for the dashboard (include soft-deleted users) - default to today
        $actionLogs = ActionLog::with(['user' => function($query) {
                $query->withTrashed();
            }])
            ->whereDate('created_at', now()->toDateString())
            ->latest()
            ->limit(50)
            ->get();

        // Get goals overview for the dashboard (include soft-deleted users)
        $goalsOverview = Goal::with(['user' => function($query) {
                $query->withTrashed();
            }])
            ->get()
            ->map(function ($goal) use ($today) {
                $todayProposalsCount = Proposal::where('user_id', $goal->user_id)
                    ->whereDate('submitted_at', $today)
                    ->count();

                $goal->today_proposals_count = $todayProposalsCount;
                return $goal;
            });

        return view('admin.dashboard', compact(
            'users', 'active', 'totalProposals', 'interviewingCount', 'todayCount', 'weekCount', 'monthCount',
            'dailyLabels', 'dailyCounts', 'weeklyLabels', 'weeklyCounts', 'monthlyLabels', 'monthlyCounts',
            'latestBdUsers', 'latestProposals', 'actionLogs', 'goalsOverview'
        ));
    }

    /**
     * Get filtered activity logs via AJAX
     */
    public function getActivityLogs(Request $request)
    {
        $query = ActionLog::with(['user' => function($q) {
            $q->withTrashed();
        }]);

        // Apply date filter
        if ($request->has('date') && $request->date) {
            $date = $request->date;

            switch ($date) {
                case 'today':
                    $query->whereDate('created_at', now()->toDateString());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', now()->subDay()->toDateString());
                    break;
                case 'last_7_days':
                    $query->whereBetween('created_at', [now()->subDays(7), now()]);
                    break;
                case 'last_30_days':
                    $query->whereBetween('created_at', [now()->subDays(30), now()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                default:
                    // Custom date format YYYY-MM-DD
                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                        $query->whereDate('created_at', $date);
                    }
            }
        }

        $actionLogs = $query->latest()->limit(50)->get();

        return response()->json([
            'success' => true,
            'count' => $actionLogs->count(),
            'html' => view('admin.partials.activity-logs', compact('actionLogs'))->render()
        ]);
    }
}
