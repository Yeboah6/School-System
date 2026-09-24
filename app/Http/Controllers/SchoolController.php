<?php

namespace App\Http\Controllers;

use App\Http\Requests\School\StoreAcademicYearRequest;
use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Term;
use App\Models\SchoolBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class SchoolController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('manage-school-setup');

        $school = $request->user()?->school ?? School::query()->firstOrCreate(
            ['name' => 'School System'],
            [
                'email' => 'admin@hillcrest.edu.gh',
                'phone' => '+233200000000',
                'currency' => 'GHS',
                'timezone' => 'Africa/Accra',
                'status' => 'active',
            ]
        );

        $request->user()?->update(['school_id' => $school->id]);

        return Inertia::render('School/Overview', [
            'school' => [
                'id' => $school->id,
                'name' => $school->name,
                'email' => $school->email,
                'phone' => $school->phone,
                'address' => $school->address,
                'motto' => $school->motto,
                'principal_name' => $school->principal_name,
                'school_type' => $school->school_type,
            ],
            'academicYears' => $school->academicYears()->orderByDesc('starts_at')->get(['id', 'name', 'starts_at', 'ends_at', 'is_current']),
            'terms' => $school->terms()->orderByDesc('starts_at')->get(['id', 'name', 'starts_at', 'ends_at', 'is_current']),
            'departments' => $school->departments()->orderBy('name')->get(['id', 'name', 'head_name']),
            'classes' => $school->classes()->with('branch:id,name,code')->orderBy('name')->get(['id', 'name', 'level', 'status', 'branch_id']),
            'subjects' => $school->subjects()->orderBy('name')->get(['id', 'name', 'code']),
            'branches' => $school->branches()->orderBy('name')->get(['id', 'name', 'code', 'address', 'phone', 'status']),
            'stats' => [
                ['label' => 'Students', 'value' => $school->students()->count(), 'tone' => 'teal'],
                ['label' => 'Staff', 'value' => $school->staff()->count(), 'tone' => 'rose'],
                ['label' => 'Subjects', 'value' => $school->subjects()->count(), 'tone' => 'amber'],
                ['label' => 'Parents', 'value' => $school->parents()->count(), 'tone' => 'sky'],
            ],
        ]);
    }

    public function details(Request $request): Response
    {
        Gate::authorize('manage-school-setup');

        $school = $request->user()?->school ?? School::query()->firstOrCreate(
            ['name' => 'Hillcrest Academy'],
            [
                'email' => 'admin@hillcrest.edu.gh',
                'phone' => '+233200000000',
                'currency' => 'GHS',
                'timezone' => 'Africa/Accra',
                'status' => 'active',
            ]
        );

        $request->user()?->update(['school_id' => $school->id]);

        $academicYears = $school->academicYears()->orderByDesc('starts_at')->get();
        $terms = $school->terms()->orderByDesc('starts_at')->get();
        $departments = $school->departments()->orderBy('name')->get();
        $classes = $school->classes()->with(['branch:id,name,code', 'academicYear:id,name'])->orderBy('name')->get();
        $subjects = $school->subjects()->orderBy('name')->get();

        return Inertia::render('School/Index', [
            'school' => [
                'id' => $school->id,
                'name' => $school->name,
                'email' => $school->email,
                'phone' => $school->phone,
                'address' => $school->address,
                'motto' => $school->motto,
                'currency' => $school->currency,
                'timezone' => $school->timezone,
                'principal_name' => $school->principal_name,
                'school_type' => $school->school_type,
            ],
            'academicYears' => $academicYears->map(fn(AcademicYear $year) => [
                'id' => $year->id,
                'name' => $year->name,
                'starts_at' => $year->starts_at->format('Y-m-d'),
                'ends_at' => $year->ends_at->format('Y-m-d'),
                'is_current' => (bool) $year->is_current,
            ]),
            'terms' => $terms->map(fn(Term $term) => [
                'id' => $term->id,
                'name' => $term->name,
                'academic_year_id' => $term->academic_year_id,
                'academicYear' => $term->academicYear ? ['id' => $term->academicYear->id, 'name' => $term->academicYear->name] : null,
                'starts_at' => $term->starts_at->format('Y-m-d'),
                'ends_at' => $term->ends_at->format('Y-m-d'),
                'is_current' => (bool) $term->is_current,
            ]),
            'departments' => $departments->map(fn(Department $department) => [
                'id' => $department->id,
                'name' => $department->name,
                'head_name' => $department->head_name,
                'description' => $department->description,
            ]),
            'classes' => $classes->map(fn(SchoolClass $schoolClass) => [
                'id' => $schoolClass->id,
                'academic_year_id' => $schoolClass->academic_year_id,
                'academicYear' => $schoolClass->academicYear ? ['id' => $schoolClass->academicYear->id, 'name' => $schoolClass->academicYear->name] : null,
                'name' => $schoolClass->name,
                'level' => $schoolClass->level,
                'status' => $schoolClass->status,
                'branch_id' => $schoolClass->branch_id,
                'branch' => $schoolClass->branch ? ['id' => $schoolClass->branch->id, 'name' => $schoolClass->branch->name, 'code' => $schoolClass->branch->code] : null,
            ]),
            'subjects' => $subjects->map(fn(Subject $subject) => [
                'id' => $subject->id,
                'name' => $subject->name,
                'code' => $subject->code,
                'description' => $subject->description,
            ]),
            'branches' => $school->branches()->orderBy('name')->get()->map(fn(SchoolBranch $branch) => [
                'id' => $branch->id,
                'name' => $branch->name,
                'code' => $branch->code,
                'address' => $branch->address,
                'phone' => $branch->phone,
                'status' => $branch->status,
            ]),
            'stats' => [
                ['label' => 'Total Students', 'value' => $school->students()->count(), 'tone' => 'teal'],
                ['label' => 'Staff', 'value' => $school->staff()->count(), 'tone' => 'rose'],
                ['label' => 'Total Subjects', 'value' => $school->subjects()->count(), 'tone' => 'amber'],
                ['label' => 'Branches', 'value' => $school->branches()->count(), 'tone' => 'sky'],
            ],
        ]);
    }

    public function updateSchoolProfile(Request $request)
    {
        Gate::authorize('manage-school-setup');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'motto' => ['nullable', 'string', 'max:255'],
            'currency' => ['nullable', 'string', 'max:3'],
            'timezone' => ['nullable', 'string', 'max:255'],
            'principal_name' => ['nullable', 'string', 'max:255'],
            'school_type' => ['nullable', 'string', 'max:255'],
        ]);

        $school = $request->user()?->school ?? School::firstOrCreate(['name' => $data['name']]);
        $school->fill($data)->save();

        if ($request->user()) {
            $request->user()->update(['school_id' => $school->id]);
        }

        return redirect()->route('school.details')->with('success', 'School profile updated successfully.');
    }

    public function storeAcademicYear(StoreAcademicYearRequest $request)
    {
        Gate::authorize('manage-school-setup');

        $school = $request->user()?->school ?? School::query()->firstOrCreate(
            ['name' => 'Hillcrest Academy'],
            ['status' => 'active']
        );

        $academicYear = $school->academicYears()->create($request->validated());

        if ($request->boolean('is_current')) {
            $school->academicYears()->whereKeyNot($academicYear->id)->update(['is_current' => false]);
        }

        return redirect()->route('school.details')->with('success', 'Academic year created successfully.');
    }

    public function updateAcademicYear(Request $request, AcademicYear $academicYear)
    {
        Gate::authorize('manage-school-setup');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
            'is_current' => ['nullable', 'boolean'],
        ]);

        $academicYear->update($data);

        if ($request->boolean('is_current')) {
            $academicYear->school()->first()->academicYears()->whereKeyNot($academicYear->id)->update(['is_current' => false]);
        }

        return redirect()->route('school.details')->with('success', 'Academic year updated successfully.');
    }

    public function destroyAcademicYear(AcademicYear $academicYear)
    {
        Gate::authorize('manage-school-setup');
        $academicYear->delete();

        return redirect()->route('school.details')->with('success', 'Academic year deleted successfully.');
    }

    public function storeTerm(Request $request)
    {
        Gate::authorize('manage-school-setup');

        $data = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'name' => ['required', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
            'is_current' => ['nullable', 'boolean'],
        ]);

        $school = $request->user()?->school ?? School::query()->firstOrCreate(['name' => 'School System']);
        $term = $school->terms()->create([
            'academic_year_id' => $data['academic_year_id'],
            'name' => $data['name'],
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
            'is_current' => $data['is_current'] ?? false,
            'status' => 'active',
        ]);

        if ($request->boolean('is_current')) {
            $school->terms()->where('academic_year_id', $term->academic_year_id)->whereKeyNot($term->id)->update(['is_current' => false]);
        }

        return redirect()->route('school.details')->with('success', 'Term created successfully.');
    }

    public function updateTerm(Request $request, Term $term)
    {
        Gate::authorize('manage-school-setup');

        $data = $request->validate([
            'academic_year_id' => ['sometimes', 'exists:academic_years,id'],
            'name' => ['required', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
            'is_current' => ['nullable', 'boolean'],
        ]);

        $term->fill($data);
        $term->save();

        if ($request->boolean('is_current')) {
            $term->school()->first()->terms()->where('academic_year_id', $term->academic_year_id)->whereKeyNot($term->id)->update(['is_current' => false]);
        }

        return redirect()->route('school.details')->with('success', 'Term updated successfully.');
    }

    public function destroyTerm(Term $term)
    {
        Gate::authorize('manage-school-setup');
        $term->delete();

        return redirect()->route('school.details')->with('success', 'Term deleted successfully.');
    }

    public function storeDepartment(Request $request)
    {
        Gate::authorize('manage-school-setup');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'head_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $school = $request->user()?->school ?? School::query()->firstOrCreate(['name' => 'Hillcrest Academy']);
        $school->departments()->create([
            'name' => $data['name'],
            'head_name' => $data['head_name'] ?? null,
            'description' => $data['description'] ?? null,
            'status' => 'active',
        ]);

        return redirect()->route('school.details')->with('success', 'Department created successfully.');
    }

    public function updateDepartment(Request $request, Department $department)
    {
        Gate::authorize('manage-school-setup');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'head_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $department->update($data);

        return redirect()->route('school.details')->with('success', 'Department updated successfully.');
    }

    public function destroyDepartment(Department $department)
    {
        Gate::authorize('manage-school-setup');
        $department->delete();

        return redirect()->route('school.details')->with('success', 'Department deleted successfully.');
    }

    public function storeClass(Request $request)
    {
        Gate::authorize('manage-school-setup');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'level' => ['nullable', 'string', 'max:255'],
            'academic_year_id' => ['nullable', 'integer', 'exists:academic_years,id'],
            'branch_id' => ['nullable', 'integer'],
        ]);

        $school = $request->user()?->school ?? School::query()->firstOrCreate(['name' => 'Hillcrest Academy']);
        if (! empty($data['academic_year_id'])) {
            $school->academicYears()->whereKey($data['academic_year_id'])->firstOrFail();
        }
        if (! empty($data['branch_id'])) {
            $school->branches()->whereKey($data['branch_id'])->firstOrFail();
        }
        $school->classes()->create([
            'name' => $data['name'],
            'level' => $data['level'] ?? null,
            'academic_year_id' => $data['academic_year_id'] ?? null,
            'branch_id' => $data['branch_id'] ?? null,
            'status' => 'active',
        ]);

        return redirect()->route('school.details')->with('success', 'Class created successfully.');
    }

    public function updateClass(Request $request, SchoolClass $schoolClass)
    {
        Gate::authorize('manage-school-setup');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'level' => ['nullable', 'string', 'max:255'],
            'academic_year_id' => ['nullable', 'integer', 'exists:academic_years,id'],
            'branch_id' => ['nullable', 'integer'],
        ]);

        abort_unless($schoolClass->school_id === $request->user()?->school_id, 404);
        if (! empty($data['academic_year_id'])) {
            $request->user()?->school?->academicYears()->whereKey($data['academic_year_id'])->firstOrFail();
        }
        if (! empty($data['branch_id'])) {
            $request->user()?->school?->branches()->whereKey($data['branch_id'])->firstOrFail();
        }
        $schoolClass->update($data);

        return redirect()->route('school.details')->with('success', 'Class updated successfully.');
    }

    public function destroyClass(SchoolClass $schoolClass)
    {
        Gate::authorize('manage-school-setup');
        $schoolClass->delete();

        return redirect()->route('school.details')->with('success', 'Class deleted successfully.');
    }

    public function storeSubject(Request $request)
    {
        Gate::authorize('manage-school-setup');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
        ]);

        $school = $request->user()?->school ?? School::query()->firstOrCreate(['name' => 'Hillcrest Academy']);
        $school->subjects()->create([
            'name' => $data['name'],
            'code' => $data['code'] ?? null,
            'description' => $data['description'] ?? null,
            'status' => 'active',
        ]);

        return redirect()->route('school.details')->with('success', 'Subject created successfully.');
    }

    public function updateSubject(Request $request, Subject $subject)
    {
        Gate::authorize('manage-school-setup');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
        ]);

        $subject->update($data);

        return redirect()->route('school.details')->with('success', 'Subject updated successfully.');
    }

    public function destroySubject(Subject $subject)
    {
        Gate::authorize('manage-school-setup');
        $subject->delete();

        return redirect()->route('school.details')->with('success', 'Subject deleted successfully.');
    }

    public function storeBranch(Request $request)
    {
        Gate::authorize('manage-school-setup');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
        ]);

        $school = $request->user()?->school;
        abort_unless($school, 422, 'A school is required before adding a branch.');
        $school->branches()->create([...$data, 'code' => strtoupper($data['code']), 'status' => 'active']);

        return redirect()->route('school.details')->with('success', 'School branch created successfully.');
    }

    public function updateBranch(Request $request, SchoolBranch $branch)
    {
        Gate::authorize('manage-school-setup');
        abort_unless($branch->school_id === $request->user()?->school_id, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $branch->update([...$data, 'code' => strtoupper($data['code'])]);

        return redirect()->route('school.details')->with('success', 'School branch updated successfully.');
    }

    public function destroyBranch(Request $request, SchoolBranch $branch)
    {
        Gate::authorize('manage-school-setup');
        abort_unless($branch->school_id === $request->user()?->school_id, 404);
        $branch->delete();

        return redirect()->route('school.details')->with('success', 'School branch deleted successfully.');
    }
}
