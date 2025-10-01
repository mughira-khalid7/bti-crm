<?php

namespace App\Http\Controllers\BD;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BdDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $myTotalProposals = Proposal::where('user_id', $user->id)->count();
        $myInterviewing = Proposal::where('user_id', $user->id)->where('status', 'interviewing')->count();
        $myTodayProposals = Proposal::where('user_id', $user->id)->whereDate('submitted_at', $today)->count();
        $connectsUsedToday = Proposal::where('user_id', $user->id)->whereDate('submitted_at', $today)->sum('connects_used');

        $goal = Goal::firstOrCreate(['user_id' => $user->id], ['daily_goal' => 0]);
        $dailyGoal = (int) $goal->daily_goal;
        $bidsLeft = max($dailyGoal - $myTodayProposals, 0);

        // Performance (last 30 days) for this BD
        $perfLabels = [];
        $perfCounts = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $perfLabels[] = now()->subDays($i)->format('M d');
            $perfCounts[] = Proposal::where('user_id', $user->id)
                ->whereDate('submitted_at', $date)
                ->count();
        }

        // Recent proposals and recent interviewing lists
        $recentProposals = Proposal::where('user_id', $user->id)
            ->orderByDesc('submitted_at')
            ->limit(5)
            ->get();

        $recentInterviewing = Proposal::where('user_id', $user->id)
            ->where('status', 'interviewing')
            ->orderByDesc('submitted_at')
            ->limit(5)
            ->get();

        return view('bd.dashboard', compact(
            'myTotalProposals', 'myInterviewing', 'myTodayProposals', 'dailyGoal', 'bidsLeft', 'connectsUsedToday',
            'perfLabels', 'perfCounts', 'recentProposals', 'recentInterviewing'
        ));
    }
}
