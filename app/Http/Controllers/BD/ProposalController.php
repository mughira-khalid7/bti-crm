<?php

namespace App\Http\Controllers\BD;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\ProposalVersion;
use App\Models\Goal;
use App\Models\ActionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProposalController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Proposal::where('user_id', $user->id)
            ->where('is_copy', false)  // Exclude copied proposals for BD users
            ->where(function($q) {
                // Hide BD-deleted proposals from BD view
                $q->whereNull('deletion_type')
                  ->orWhere('deletion_type', '!=', 'bd');
            })
            ->orderByDesc('submitted_at');

        $dateFilter = $request->get('date', 'today');
        $from = $request->get('from');
        $to = $request->get('to');
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

        if ($status) {
            $query->where('status', $status);
        }

        $proposals = $query->paginate(10)->withQueryString();

        return view('bd.proposals.index', compact('proposals'));
    }

    public function interviewing(Request $request)
    {
        $user = Auth::user();
        $proposals = Proposal::where('user_id', $user->id)
            ->where('status', 'interviewing')
            ->orderByDesc('submitted_at')
            ->paginate(10);

        return view('bd.interviewing.index', compact('proposals'));
    }

    /**
     * Return remaining connects for the authenticated BD for a given date.
     * Optional query param: proposal_id (exclude this proposal when editing)
     */
    public function remainingConnects(Request $request)
    {
        $userId = Auth::id();
        $date = $request->query('date', now()->toDateString());
        $proposalId = $request->query('proposal_id');

        $goal = Goal::where('user_id', $userId)->first();
        if (!$goal) {
            return response()->json([
                'allowed' => null,
                'used' => 0,
                'remaining' => null,
                'date' => $date,
                'message' => 'No goal set for this user.'
            ]);
        }

        $targetDate = (string) (Carbon::parse($date)->toDateString());
        $query = Proposal::where('user_id', $userId)
            ->whereDate('submitted_at', $targetDate);
        if ($proposalId) {
            $query->where('id', '!=', $proposalId);
        }
        $used = (int) $query->sum('connects_used');
        $allowed = (int) $goal->allowed_connects;
        $remaining = max($allowed - $used, 0);

        return response()->json([
            'allowed' => $allowed,
            'used' => $used,
            'remaining' => $remaining,
            'date' => $targetDate,
        ]);
    }

    public function create()
    {
        $user = Auth::user();

        // Get upwork profiles assigned to this BD user
        $upworkProfiles = \App\Models\UpworkProfile::whereHas('users', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        return view('bd.proposals.create', compact('upworkProfiles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'job_description' => 'required|string',
            'connects_used' => 'required|integer|min:0',
            'url' => ['required', 'url', 'regex:/^https:\/\/(www\.)?upwork\.com\//i'],
            'notes' => 'nullable|string',
            'submitted_at' => 'required|date',
            'upwork_profile_id' => 'required|exists:upwork_profiles,id',
        ], [
            'title.required' => 'Please enter a title for this proposal.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'job_description.required' => 'Please add a brief job description.',
            'connects_used.required' => 'Enter how many connects you used.',
            'connects_used.integer' => 'Connects used must be a whole number.',
            'connects_used.min' => 'Connects used cannot be negative.',
            'url.required' => 'Enter the job posting URL.',
            'url.url' => 'Please enter a valid URL (including https://).',
            'url.regex' => 'The job URL must be a valid Upwork URL (e.g., https://www.upwork.com/nx/proposals/...).',
            'submitted_at.required' => 'Select the date you submitted this proposal.',
            'submitted_at.date' => 'Submitted date must be a valid date.',
            'upwork_profile_id.required' => 'Please select an Upwork profile.',
            'upwork_profile_id.exists' => 'The selected Upwork profile is invalid.',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'submitted';

        // Enforce per-user daily allowed connects only for TODAY
        $targetDate = (string) (\Carbon\Carbon::parse($validated['submitted_at'])->toDateString());
        $isToday = $targetDate === now()->toDateString();
        if ($isToday) {
            $goal = Goal::where('user_id', $validated['user_id'])->first();
            if ($goal) {
                $usedOnDate = Proposal::where('user_id', $validated['user_id'])
                    ->whereDate('submitted_at', $targetDate)
                    ->sum('connects_used');

                $proposedTotal = $usedOnDate + (int) $validated['connects_used'];
                if ($proposedTotal > (int) $goal->allowed_connects) {
                    $allowed = (int) $goal->allowed_connects;
                    $message = "You have only " . max($allowed - $usedOnDate, 0) . " connects left for $targetDate. You've already used $usedOnDate out of $allowed.";
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => $message,
                            'errors' => ['connects_used' => [$message]],
                        ], 422);
                    }
                    return back()->withErrors(['connects_used' => $message])->withInput();
                }
            }
        }

        Proposal::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Proposal submitted successfully!', 'redirect' => route('bd.proposals.index')]);
        }

        return redirect()->route('bd.proposals.index')->with('success', 'Proposal submitted successfully!');
    }

    public function show(Proposal $proposal)
    {
        $this->authorizeOwnership($proposal);
        // Reload proposal with only non-cancelled meeting data so cancelled meetings don't show
        $proposal->load([
            'upworkProfile',
            'meeting' => function($q) {
                $q->where(function($q2) {
                    $q2->whereNull('status')->orWhere('status', '!=', 'cancelled');
                })->orderByDesc('scheduled_at');
            },
            'meeting.bdUser'
        ]);
        return view('bd.proposals.show', compact('proposal'));
    }

    public function edit(Proposal $proposal)
    {
        $this->authorizeOwnership($proposal);
        return view('bd.proposals.edit', compact('proposal'));
    }

    public function update(Request $request, Proposal $proposal)
    {
        $this->authorizeOwnership($proposal);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'job_description' => 'required|string',
            'connects_used' => 'required|integer|min:0',
            'url' => ['required', 'url', 'regex:/^https:\/\/(www\.)?upwork\.com\//i'],
            'notes' => 'nullable|string',
            'submitted_at' => 'required|date',
        ], [
            'title.required' => 'Please enter a title for this proposal.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'job_description.required' => 'Please add a brief job description.',
            'connects_used.required' => 'Enter how many connects you used.',
            'connects_used.integer' => 'Connects used must be a whole number.',
            'connects_used.min' => 'Connects used cannot be negative.',
            'url.required' => 'Enter the job posting URL.',
            'url.url' => 'Please enter a valid URL (including https://).',
            'url.regex' => 'The job URL must be a valid Upwork URL (e.g., https://www.upwork.com/nx/proposals/...).',
            'submitted_at.required' => 'Select the date you submitted this proposal.',
            'submitted_at.date' => 'Submitted date must be a valid date.',
            'upwork_profile_id.required' => 'Please select an Upwork profile.',
            'upwork_profile_id.exists' => 'The selected Upwork profile is invalid.',
        ]);

        // Enforce per-user daily allowed connects only for TODAY on update
        $targetDate = (string) (\Carbon\Carbon::parse($validated['submitted_at'])->toDateString());
        $isToday = $targetDate === now()->toDateString();
        if ($isToday) {
            $goal = Goal::where('user_id', $proposal->user_id)->first();
            if ($goal) {
                $usedOnDate = Proposal::where('user_id', $proposal->user_id)
                    ->whereDate('submitted_at', $targetDate)
                    ->where('id', '!=', $proposal->id)
                    ->sum('connects_used');

                $proposedTotal = $usedOnDate + (int) $validated['connects_used'];
                if ($proposedTotal > (int) $goal->allowed_connects) {
                    $allowed = (int) $goal->allowed_connects;
                    $message = "You have only " . max($allowed - $usedOnDate, 0) . " connects left for $targetDate. You've already used $usedOnDate out of $allowed (excluding this proposal).";
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => $message,
                            'errors' => ['connects_used' => [$message]],
                        ], 422);
                    }
                    return back()->withErrors(['connects_used' => $message])->withInput();
                }
            }
        }

        // Create a copy of the original proposal before updating (visible only to admin)
        $this->createProposalCopy($proposal);

        // Track changes for version history
        $this->trackProposalChanges($proposal, $validated);

        $proposal->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Proposal updated successfully!', 'redirect' => route('bd.proposals.index')]);
        }

        return redirect()->route('bd.proposals.index')->with('success', 'Proposal updated successfully!');
    }

    public function destroy(Request $request, Proposal $proposal)
    {
        $this->authorizeOwnership($proposal);

        // Store previous status for logging
        $previousStatus = $proposal->status;

        // Mark as BD-deleted (hidden from BD view but visible to admin)
        $proposal->update([
            'status' => 'deleted',
            'deletion_type' => 'bd'
        ]);

        $proposal->delete();

        ActionLog::create([
            'user_id' => Auth::id(),
            'action' => 'proposal_deleted',
            'description' => 'BD deleted a proposal (hidden from BD view, visible to admin)',
            'model_type' => Proposal::class,
            'model_id' => $proposal->id,
            'metadata' => [
                'title' => $proposal->title,
                'previous_status' => $previousStatus,
                'deletion_type' => 'bd'
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Proposal deleted']);
        }

        return redirect()->route('bd.proposals.index')->with('success', 'Proposal deleted');
    }

    public function moveToInterviewing(Request $request, Proposal $proposal)
    {
        // Only allow the owner BD to change status
        $this->authorizeOwnership($proposal);

        if ($proposal->status !== 'interviewing') {
            $proposal->status = 'interviewing';
            $proposal->save();
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Proposal moved to interviewing successfully!',
                'redirect' => route('bd.proposals.index'),
            ]);
        }

        return redirect()
            ->route('bd.proposals.index')
            ->with('success', 'Proposal moved to interviewing successfully!');
    }

    private function authorizeOwnership(Proposal $proposal): void
    {
        if ($proposal->user_id !== Auth::id()) {
            abort(403);
        }
    }

    /**
     * Track changes to a proposal and create a version record.
     */
    /**
     * Create a copy of the proposal before updating (for admin review)
     */
    private function createProposalCopy(Proposal $proposal): void
    {
        // Only create a copy if this is not already a copy
        if ($proposal->is_copy) {
            return;
        }

        $copyData = $proposal->toArray();

        // Remove fields that shouldn't be copied
        unset($copyData['id'], $copyData['created_at'], $copyData['updated_at'], $copyData['deleted_at']);

        // Set copy-specific fields
        $copyData['is_copy'] = true;
        $copyData['original_proposal_id'] = $proposal->id;
        $copyData['status'] = 'copied';

        // Create the copy
        Proposal::create($copyData);
    }

    private function trackProposalChanges(Proposal $proposal, array $newData): void
    {
        $changes = [];
        $trackedFields = Proposal::getTrackedFields();

        foreach ($trackedFields as $field) {
            $oldValue = $proposal->$field;
            $newValue = $newData[$field] ?? null;

            // Normalize values for comparison
            if ($field === 'submitted_at') {
                $oldValue = $oldValue ? Carbon::parse($oldValue)->format('Y-m-d H:i:s') : null;
                $newValue = $newValue ? Carbon::parse($newValue)->format('Y-m-d H:i:s') : null;
            }

            // Track if changed
            if ($oldValue != $newValue) {
                $changes[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        // Only create a version if there were actual changes
        if (!empty($changes)) {
            // Get the next version number
            $lastVersion = ProposalVersion::where('proposal_id', $proposal->id)
                ->orderBy('version_number', 'desc')
                ->first();
            $nextVersion = $lastVersion ? $lastVersion->version_number + 1 : 1;

            // Create snapshot of all tracked fields BEFORE the update
            $snapshot = [];
            foreach ($trackedFields as $field) {
                $snapshot[$field] = $proposal->$field;
            }

            ProposalVersion::create([
                'proposal_id' => $proposal->id,
                'user_id' => Auth::id(),
                'version_number' => $nextVersion,
                'changes' => $changes,
                'snapshot' => $snapshot,
            ]);
        }
    }
}


