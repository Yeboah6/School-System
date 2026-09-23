<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Staff;
use App\Services\AuditService;
use App\Services\PortalAccountService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class StaffController extends Controller
{
    public function overview(Request $request)
    {
        Gate::authorize('manage-school-setup');
        $school = $request->user()->school;
        return Inertia::render('Staff/Overview', [
            'summary' => [
                'total' => $school->staff()->count(),
                'active' => $school->staff()->where('status', 'active')->count(),
                'teachers' => $school->staff()->where('role', 'like', '%teacher%')->count(),
                'linked' => $school->staff()->whereNotNull('user_id')->count(),
            ],
            'staff' => $school->staff()->latest()->limit(8)->get(['id', 'first_name', 'last_name', 'role', 'employee_id', 'status', 'user_id', 'email']),
        ]);
    }

    public function index(Request $request)
    {
        Gate::authorize('manage-school-setup');

        $school = $request->user()?->school ?? School::query()->firstOrCreate(['name' => 'Hillcrest Academy']);

        return Inertia::render('Staff/Index', [
            'branches' => $school->branches()->where('status', 'active')->orderBy('name')->get(['id', 'name', 'code']),
            'classes' => $school->classes()->where('status', 'active')->with('branch')->orderBy('name')->get(['id', 'name', 'level', 'branch_id']),
            'staff' => $school->staff()->with('branch')->orderBy('created_at', 'desc')->get()->map(fn (Staff $member) => [
                'id' => $member->id,
                'first_name' => $member->first_name,
                'last_name' => $member->last_name,
                'role' => $member->role,
                'department_name' => $member->department_name,
                'email' => $member->email,
                'phone' => $member->phone,
                'status' => $member->status,
                'branch' => $member->branch?->name,
                'employee_id' => $member->employee_id,
                'qualification' => $member->qualification,
                'joining_date' => $member->joining_date?->format('Y-m-d'),
                'classes' => $member->classes()->orderBy('name')->get(['classes.id', 'classes.name', 'classes.level'])->map(fn ($class) => [
                    'id' => $class->id,
                    'name' => $class->name,
                    'level' => $class->level,
                ]),
            ]),
        ]);
    }

    public function store(Request $request, AuditService $audit)
    {
        Gate::authorize('manage-school-setup');

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'department_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'branch_id' => ['nullable', 'integer'],
            'employee_id' => ['nullable', 'string', 'max:100'],
            'gender' => ['nullable', 'string', 'max:50'],
            'date_of_birth' => ['nullable', 'date'],
            'address' => ['nullable', 'string', 'max:500'],
            'position' => ['nullable', 'string', 'max:255'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'employment_type' => ['nullable', 'string', 'max:100'],
            'joining_date' => ['nullable', 'date'],
        ]);

        $school = $request->user()?->school ?? School::query()->firstOrCreate(['name' => 'Hillcrest Academy']);
        if (! empty($data['branch_id'])) {
            $school->branches()->whereKey($data['branch_id'])->firstOrFail();
        }
        $staff = null;
        $credentials = DB::transaction(function () use ($school, $data, &$staff): ?array {
            $data['employee_id'] = filled($data['employee_id'] ?? null) ? $data['employee_id'] : $this->nextEmployeeId($school->id);
            $staff = $school->staff()->create($data + ['status' => 'active']);

            return app(PortalAccountService::class)->provisionTeacher($staff);
        });
        $audit->record($request, 'staff.created', 'Staff profile created.', $staff);

        return redirect()->route('staff.overview')
            ->with('success', 'Staff profile created successfully.')
            ->with('portal_credentials', $credentials);
    }

    private function nextEmployeeId(int $schoolId): string
    {
        $number = $schoolId.'-'.str_pad((string) ($schoolId + Staff::where('school_id', $schoolId)->count() + 1), 4, '0', STR_PAD_LEFT);
        return 'EMP-'.$number;
    }

    public function show(Request $request, Staff $staff)
    {
        Gate::authorize('manage-school-setup');
        abort_unless($staff->school_id === $request->user()->school_id, 404);

        $staff->load(['branch', 'classes.branch']);

        return Inertia::render('Staff/Show', [
            'staff' => [
                'id' => $staff->id,
                'employee_id' => $staff->employee_id,
                'first_name' => $staff->first_name,
                'last_name' => $staff->last_name,
                'role' => $staff->role,
                'position' => $staff->position,
                'department_name' => $staff->department_name,
                'gender' => $staff->gender,
                'date_of_birth' => $staff->date_of_birth?->format('Y-m-d'),
                'address' => $staff->address,
                'qualification' => $staff->qualification,
                'employment_type' => $staff->employment_type,
                'joining_date' => $staff->joining_date?->format('Y-m-d'),
                'email' => $staff->email,
                'phone' => $staff->phone,
                'status' => $staff->status,
                'branch' => $staff->branch?->name,
                'classes' => $staff->classes->map(fn ($class) => ['id' => $class->id, 'name' => $class->name, 'level' => $class->level, 'branch' => $class->branch?->name]),
            ],
            'classes' => $request->user()->school->classes()
                ->where('status', 'active')
                ->with('branch')
                ->orderBy('name')
                ->get(['id', 'name', 'level', 'branch_id'])
                ->map(fn ($class) => [
                    'id' => $class->id,
                    'name' => $class->name,
                    'level' => $class->level,
                    'branch' => $class->branch?->name,
                ]),
        ]);
    }

    public function edit(Request $request, Staff $staff)
    {
        Gate::authorize('manage-school-setup');
        abort_unless($staff->school_id === $request->user()->school_id, 404);
        $school = $request->user()->school;

        return Inertia::render('Staff/Edit', [
            'staff' => [
                ...$staff->toArray(),
                'date_of_birth' => $staff->date_of_birth?->format('Y-m-d'),
                'joining_date' => $staff->joining_date?->format('Y-m-d'),
            ],
            'branches' => $school->branches()->where('status', 'active')->orderBy('name')->get(['id', 'name', 'code']),
        ]);
    }

    public function update(Request $request, Staff $staff)
    {
        Gate::authorize('manage-school-setup');
        abort_unless($staff->school_id === $request->user()->school_id, 404);
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'], 'last_name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'], 'department_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'], 'phone' => ['nullable', 'string', 'max:255'],
            'branch_id' => ['nullable', 'integer'], 'status' => ['required', 'in:active,inactive'],
            'employee_id' => ['nullable', 'string', 'max:100'], 'gender' => ['nullable', 'string', 'max:50'],
            'date_of_birth' => ['nullable', 'date'], 'address' => ['nullable', 'string', 'max:500'],
            'position' => ['nullable', 'string', 'max:255'], 'qualification' => ['nullable', 'string', 'max:255'],
            'employment_type' => ['nullable', 'string', 'max:100'], 'joining_date' => ['nullable', 'date'],
        ]);
        if (! empty($data['branch_id'])) {
            $request->user()->school->branches()->whereKey($data['branch_id'])->firstOrFail();
        }
        $staff->update($data);

        return back()->with('success', 'Staff profile updated successfully.');
    }

    public function destroy(Request $request, Staff $staff)
    {
        Gate::authorize('manage-school-setup');
        abort_unless($staff->school_id === $request->user()->school_id, 404);
        $staff->delete();

        return redirect()->route('staff.index')->with('success', 'Staff profile deleted successfully.');
    }

    public function assignClass(Request $request, Staff $staff)
    {
        Gate::authorize('manage-school-setup');
        abort_unless($staff->school_id === $request->user()->school_id, 404);
        $data = $request->validate(['class_id' => ['required', 'integer'], 'is_primary' => ['nullable', 'boolean']]);
        $school = $request->user()->school;
        $schoolClass = $school->classes()->whereKey($data['class_id'])->firstOrFail();
        $staff->classes()->syncWithoutDetaching([
            $schoolClass->id => [
                'school_id' => $school->id,
                'is_primary' => (bool) ($data['is_primary'] ?? false),
            ],
        ]);

        return back()->with('success', 'Teacher assigned to class successfully.');
    }

    public function unassignClass(Request $request, Staff $staff, SchoolClass $schoolClass)
    {
        Gate::authorize('manage-school-setup');
        abort_unless($staff->school_id === $request->user()->school_id, 404);
        abort_unless($schoolClass->school_id === $request->user()->school_id, 404);
        $staff->classes()->detach($schoolClass->id);

        return back()->with('success', 'Teacher removed from class successfully.');
    }
}
