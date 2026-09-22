<?php

namespace App\Http\Controllers;

use App\Models\ParentGuardian;
use App\Services\PortalAccountService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ParentController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('manage-school-setup');
        $school = $request->user()->school;
        $parents = $school->parents()->with('students')->orderBy('created_at', 'desc')->get();

        return Inertia::render('Parents/Index', [
            'parents' => $parents->map(fn (ParentGuardian $parent) => [
                'id' => $parent->id,
                'first_name' => $parent->first_name,
                'last_name' => $parent->last_name,
                'email' => $parent->email,
                'phone' => $parent->phone,
                'address' => $parent->address,
                'occupation' => $parent->occupation,
                'emergency_contact' => $parent->emergency_contact,
                'relationship' => $parent->relationship_to_student,
                'students' => $parent->students->map(fn ($student) => ['id' => $student->id, 'name' => trim($student->first_name.' '.$student->last_name)]),
            ]),
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-school-setup');
        $data = $this->validated($request);
        $credentials = DB::transaction(function () use ($request, $data): ?array {
            $parent = $request->user()->school->parents()->create($data + ['status' => 'active']);

            return app(PortalAccountService::class)->provisionParent($parent);
        });

        return back()->with('success', 'Parent record created successfully.')
            ->with('portal_credentials', $credentials);
    }

    public function update(Request $request, ParentGuardian $parent)
    {
        Gate::authorize('manage-school-setup');
        abort_unless($parent->school_id === $request->user()->school_id, 404);
        $parent->update($this->validated($request));

        return back()->with('success', 'Parent record updated successfully.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'relationship_to_student' => ['nullable', 'string', 'max:255'],
        ]);
    }
}