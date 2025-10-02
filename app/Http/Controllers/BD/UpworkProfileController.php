<?php

namespace App\Http\Controllers\BD;

use App\Http\Controllers\Controller;
use App\Models\UpworkProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpworkProfileController extends Controller
{
    /**
     * Display a listing of the user's assigned upwork profiles.
     */
    public function index()
    {
        $user = Auth::user();

        // Get profiles assigned to this BD user using the relationship
        $profiles = UpworkProfile::whereHas('assignedBds', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['assignedBds' => function($query) {
            $query->select('name', 'email', 'avatar');
        }])->paginate(15);

        return view('bd.upwork-profiles.index', compact('profiles'));
    }
}
