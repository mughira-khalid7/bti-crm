<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'bd');

        // Filter to show only soft-deleted users if requested
        if ($request->get('show_deleted') === 'only') {
            $query->onlyTrashed();
        } elseif ($request->get('show_deleted') === 'with') {
            $query->withTrashed();
        }

        $users = $query->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Restore a soft-deleted user account.
     */
    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('admin.users.index', ['show_deleted' => 'with'])
            ->with('success', 'User account has been restored successfully!');
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users|ends_with:@browntech.co',
            'password' => 'required|min:6',
            'avatar'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ], [
            'email.ends_with' => 'Email must be a company email (@browntech.co).',
            'avatar.max'      => 'Profile picture may not be greater than 10 MB.',
            'avatar.mimes'    => 'Profile picture must be a JPG, PNG, or WEBP image.',
            'avatar.image'    => 'Please upload a valid image file.',
        ]);

        $avatarFilename = null;
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $avatarFilename = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
            if (!is_dir(public_path('avatars'))) {
                @mkdir(public_path('avatars'), 0755, true);
            }
            $file->move(public_path('avatars'), $avatarFilename);
        }

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'bd',
            'status'   => 'active',
            'avatar'   => $avatarFilename,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'BD user created successfully!',
                'redirect' => route('admin.users.index')
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'BD user created successfully!');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|ends_with:@browntech.co|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ], [
            'email.ends_with' => 'Email must be a company email (@browntech.co).',
            'avatar.max'      => 'Profile picture may not be greater than 10 MB.',
            'avatar.mimes'    => 'Profile picture must be a JPG, PNG, or WEBP image.',
            'avatar.image'    => 'Please upload a valid image file.',
        ]);

        $data = [
            'name'   => $request->name,
            'email'  => $request->email,
            'status' => $request->status,
        ];

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $avatarFilename = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
            if (!is_dir(public_path('avatars'))) {
                @mkdir(public_path('avatars'), 0755, true);
            }
            $file->move(public_path('avatars'), $avatarFilename);
            $data['avatar'] = $avatarFilename;
        }

        $user->update($data);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'BD user updated successfully!',
                'redirect' => route('admin.users.index')
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'BD user updated successfully!');
    }

    /**
     * Permanently delete a user and all associated data.
     * Admin deletion: Force delete (permanent removal)
     */
    public function destroy(Request $request, User $user)
    {
        // Delete user's avatar if exists
        if ($user->avatar) {
            $avatarPath = public_path('avatars/' . $user->avatar);
            if (file_exists($avatarPath)) {
                @unlink($avatarPath);
            }
        }

        // Permanently delete the user and cascade delete associated data
        // Force delete bypasses soft deletes
        $user->forceDelete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'BD user and all associated data permanently deleted!'
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'BD user and all associated data permanently deleted!');
    }
}
