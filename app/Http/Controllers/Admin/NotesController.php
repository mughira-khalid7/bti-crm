<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BDNote;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotesController extends Controller
{
    /**
     * Display all BD notes for admin review.
     */
    public function index()
    {
        $bdNotes = BDNote::with('user')
            ->join('users', 'b_d_notes.user_id', '=', 'users.id')
            ->where('users.role', 'bd')
            ->whereNull('users.deleted_at')
            ->select('b_d_notes.*')
            ->orderBy('b_d_notes.updated_at', 'desc')
            ->get();

        // Get admin's own note
        $admin = Auth::user();
        $adminNote = BDNote::where('user_id', $admin->id)->first();

        return view('admin.notes.index', compact('bdNotes', 'adminNote', 'admin'));
    }

    /**
     * Display notes for a specific BD.
     */
    public function show(User $user)
    {
        if ($user->role !== 'bd') {
            abort(403, 'Unauthorized');
        }

        $note = BDNote::where('user_id', $user->id)->first();

        return view('admin.notes.show', compact('note', 'user'));
    }

    /**
     * View and edit the admin's own notes.
     */
    public function my()
    {
        $me = Auth::user();
        $note = BDNote::where('user_id', $me->id)->first();
        return view('admin.notes.my', compact('note', 'me'));
    }

    /**
     * Create or update a note for the current user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|min:3',
        ]);

        $me = Auth::user();
        $note = BDNote::firstOrNew(['user_id' => $me->id]);
        $note->content = $request->input('content');
        $note->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Notes saved successfully.']);
        }

        return redirect()->route('admin.notes.my')->with('success', 'Notes saved successfully.');
    }
}
