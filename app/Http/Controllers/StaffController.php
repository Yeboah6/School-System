<?php

namespace App\Http\Controllers;

use App\Models\School;
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
            'staff' => $school->staff()->latest()->limit(8)->get(['id', 'first_name', 'last_name', 'role', 'employee_id', 'status', 'user_id']),
        ]);
    }

    public function index(Request $request)
    {
        Gate::authorize('manage-school-setup');

        $school = $request->user()?->school ?? School::query()->firstOrCreate(['name' => 'Hillcrest Academy']);

        return Inertia::render('Staff/Index', [
            'branches' => $school->branches()->where('status', 'active')->orderBy('name')->get(['id', 'name', 'code']),
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
            $data['employee_id'] ??= $this->nextEmployeeId($school->id);
            $staff = $school->staff()->create($data + ['status' => 'active']);

            return app(PortalAccountService::class)->provisionTeacher($staff);
        });
        $audit->record($request, 'staff.created', 'Staff profile created.', $staff);

        return redirect()->route('staff.index')
            ->with('success', 'Staff profile created successfully.')
            ->with('portal_credentials', $credentials);
    }

    private function nextEmployeeId(int $schoolId): string
    {
        $number = $schoolId.'-'.str_pad((string) ($schoolId + Staff::where('school_id', $schoolId)->count() + 1), 4, '0', STR_PAD_LEFT);
        return 'EMP-'.$number;
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
            'employee_id' => ['nullable', 'string', 'max:100'], 'qualification' => ['nullable', 'string', 'max:255'],
        ]);
        if (! empty($data['branch_id'])) {
            $request->user()->school->branches()->whereKey($data['branch_id'])->firstOrFail();
        }
        $staff->update($data);

        return back()->with('success', 'Staff profile updated successfully.');
    }
}
