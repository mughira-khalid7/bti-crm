<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\User;
use App\Models\ActionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProposalController extends Controller
{
    public function index(Request $request)
    {
        $query = Proposal::withTrashed()->with(['user' => function($query) {
            $query->withTrashed();
        }])->orderByDesc('submitted_at');

        // Filters: date ranges and user
        $dateFilter = $request->get('date');
        $from = $request->get('from');
        $to = $request->get('to');
        $userId = $request->get('user_id');
        $status = $request->get('status');

        if ($dateFilter) {
            $today = now()->toDateString();
            switch ($dateFilter) {
                case 'today':
                    $query->whereDate('submitted_at', $today);
                    break;
                case 'yesterday':
                    $query->whereDate('submitted_at', now()->subDay()->toDateString());
                    break;
                case 'last_3_days':
                    $query->whereBetween('submitted_at', [now()->subDays(3)->toDateString(), $today]);
                    break;
                case 'last_week':
                    $query->whereBetween('submitted_at', [now()->subWeek()->toDateString(), $today]);
                    break;
                case 'last_month':
                    $query->whereBetween('submitted_at', [now()->subMonth()->toDateString(), $today]);
                    break;
            }
        }

        if ($from && $to) {
            $query->whereBetween('submitted_at', [$from, $to]);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $proposals = $query->paginate(10)->withQueryString();
        $users = User::where('role', 'bd')->withTrashed()->orderBy('name')->get();

        return view('admin.proposals.index', compact('proposals', 'users'));
    }

    public function show($id)
    {
        // Include soft-deleted proposals and users so admins can view deleted records
        $proposal = Proposal::withTrashed()->with(['user' => function($query) {
            $query->withTrashed();
        }])->findOrFail($id);

        // Get the latest version to determine what was recently changed
        $latestVersion = $proposal->versions()->with('user')->first();
        $recentChanges = $latestVersion ? $latestVersion->changes : [];

        return view('admin.proposals.show', compact('proposal', 'recentChanges'));
    }

    /**
     * Get version history for a proposal (AJAX endpoint)
     */
    public function versionHistory($id)
    {
        $proposal = Proposal::withTrashed()->findOrFail($id);

        $versions = $proposal->versions()
            ->with('user')
            ->orderBy('version_number', 'desc')
            ->get()
            ->map(function ($version) {
                return [
                    'id' => $version->id,
                    'version_number' => $version->version_number,
                    'user' => $version->user ? $version->user->name : 'Deleted User',
                    'user_avatar' => $version->user && $version->user->avatar
                        ? asset('avatars/' . $version->user->avatar)
                        : null,
                    'created_at' => $version->created_at->format('M d, Y \a\t h:i A'),
                    'created_at_human' => $version->created_at->diffForHumans(),
                    'changes' => $version->changes,
                    'snapshot' => $version->snapshot,
                ];
            });

        return response()->json([
            'success' => true,
            'versions' => $versions,
        ]);
    }

    public function moveToInterviewing(Request $request, Proposal $proposal)
    {
        $proposal->update(['status' => 'interviewing']);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Moved to Interviewing']);
        }

        return back()->with('success', 'Proposal moved to Interviewing');
    }

    public function destroy(Request $request, Proposal $proposal)
    {
        // Store proposal data before deletion for action log
        $proposalId = $proposal->id;
        $proposalTitle = $proposal->title;

        $proposal->delete();

        ActionLog::create([
            'user_id' => Auth::id(),
            'action' => 'proposal_deleted',
            'description' => 'Admin deleted a proposal',
            'model_type' => Proposal::class,
            'model_id' => $proposalId,
            'metadata' => ['title' => $proposalTitle],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Proposal deleted']);
        }

        return back()->with('success', 'Proposal deleted');
    }
}


