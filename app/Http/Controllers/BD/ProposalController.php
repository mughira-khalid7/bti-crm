<?php

namespace App\Http\Controllers\BD;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
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
        $query = Proposal::where('user_id', $user->id)->orderByDesc('submitted_at');

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
        return view('bd.proposals.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'job_description' => 'required|string',
            'connects_used' => 'required|integer|min:0',
            'url' => 'required|url',
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
            'submitted_at.required' => 'Select the date you submitted this proposal.',
            'submitted_at.date' => 'Submitted date must be a valid date.',
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
            'url' => 'required|url',
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
            'submitted_at.required' => 'Select the date you submitted this proposal.',
            'submitted_at.date' => 'Submitted date must be a valid date.',
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

        $proposal->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Proposal updated successfully!', 'redirect' => route('bd.proposals.index')]);
        }

        return redirect()->route('bd.proposals.index')->with('success', 'Proposal updated successfully!');
    }

    public function destroy(Request $request, Proposal $proposal)
    {
        $this->authorizeOwnership($proposal);

        $proposal->delete();

        ActionLog::create([
            'user_id' => Auth::id(),
            'action' => 'proposal_deleted',
            'description' => 'BD deleted a proposal',
            'model_type' => Proposal::class,
            'model_id' => $proposal->id,
            'metadata' => ['title' => $proposal->title],
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
}


