<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('manage-school-setup');

        $school = $request->user()?->school ?? School::query()->firstOrCreate(['name' => 'Hillcrest Academy']);

        return Inertia::render('Staff/Index', [
            'staff' => $school->staff()->orderBy('created_at', 'desc')->get()->map(fn (Staff $member) => [
                'id' => $member->id,
                'first_name' => $member->first_name,
                'last_name' => $member->last_name,
                'role' => $member->role,
                'department_name' => $member->department_name,
                'email' => $member->email,
                'phone' => $member->phone,
                'status' => $member->status,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-school-setup');

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'department_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
        ]);

        $school = $request->user()?->school ?? School::query()->firstOrCreate(['name' => 'Hillcrest Academy']);
        $school->staff()->create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'role' => $data['role'],
            'department_name' => $data['department_name'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'status' => 'active',
        ]);

        return redirect()->route('staff.index')->with('success', 'Staff profile created successfully.');
    }
}
