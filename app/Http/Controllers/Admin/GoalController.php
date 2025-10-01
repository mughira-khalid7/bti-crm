<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\User;
use App\Models\Proposal;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GoalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $goals = Goal::with(['user' => function($query) {
            $query->withTrashed();
        }])->get();
        $users = User::where('role', 'bd')->get();

        // Get today's proposal counts for each user
        $today = now()->toDateString();
        $todayProposals = Proposal::whereDate('submitted_at', $today)
            ->selectRaw('user_id, COUNT(*) as count')
            ->groupBy('user_id')
            ->pluck('count', 'user_id');

        return view('admin.goals.index', compact('goals', 'users', 'todayProposals', 'today'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('role', 'bd')->get();
        return view('admin.goals.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'daily_goal' => 'required|integer|min:0|max:1000',
            'allowed_connects' => 'required|integer|min:0|max:1000',
        ]);

        // Check if user already has a goal
        $existingGoal = Goal::where('user_id', $request->user_id)->first();
        if ($existingGoal) {
            return redirect()->back()->withErrors(['user_id' => 'This user already has a goal set. Please edit the existing goal instead.']);
        }

        Goal::create([
            'user_id' => $request->user_id,
            'daily_goal' => $request->daily_goal,
            'allowed_connects' => $request->allowed_connects,
        ]);

        return redirect()->route('admin.goals.index')->with('success', 'Goal created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Goal $goal)
    {
        $goal->load(['user' => function($query) {
            $query->withTrashed();
        }]);

        $today = now()->toDateString();
        $todayProposalsCount = Proposal::where('user_id', $goal->user_id)
            ->whereDate('submitted_at', $today)
            ->count();

        $dailyGoal = (int) $goal->daily_goal;
        $bidsLeft = max($dailyGoal - $todayProposalsCount, 0);

        return view('admin.goals.show', compact('goal', 'dailyGoal', 'todayProposalsCount', 'bidsLeft'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Goal $goal)
    {
        $goal->load(['user' => function($query) {
            $query->withTrashed();
        }]);
        return view('admin.goals.edit', compact('goal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Goal $goal)
    {
        $request->validate([
            'daily_goal' => 'required|integer|min:0|max:1000',
            'allowed_connects' => 'required|integer|min:0|max:1000',
        ]);

        $goal->update([
            'daily_goal' => $request->daily_goal,
            'allowed_connects' => $request->allowed_connects,
        ]);

        return redirect()->route('admin.goals.index')->with('success', 'Goal updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Goal $goal)
    {
        $goal->delete();
        return redirect()->route('admin.goals.index')->with('success', 'Goal deleted successfully!');
    }
}
