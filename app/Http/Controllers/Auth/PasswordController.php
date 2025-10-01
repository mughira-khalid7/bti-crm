<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed', 'different:current_password'],
        ]);

        // Only reaches here if validation passes; wrap in a transaction for atomicity
        DB::transaction(function () use ($request, $validated) {
            $request->user()->update([
                'password' => Hash::make($validated['password']),
            ]);
        });

        return back()->with('status', 'password-updated')
            ->with('success', 'Password updated successfully!');
    }
}
