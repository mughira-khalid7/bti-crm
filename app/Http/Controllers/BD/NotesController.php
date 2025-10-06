<?php

namespace App\Http\Controllers\BD;

use App\Http\Controllers\Controller;
use App\Models\BDNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotesController extends Controller
{
    /**
     * Display the notes page for BD.
     */
    public function index(Request $request)
    {
        $note = BDNote::where('user_id', Auth::id())->first();

        // Check if user wants Quill editor or simple editor
        $editor = $request->get('editor', 'working'); // Default to working version

        if ($editor === 'simple') {
            return view('bd.notes.simple', compact('note'));
        } elseif ($editor === 'quill') {
            return view('bd.notes.quill', compact('note'));
        } else {
            return view('bd.notes.working', compact('note'));
        }
    }

    /**
     * Store or update the BD's notes.
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        BDNote::updateOrCreate(
            ['user_id' => Auth::id()],
            ['content' => $request->content]
        );

        return response()->json([
            'success' => true,
            'message' => 'Notes saved successfully!'
        ]);
    }
}
