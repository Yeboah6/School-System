<?php

namespace App\Http\Controllers;

use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Profile/Index', ['user' => $request->user()->only(['name', 'email'])]);
    }

    public function update(Request $request, AuditService $audit)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$request->user()->id]]);
        $request->user()->update($data);
        $audit->record($request, 'profile.updated', 'Account profile updated.');
        return back()->with('success', 'Profile updated successfully.');
    }

    public function password(Request $request, AuditService $audit)
    {
        $data = $request->validate(['current_password' => ['required', 'current_password'], 'password' => ['required', 'confirmed', Password::defaults()]]);
        $request->user()->update(['password' => Hash::make($data['password'])]);
        $audit->record($request, 'password.changed', 'Account password changed.');
        return back()->with('success', 'Password changed successfully.');
    }
}