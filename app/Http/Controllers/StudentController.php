<?php

namespace App\Http\Controllers;

use App\Models\ParentGuardian;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentNote;
use App\Models\StudentTimeline;
use App\Services\PortalAccountService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('manage-school-setup');

        $school = $request->user()?->school ?? School::query()->firstOrCreate(['name' => 'Hillcrest Academy']);
        $students = $school->students()->with(['schoolClass.branch', 'branch'])->orderBy('created_at', 'desc')->get();
        $parents = $school->parents()->orderBy('created_at', 'desc')->get();

        return Inertia::render('Students/Index', [
            'students' => $students->map(fn (Student $student) => [
                'id' => $student->id,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'admission_no' => $student->admission_no,
                'gender' => $student->gender,
                'date_of_birth' => $student->date_of_birth?->format('Y-m-d'),
                'status' => $student->status,
                'class' => $student->schoolClass?->name,
                'branch' => $student->branch?->name,
                'student_id' => $student->student_id,
                'nationality' => $student->nationality,
                'email' => $student->email,
                'phone' => $student->phone,
            ]),
            'parents' => $parents->map(fn (ParentGuardian $parent) => [
                'id' => $parent->id,
                'first_name' => $parent->first_name,
                'last_name' => $parent->last_name,
                'phone' => $parent->phone,
                'relationship' => $parent->relationship_to_student,
            ]),
        ]);
    }

    public function create(Request $request)
    {
        Gate::authorize('manage-school-setup');

        $school = $request->user()->school;

        return Inertia::render('Students/Create', [
            'classes' => $school->classes()->where('status', 'active')->orderBy('name')->get(['id', 'name', 'level', 'branch_id']),
            'branches' => $school->branches()->where('status', 'active')->orderBy('name')->get(['id', 'name', 'code']),
        ]);
    }

    public function show(Request $request, Student $student)
    {
        Gate::authorize('manage-school-setup');

        abort_unless($student->school_id === $request->user()?->school_id, 404);

        $student->load(['parents', 'notes', 'timelines', 'schoolClass.branch', 'branch', 'results.assessment.subject', 'results.assessment.examination']);

        return Inertia::render('Students/Show', [
            'student' => [
                'id' => $student->id,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'middle_name' => $student->middle_name,
                'admission_no' => $student->admission_no,
                'gender' => $student->gender,
                'date_of_birth' => $student->date_of_birth?->format('Y-m-d'),
                'status' => $student->status,
                'class' => $student->schoolClass?->name,
                'branch' => $student->branch?->name,
                'student_id' => $student->student_id,
                'nationality' => $student->nationality,
                'email' => $student->email,
                'phone' => $student->phone,
                'address' => $student->address,
                'admission_date' => $student->admission_date?->format('Y-m-d'),
                'previous_school' => $student->previous_school,
                'student_type' => $student->student_type,
            ],
            'parents' => $student->parents->map(fn ($parent) => [
                'id' => $parent->id,
                'name' => trim($parent->first_name . ' ' . $parent->last_name),
                'phone' => $parent->phone,
                'relationship' => $parent->pivot->relationship ?: $parent->relationship_to_student,
                'is_primary' => (bool) $parent->pivot->is_primary,
                'email' => $parent->email,
                'address' => $parent->address,
                'occupation' => $parent->occupation,
                'emergency_contact' => $parent->emergency_contact,
            ]),
            'notes' => $student->notes->map(fn ($note) => [
                'id' => $note->id,
                'title' => $note->title,
                'note' => $note->note,
                'category' => $note->category,
                'priority' => $note->priority,
                'visibility' => $note->visibility,
                'follow_up_date' => $note->follow_up_date,
                'follow_up_status' => $note->follow_up_status,
                'created_at' => $note->created_at->format('Y-m-d H:i'),
            ]),
            'timelines' => $student->timelines->map(fn ($timeline) => [
                'id' => $timeline->id,
                'title' => $timeline->title,
                'description' => $timeline->description,
                'event_type' => $timeline->event_type,
                'occurred_at' => $timeline->occurred_at?->format('Y-m-d H:i'),
            ]),
            'results' => $student->results->map(fn ($result) => [
                'id' => $result->id,
                'assessment' => $result->assessment->name,
                'examination' => $result->assessment->examination->name,
                'subject' => $result->assessment->subject->name,
                'marks' => (float) $result->marks,
                'maximum_marks' => (float) $result->maximum_marks,
                'grade' => $result->grade,
                'grade_point' => $result->grade_point !== null ? (float) $result->grade_point : null,
                'teacher_comment' => $result->teacher_comment,
                'status' => $result->status,
            ]),
        ]);
    }

    public function edit(Request $request, Student $student)
    {
        Gate::authorize('manage-school-setup');
        abort_unless($student->school_id === $request->user()->school_id, 404);
        $school = $request->user()->school;

        return Inertia::render('Students/Edit', [
            'student' => [
                ...$student->only(['id', 'student_id', 'admission_no', 'first_name', 'last_name', 'middle_name', 'gender', 'nationality', 'email', 'phone', 'address', 'previous_school', 'student_type', 'status', 'class_id', 'branch_id']),
                'date_of_birth' => $student->date_of_birth?->format('Y-m-d'),
                'admission_date' => $student->admission_date?->format('Y-m-d'),
                ...$this->editParentData($student),
            ],
            'classes' => $school->classes()->where('status', 'active')->orderBy('name')->get(['id', 'name', 'level', 'branch_id']),
            'branches' => $school->branches()->where('status', 'active')->orderBy('name')->get(['id', 'name', 'code']),
        ]);
    }

    public function update(Request $request, Student $student)
    {
        Gate::authorize('manage-school-setup');
        abort_unless($student->school_id === $request->user()->school_id, 404);
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'max:50'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'date_of_birth' => ['nullable', 'date'],
            'admission_date' => ['nullable', 'date'],
            'previous_school' => ['nullable', 'string', 'max:255'],
            'student_type' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:applicant,active,graduated,transferred,withdrawn,suspended'],
            'class_id' => ['nullable', 'integer'],
            'branch_id' => ['nullable', 'integer'],
            'parent_name' => ['nullable', 'string', 'max:255'],
            'parent_email' => ['nullable', 'email', 'max:255'],
            'parent_phone' => ['nullable', 'string', 'max:255'],
            'relationship' => ['nullable', 'string', 'max:255'],
            'parent_address' => ['nullable', 'string', 'max:500'],
            'parent_occupation' => ['nullable', 'string', 'max:255'],
            'parent_emergency_contact' => ['nullable', 'string', 'max:255'],
        ]);

        $school = $request->user()->school;
        $class = ! empty($data['class_id']) ? $school->classes()->whereKey($data['class_id'])->firstOrFail() : null;
        $branch = ! empty($data['branch_id']) ? $school->branches()->whereKey($data['branch_id'])->firstOrFail() : null;
        abort_if($class && $branch && $class->branch_id && $class->branch_id !== $branch->id, 422, 'The selected class does not belong to the selected branch.');

        $student->update([...$data, 'branch_id' => $branch?->id ?? $class?->branch_id]);
        $parentData = collect($data)->only(['parent_name', 'parent_email', 'parent_phone', 'relationship', 'parent_address', 'parent_occupation', 'parent_emergency_contact']);
        if ($parentData->filter()->isNotEmpty()) {
            $parent = $student->parents()->wherePivot('is_primary', true)->first() ?? new ParentGuardian(['school_id' => $student->school_id]);
            $nameParts = preg_split('/\s+/', trim((string) $parentData->get('parent_name', 'Parent')));
            $parent->fill([
                'first_name' => $nameParts[0] ?? 'Parent',
                'last_name' => count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : null,
                'email' => $parentData->get('parent_email'), 'phone' => $parentData->get('parent_phone'),
                'relationship_to_student' => $parentData->get('relationship'), 'address' => $parentData->get('parent_address'),
                'occupation' => $parentData->get('parent_occupation'), 'emergency_contact' => $parentData->get('parent_emergency_contact'),
                'status' => 'active',
            ])->save();
            $student->parents()->syncWithoutDetaching([$parent->id => ['relationship' => $parentData->get('relationship'), 'is_primary' => true]]);
        }
        $this->recordTimeline($student, 'Student updated', 'Student profile or assignment updated.', 'student_updated');

        return redirect()->route('students.show', $student)->with('success', 'Student record updated successfully.');
    }

    public function destroy(Request $request, Student $student)
    {
        Gate::authorize('manage-school-setup');
        abort_unless($student->school_id === $request->user()->school_id, 404);

        $student->delete();

        return redirect()->route('students.index')->with('success', 'Student record deleted successfully.');
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-school-setup');

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'max:50'],
            'date_of_birth' => ['nullable', 'date'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'admission_date' => ['nullable', 'date'],
            'previous_school' => ['nullable', 'string', 'max:255'],
            'student_type' => ['nullable', 'string', 'max:100'],
            'admission_no' => ['nullable', 'string', 'max:100'],
            'class_id' => ['nullable', 'integer'],
            'branch_id' => ['nullable', 'integer'],
            'parent_name' => ['nullable', 'string', 'max:255'],
            'parent_first_name' => ['nullable', 'string', 'max:255'],
            'parent_last_name' => ['nullable', 'string', 'max:255'],
            'parent_email' => ['nullable', 'email', 'max:255'],
            'parent_phone' => ['nullable', 'string', 'max:255'],
            'parent_address' => ['nullable', 'string', 'max:500'],
            'parent_occupation' => ['nullable', 'string', 'max:255'],
            'parent_emergency_contact' => ['nullable', 'string', 'max:255'],
            'relationship' => ['nullable', 'string', 'max:255'],
        ]);

        $school = $request->user()?->school ?? School::query()->firstOrCreate(['name' => 'Hillcrest Academy']);

        $class = ! empty($data['class_id'])
            ? $school->classes()->whereKey($data['class_id'])->firstOrFail()
            : null;
        $branch = ! empty($data['branch_id'])
            ? $school->branches()->whereKey($data['branch_id'])->firstOrFail()
            : null;

        if ($class && $branch && $class->branch_id && $class->branch_id !== $branch->id) {
            abort(422, 'The selected class does not belong to the selected branch.');
        }

        $student = null;
        $credentials = DB::transaction(function () use ($school, $data, $class, $branch, &$student): ?array {
            $student = $school->students()->create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'middle_name' => $data['middle_name'] ?? null,
                'gender' => $data['gender'] ?? null,
                'nationality' => $data['nationality'] ?? null,
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'admission_date' => $data['admission_date'] ?? null,
                'previous_school' => $data['previous_school'] ?? null,
                'student_type' => $data['student_type'] ?? null,
                'class_id' => $class?->id,
                'branch_id' => $branch?->id ?? $class?->branch_id,
                'admission_no' => ! empty($data['admission_no']) && $data['admission_no'] !== 'Auto-generated'
                    ? $data['admission_no']
                    : $this->nextAdmissionNumber($school->id),
                'status' => 'active',
            ]);

            $parentName = trim($data['parent_name'] ?? '');
            $nameParts = $parentName ? preg_split('/\s+/', $parentName) : [];
            $hasParent = $parentName || ! empty($data['parent_phone']) || ! empty($data['parent_email'] ?? null);

            $credentials = null;
            if ($hasParent) {
                $parent = $school->parents()->firstOrCreate(
                    ['phone' => $data['parent_phone'] ?? null, 'email' => $data['parent_email'] ?? null],
                    [
                        'first_name' => $data['parent_first_name'] ?? ($nameParts[0] ?? 'Parent'),
                        'last_name' => $data['parent_last_name'] ?? (count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : null),
                        'email' => $data['parent_email'] ?? null,
                        'phone' => $data['parent_phone'] ?? null,
                        'address' => $data['parent_address'] ?? null,
                        'occupation' => $data['parent_occupation'] ?? null,
                        'emergency_contact' => $data['parent_emergency_contact'] ?? null,
                        'relationship_to_student' => $data['relationship'] ?? null,
                        'status' => 'active',
                    ],
                );

                $student->parents()->syncWithoutDetaching([
                    $parent->id => ['relationship' => $data['relationship'] ?? null, 'is_primary' => true],
                ]);

                $credentials = app(PortalAccountService::class)->provisionParent($parent);
            }

            return $credentials;
        });

        return redirect()->route('students.show', $student)
            ->with('success', 'Student record created successfully.')
            ->with('portal_credentials', $credentials);
    }

    private function nextAdmissionNumber(int $schoolId): string
    {
        $lastNumber = Student::where('school_id', $schoolId)
            ->where('admission_no', 'like', 'AD%')
            ->pluck('admission_no')
            ->map(fn (string $admissionNo) => (int) substr($admissionNo, 2))
            ->max() ?: 0;

        return 'AD' . str_pad((string) ($lastNumber + 1), 3, '0', STR_PAD_LEFT);
    }

    private function editParentData(Student $student): array
    {
        $parent = $student->parents()->wherePivot('is_primary', true)->first();

        return [
            'parent_name' => $parent ? trim($parent->first_name . ' ' . $parent->last_name) : '',
            'parent_email' => $parent?->email ?? '',
            'parent_phone' => $parent?->phone ?? '',
            'relationship' => $parent?->pivot?->relationship ?: ($parent?->relationship_to_student ?? ''),
            'parent_address' => $parent?->address ?? '',
            'parent_occupation' => $parent?->occupation ?? '',
            'parent_emergency_contact' => $parent?->emergency_contact ?? '',
        ];
    }

    public function storeNote(Request $request)
    {
        Gate::authorize('manage-school-setup');

        $data = $request->validate([
            'student_id' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'note' => ['required', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'priority' => ['nullable', 'in:low,normal,high,urgent'],
            'visibility' => ['nullable', 'in:private,staff_only,management,parent_visible'],
            'follow_up_date' => ['nullable', 'date'],
            'follow_up_status' => ['nullable', 'in:not_required,pending,complete'],
        ]);

        $school = $request->user()?->school ?? School::query()->firstOrCreate(['name' => 'Hillcrest Academy']);
        $student = $school->students()->whereKey($data['student_id'])->firstOrFail();
        $school->studentNotes()->create([
            'student_id' => $student->id,
            'created_by' => $request->user()?->id,
            'title' => $data['title'],
            'note' => $data['note'],
            'category' => $data['category'] ?? 'General',
            'priority' => $data['priority'] ?? 'normal',
            'visibility' => $data['visibility'] ?? 'staff_only',
            'follow_up_date' => $data['follow_up_date'] ?? null,
            'follow_up_status' => $data['follow_up_status'] ?? 'not_required',
        ]);
        $this->recordTimeline($student, 'Note created', $data['title'], 'note_created');

        return redirect()->route('students.show', $student)->with('success', 'Student note saved successfully.');
    }

    public function storeTimeline(Request $request)
    {
        Gate::authorize('manage-school-setup');

        $data = $request->validate([
            'student_id' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'event_type' => ['nullable', 'string', 'max:100'],
        ]);

        $school = $request->user()?->school ?? School::query()->firstOrCreate(['name' => 'Hillcrest Academy']);

        $student = $school->students()->whereKey($data['student_id'])->firstOrFail();
        $school->studentTimelines()->create([
            'student_id' => $student->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'event_type' => $data['event_type'] ?? 'general',
            'occurred_at' => now(),
        ]);

        return redirect()->route('students.show', $student)->with('success', 'Student timeline entry added successfully.');
    }

    private function recordTimeline(Student $student, string $title, ?string $description, string $eventType): void
    {
        $student->timelines()->create([
            'school_id' => $student->school_id,
            'title' => $title,
            'description' => $description,
            'event_type' => $eventType,
            'occurred_at' => now(),
        ]);
    }
}
