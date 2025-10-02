<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UpworkProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UpworkProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $profiles = UpworkProfile::with(['assignedBds' => function($query) {
            $query->select('name', 'email', 'avatar');
        }])->paginate(10);
        return view('admin.upwork-profiles.index', compact('profiles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $bdUsers = User::where('role', 'bd')->select('id', 'name', 'email', 'avatar')->get();
        return view('admin.upwork-profiles.create', compact('bdUsers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'profile_name' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'assigned_bd_ids' => 'nullable|array',
            'assigned_bd_ids.*' => 'exists:users,id'
        ]);

        try {
            DB::beginTransaction();

            // Create the profile first
            $profile = UpworkProfile::create([
                'profile_name' => $request->profile_name,
                'country' => $request->country,
                'username' => $request->username,
                'password' => $request->password,
                'assigned_bd_ids' => $request->assigned_bd_ids
            ]);

            // Sync BD assignments (this also updates the pivot table)
            $profile->assignedBds()->sync($request->assigned_bd_ids ?? []);

            DB::commit();

            return redirect()->route('admin.upwork-profiles.index')
                           ->with('success', 'Upwork profile created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                        ->with('error', 'Failed to create upwork profile. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(UpworkProfile $upworkProfile)
    {
        $upworkProfile->load(['assignedBds' => function($query) {
            $query->select('name', 'email', 'avatar');
        }]);
        return view('admin.upwork-profiles.show', compact('upworkProfile'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UpworkProfile $upworkProfile)
    {
        $upworkProfile->load(['assignedBds' => function($query) {
            $query->select('name', 'email', 'avatar');
        }]);
        $bdUsers = User::where('role', 'bd')->select('id', 'name', 'email', 'avatar')->get();
        $assignedBdIds = $upworkProfile->assignedBds->pluck('id')->toArray();

        return view('admin.upwork-profiles.edit', compact('upworkProfile', 'bdUsers', 'assignedBdIds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UpworkProfile $upworkProfile)
    {
        $request->validate([
            'profile_name' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'assigned_bd_ids' => 'nullable|array',
            'assigned_bd_ids.*' => 'exists:users,id'
        ]);

        try {
            DB::beginTransaction();

            // Sync BD assignments first
            $upworkProfile->assignedBds()->sync($request->assigned_bd_ids ?? []);

            // Update the JSON field to match the pivot table
            $upworkProfile->update([
                'profile_name' => $request->profile_name,
                'country' => $request->country,
                'username' => $request->username,
                'password' => $request->password,
                'assigned_bd_ids' => $request->assigned_bd_ids
            ]);

            DB::commit();

            return redirect()->route('admin.upwork-profiles.index')
                           ->with('success', 'Upwork profile updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                        ->with('error', 'Failed to update upwork profile. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UpworkProfile $upworkProfile)
    {
        try {
            $upworkProfile->assignedBds()->detach();
            $upworkProfile->delete();

            return redirect()->route('admin.upwork-profiles.index')
                           ->with('success', 'Upwork profile deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete upwork profile. Please try again.');
        }
    }
}
