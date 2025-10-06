<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\User;
use App\Models\ActionLog;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProposalController extends Controller
{
    public function index(Request $request)
    {
        $query = Proposal::withTrashed()->with(['user' => function($query) {
            $query->withTrashed();
        }])
        ->where(function($q) {
            // Show proposals that are not deleted OR are BD-deleted (admin can see BD-deleted proposals for review)
            $q->whereNull('deletion_type')
              ->orWhere('deletion_type', 'bd');
        })
        ->orderByDesc('submitted_at');

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

    public function create()
    {
        $users = User::where('role', 'bd')->whereNull('deleted_at')->orderBy('name')->get();

        // Get all upwork profiles for admin
        $upworkProfiles = \App\Models\UpworkProfile::all();

        return view('admin.proposals.create', compact('users', 'upworkProfiles'));
    }

    public function remainingConnects(Request $request)
    {
        $userId = $request->query('user_id');
        $date = $request->query('date', now()->toDateString());
        if (!$userId) {
            return response()->json(['allowed' => null, 'used' => 0, 'remaining' => null, 'date' => $date]);
        }
        $goal = \App\Models\Goal::where('user_id', $userId)->first();
        if (!$goal) {
            return response()->json(['allowed' => null, 'used' => 0, 'remaining' => null, 'date' => $date]);
        }
        $targetDate = (string) (\Carbon\Carbon::parse($date)->toDateString());
        $used = (int) Proposal::where('user_id', $userId)->whereDate('submitted_at', $targetDate)->sum('connects_used');
        $allowed = (int) $goal->allowed_connects;
        $remaining = max($allowed - $used, 0);
        return response()->json(['allowed' => $allowed, 'used' => $used, 'remaining' => $remaining, 'date' => $targetDate]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'job_description' => 'required|string',
            'connects_used' => 'required|integer|min:0',
            'url' => ['required', 'url', 'regex:/^https:\/\/(www\.)?upwork\.com\//i'],
            'notes' => 'nullable|string',
            'submitted_at' => 'required|date',
            'upwork_profile_id' => 'required|exists:upwork_profiles,id',
        ]);

        $validated['status'] = 'submitted';

        // Enforce daily connects only for TODAY for the selected BD
        $targetDate = (string) (Carbon::parse($validated['submitted_at'])->toDateString());
        $isToday = $targetDate === now()->toDateString();
        if ($isToday) {
            $goal = \App\Models\Goal::where('user_id', $validated['user_id'])->first();
            if ($goal) {
                $usedOnDate = (int) Proposal::where('user_id', $validated['user_id'])
                    ->whereDate('submitted_at', $targetDate)
                    ->sum('connects_used');
                $proposedTotal = $usedOnDate + (int) $validated['connects_used'];
                if ($proposedTotal > (int) $goal->allowed_connects) {
                    $allowed = (int) $goal->allowed_connects;
                    $message = "This BD has only " . max($allowed - $usedOnDate, 0) . " connects left for $targetDate. Used $usedOnDate of $allowed.";
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
            return response()->json(['success' => true, 'message' => 'Proposal created successfully!', 'redirect' => route('admin.proposals.index')]);
        }

        return redirect()->route('admin.proposals.index')->with('success', 'Proposal created successfully!');
    }

    public function show($id)
    {
        // Include soft-deleted proposals and users so admins can view deleted records
        $proposal = Proposal::withTrashed()->with([
            'user' => function($query) {
                $query->withTrashed();
            },
            'upworkProfile',
            // Load only active (not cancelled) meeting; newest first
            'meeting' => function($q) {
                $q->where(function($q2) {
                    $q2->whereNull('status')->orWhere('status', '!=', 'cancelled');
                })->orderByDesc('scheduled_at');
            },
            'meeting.bdUser'
        ])
        ->where(function($q) {
            // Show proposals that are not deleted OR are BD-deleted (admin can see BD-deleted proposals for review)
            $q->whereNull('deletion_type')
              ->orWhere('deletion_type', 'bd');
        })
        ->findOrFail($id);

        // Get the latest version to determine what was recently changed
        $latestVersion = $proposal->versions()->with('user')->first();
        $recentChanges = $latestVersion ? $latestVersion->changes : [];

        // Get all BD users for meeting assignment
        $bdUsers = User::where('role', 'bd')->where('status', 'active')->orderBy('name')->get();

        return view('admin.proposals.show', compact('proposal', 'recentChanges', 'bdUsers'));
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

    public function destroy(Request $request, $id)
    {
        // Find the proposal including soft-deleted ones
        $proposal = Proposal::withTrashed()->findOrFail($id);

        // Store proposal data before deletion for action log
        $proposalId = $proposal->id;
        $proposalTitle = $proposal->title;
        $isPermanent = $request->has('permanent') && $proposal->trashed();

        if ($isPermanent) {
            // Permanent deletion - only for BD-deleted proposals
            $proposal->forceDelete();

            ActionLog::create([
                'user_id' => Auth::id(),
                'action' => 'proposal_permanently_deleted',
                'description' => 'Admin permanently deleted a BD-deleted proposal (removed from database)',
                'model_type' => Proposal::class,
                'model_id' => $proposalId,
                'metadata' => ['title' => $proposalTitle, 'deletion_type' => 'permanent'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $message = 'Proposal permanently deleted';
        } else {
            if ($proposal->trashed() && $proposal->deletion_type === 'bd') {
                // BD-deleted proposal: mark as admin-deleted (hidden from both views)
                $proposal->update(['deletion_type' => 'admin']);

                ActionLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'proposal_deleted',
                    'description' => 'Admin deleted a BD-deleted proposal (hidden from all views, kept in database)',
                    'model_type' => Proposal::class,
                    'model_id' => $proposalId,
                    'metadata' => ['title' => $proposalTitle, 'deletion_type' => 'admin'],
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                $message = 'Proposal deleted';
            } else {
                // Active proposal: mark as admin-deleted and soft delete
                $proposal->update(['deletion_type' => 'admin']);
                $proposal->delete();

                ActionLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'proposal_deleted',
                    'description' => 'Admin deleted an active proposal (hidden from all views, kept in database)',
                    'model_type' => Proposal::class,
                    'model_id' => $proposalId,
                    'metadata' => ['title' => $proposalTitle, 'deletion_type' => 'admin'],
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                $message = 'Proposal deleted';
            }
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('success', $message);
    }

    /**
     * View admin-deleted proposals for recovery
     */
    public function deleted()
    {
        $proposals = Proposal::withTrashed()
            ->with(['user' => function($query) {
                $query->withTrashed();
            }])
            ->where('deletion_type', 'admin')
            ->orderByDesc('deleted_at')
            ->paginate(10);

        return view('admin.proposals.deleted', compact('proposals'));
    }

    /**
     * Restore an admin-deleted proposal
     */
    public function restore(Request $request, $id)
    {
        $proposal = Proposal::withTrashed()->findOrFail($id);

        if ($proposal->deletion_type !== 'admin') {
            abort(403, 'Only admin-deleted proposals can be restored');
        }

        $proposal->update(['deletion_type' => null]);
        $proposal->restore();

        ActionLog::create([
            'user_id' => Auth::id(),
            'action' => 'proposal_restored',
            'description' => 'Admin restored a previously deleted proposal',
            'model_type' => Proposal::class,
            'model_id' => $proposal->id,
            'metadata' => ['title' => $proposal->title],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Proposal restored successfully']);
        }

        return back()->with('success', 'Proposal restored successfully');
    }

    /**
     * Generate AI proposal for a given proposal
     */
    public function generateAI(Request $request, $id)
    {
        $proposal = Proposal::withTrashed()->findOrFail($id);
        
        // For now, return a demo AI-generated proposal
        // Later, this will integrate with actual AI service
        $aiProposal = $this->generateDemoAIProposal($proposal);
        
        ActionLog::create([
            'user_id' => Auth::id(),
            'action' => 'ai_proposal_generated',
            'description' => 'Admin generated AI proposal for: ' . $proposal->title,
            'model_type' => Proposal::class,
            'model_id' => $proposal->id,
            'metadata' => ['title' => $proposal->title],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'proposal' => $aiProposal,
            'message' => 'AI proposal generated successfully'
        ]);
    }

    /**
     * Generate a demo AI proposal (placeholder for real AI integration)
     */
    private function generateDemoAIProposal(Proposal $proposal)
    {
        // Extract key information from the original proposal
        $title = $proposal->title;
        $jobDescription = $proposal->job_description;
        
        // Demo AI-generated proposal content
        $aiContent = "
            <div class='ai-proposal-content'>
                <div class='alert alert-info mb-3'>
                    <i class='fas fa-robot me-2'></i>
                    <strong>AI-Generated Proposal</strong> - Based on: {$title}
                </div>
                
                <h5>Professional Proposal for Your {$title} Project</h5>
                
                <p>Dear Client,</p>
                
                <p>I am excited to submit my proposal for your {$title} project. With my extensive experience in this field, I am confident that I can deliver exceptional results that exceed your expectations.</p>
                
                <h6>Why Choose Me?</h6>
                <ul>
                    <li><strong>Proven Track Record:</strong> Successfully completed 50+ similar projects with 100% client satisfaction</li>
                    <li><strong>Expertise:</strong> 5+ years of experience in this specific domain</li>
                    <li><strong>Quality Assurance:</strong> Rigorous testing and quality control processes</li>
                    <li><strong>Timely Delivery:</strong> Always meet deadlines with regular progress updates</li>
                </ul>
                
                <h6>My Approach</h6>
                <p>I follow a systematic approach to ensure project success:</p>
                <ol>
                    <li><strong>Analysis & Planning:</strong> Thoroughly understand your requirements and create a detailed project plan</li>
                    <li><strong>Development Phase:</strong> Implement solutions using best practices and industry standards</li>
                    <li><strong>Testing & Optimization:</strong> Comprehensive testing to ensure quality and performance</li>
                    <li><strong>Delivery & Support:</strong> Provide final deliverables and ongoing support</li>
                </ol>
                
                <h6>Timeline & Deliverables</h6>
                <p>I estimate this project will take approximately 2-3 weeks to complete, with the following deliverables:</p>
                <ul>
                    <li>Complete project documentation</li>
                    <li>Source code with detailed comments</li>
                    <li>Comprehensive testing reports</li>
                    <li>User guide and training materials</li>
                    <li>30 days of free support and bug fixes</li>
                </ul>
                
                <h6>Investment</h6>
                <p>My rate for this project is competitive and includes all development, testing, documentation, and initial support.</p>
                
                <p>I am available to start immediately and would love to discuss your project in more detail. Please feel free to message me with any questions.</p>
                
                <p>Looking forward to working with you!</p>
                
                <p>Best regards,<br>
                [Your Name]</p>
            </div>
        ";

        return [
            'content' => $aiContent,
            'generated_at' => now()->toDateTimeString(),
            'based_on' => $title,
            'word_count' => str_word_count(strip_tags($aiContent))
        ];
    }

    /**
     * Schedule a meeting for a proposal
     */
    public function scheduleMeeting(Request $request, Proposal $proposal)
    {
        $request->validate([
            'bd_user_id' => 'nullable|exists:users,id',
            'assigned_bd_ids' => 'required|array|min:1',
            'assigned_bd_ids.*' => 'exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_at' => 'required|date|after:now',
            'meeting_type' => 'required|in:video_call,phone_call,in_person',
            'meeting_link' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);
        // Check if a non-cancelled meeting already exists for this proposal
        if ($proposal->meeting()
            ->where(function($q) {
                $q->whereNull('status')->orWhere('status', '!=', 'cancelled');
            })
            ->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'A meeting has already been scheduled for this proposal.'
            ], 400);
        }

        // Determine primary BD: explicit bd_user_id or first of assigned list
        $primaryBdId = $request->bd_user_id ?? (is_array($request->assigned_bd_ids) && count($request->assigned_bd_ids) > 0
            ? $request->assigned_bd_ids[0]
            : null);

        // Create the meeting
        $meeting = Meeting::create([
            'proposal_id' => $proposal->id,
            'bd_user_id' => $primaryBdId,
            'title' => $request->title,
            'description' => $request->description,
            'scheduled_at' => Carbon::parse($request->scheduled_at),
            'meeting_type' => $request->meeting_type,
            'meeting_link' => $request->meeting_link,
            'location' => $request->location,
            'notes' => $request->notes,
        ]);

        // Sync participants (ensure primary is included)
        $participantIds = array_unique(array_merge($request->assigned_bd_ids, $primaryBdId ? [$primaryBdId] : []));
        $meeting->participants()->sync($participantIds);

        // Update proposal status to meeting_scheduled
        $proposal->update(['status' => 'meeting_scheduled']);

        // Log the action
        ActionLog::create([
            'user_id' => Auth::id(),
            'action' => 'meeting_scheduled',
            'description' => 'Admin scheduled a meeting for proposal',
            'model_type' => Proposal::class,
            'model_id' => $proposal->id,
            'metadata' => [
                'proposal_title' => $proposal->title,
                'meeting_title' => $meeting->title,
                'scheduled_at' => $meeting->scheduled_at->format('Y-m-d H:i:s'),
                'bd_user' => $meeting->bdUser->name,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Meeting scheduled successfully!',
            'meeting' => $meeting->load('bdUser')
        ]);
    }

    /**
     * Update a scheduled meeting
     */
    public function updateMeeting(Request $request, Proposal $proposal)
    {
        $meeting = $proposal->meeting()->first();

        if (!$meeting) {
            return response()->json([
                'success' => false,
                'message' => 'No meeting found for this proposal.'
            ], 404);
        }

        $request->validate([
            'bd_user_id' => 'nullable|exists:users,id',
            'assigned_bd_ids' => 'nullable|array',
            'assigned_bd_ids.*' => 'exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_at' => 'required|date',
            'meeting_type' => 'required|in:video_call,phone_call,in_person',
            'meeting_link' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Compute primary BD if provided or fallback to first of assigned list (when present)
        $updatedPrimaryBdId = $request->bd_user_id ?? ($request->assigned_bd_ids[0] ?? $meeting->bd_user_id);

        $meeting->update([
            'bd_user_id' => $updatedPrimaryBdId,
            'title' => $request->title,
            'description' => $request->description,
            'scheduled_at' => Carbon::parse($request->scheduled_at),
            'meeting_type' => $request->meeting_type,
            'meeting_link' => $request->meeting_link,
            'location' => $request->location,
            'notes' => $request->notes,
        ]);

        if ($request->has('assigned_bd_ids')) {
            $meeting->participants()->sync(array_unique(array_merge($request->assigned_bd_ids ?? [], [$updatedPrimaryBdId])));
        }

        // Log the action
        ActionLog::create([
            'user_id' => Auth::id(),
            'action' => 'meeting_updated',
            'description' => 'Admin updated meeting details',
            'model_type' => Proposal::class,
            'model_id' => $proposal->id,
            'metadata' => [
                'proposal_title' => $proposal->title,
                'meeting_title' => $meeting->title,
                'scheduled_at' => $meeting->scheduled_at->format('Y-m-d H:i:s'),
                'bd_user' => $meeting->bdUser->name,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Meeting updated successfully!',
            'meeting' => $meeting->load('bdUser')
        ]);
    }

    /**
     * Cancel a scheduled meeting
     */
    public function cancelMeeting(Request $request, Proposal $proposal)
    {
        $meeting = $proposal->meeting()->first();

        if (!$meeting) {
            return response()->json([
                'success' => false,
                'message' => 'No meeting found for this proposal.'
            ], 404);
        }

        $meeting->update(['status' => 'cancelled']);

        // Update proposal status back to submitted
        $proposal->update(['status' => 'submitted']);

        // Log the action
        ActionLog::create([
            'user_id' => Auth::id(),
            'action' => 'meeting_cancelled',
            'description' => 'Admin cancelled meeting',
            'model_type' => Proposal::class,
            'model_id' => $proposal->id,
            'metadata' => [
                'proposal_title' => $proposal->title,
                'meeting_title' => $meeting->title,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Meeting cancelled successfully!'
        ]);
    }

    /**
     * Update proposal status (admin only)
     */
    public function updateStatus(Request $request, Proposal $proposal)
    {
        $request->validate([
            'status' => 'required|string|in:submitted,interviewing,meeting_scheduled,phone_shared,viewed,copied'
        ]);

        $proposal->status = $request->status;
        $proposal->save();

        ActionLog::create([
            'user_id' => Auth::id(),
            'action' => 'proposal_status_updated',
            'description' => 'Admin updated proposal status',
            'model_type' => Proposal::class,
            'model_id' => $proposal->id,
            'metadata' => [
                'new_status' => $proposal->status,
                'title' => $proposal->title,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'status' => $proposal->status,
        ]);
    }
}


