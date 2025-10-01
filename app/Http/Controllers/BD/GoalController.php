<?php

namespace App\Http\Controllers\BD;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $goal = Goal::firstOrCreate(['user_id' => $user->id], ['daily_goal' => 0]);

        $today = now()->toDateString();
        $todayProposalsCount = Proposal::where('user_id', $user->id)
            ->whereDate('submitted_at', $today)
            ->count();

        $dailyGoal = (int) $goal->daily_goal;
        $bidsLeft = max($dailyGoal - $todayProposalsCount, 0);

        return view('bd.goals.index', compact('goal', 'dailyGoal', 'todayProposalsCount', 'bidsLeft'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'daily_goal' => 'required|integer|min:0|max:1000',
        ]);

        $user = Auth::user();
        $goal = Goal::firstOrCreate(['user_id' => $user->id]);
        $goal->update(['daily_goal' => (int) $request->daily_goal]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Daily goal updated!', 'redirect' => route('bd.goals.index')]);
        }

        return redirect()->route('bd.goals.index')->with('success', 'Daily goal updated!');
    }
}


