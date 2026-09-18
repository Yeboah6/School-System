<?php

namespace App\Http\Controllers;

use App\Models\ParentGuardian;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentNote;
use App\Models\StudentTimeline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('manage-school-setup');

        $school = $request->user()?->school ?? School::query()->firstOrCreate(['name' => 'Hillcrest Academy']);
        $students = $school->students()->orderBy('created_at', 'desc')->get();
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

        return Inertia::render('Students/Create');
    }

    public function show(Request $request, Student $student)
    {
        Gate::authorize('manage-school-setup');

        abort_unless($student->school_id === $request->user()?->school_id, 404);

        $student->load(['parents', 'notes', 'timelines']);

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
            ],
            'parents' => $student->parents->map(fn ($parent) => [
                'id' => $parent->id,
                'name' => trim($parent->first_name . ' ' . $parent->last_name),
                'phone' => $parent->phone,
                'relationship' => $parent->pivot->relationship ?: $parent->relationship_to_student,
                'is_primary' => (bool) $parent->pivot->is_primary,
            ]),
            'notes' => $student->notes->map(fn ($note) => [
                'id' => $note->id,
                'title' => $note->title,
                'note' => $note->note,
                'category' => $note->category,
                'created_at' => $note->created_at->format('Y-m-d H:i'),
            ]),
            'timelines' => $student->timelines->map(fn ($timeline) => [
                'id' => $timeline->id,
                'title' => $timeline->title,
                'description' => $timeline->description,
                'event_type' => $timeline->event_type,
                'occurred_at' => $timeline->occurred_at?->format('Y-m-d H:i'),
            ]),
        ]);
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
            'admission_no' => ['nullable', 'string', 'max:100'],
            'parent_name' => ['nullable', 'string', 'max:255'],
            'parent_phone' => ['nullable', 'string', 'max:255'],
            'relationship' => ['nullable', 'string', 'max:255'],
        ]);

        $school = $request->user()?->school ?? School::query()->firstOrCreate(['name' => 'Hillcrest Academy']);

        $student = $school->students()->create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'gender' => $data['gender'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'admission_no' => $data['admission_no'] ?? null,
            'status' => 'active',
        ]);

        if (! empty($data['parent_name']) || ! empty($data['parent_phone'])) {
            $parent = $school->parents()->firstOrCreate([
                'phone' => $data['parent_phone'] ?: null,
            ], [
                'first_name' => $data['parent_name'] ? explode(' ', $data['parent_name'])[0] : 'Parent',
                'last_name' => $data['parent_name'] ? trim(str_replace(explode(' ', $data['parent_name'])[0], '', $data['parent_name'])) : null,
                'phone' => $data['parent_phone'] ?? null,
                'relationship_to_student' => $data['relationship'] ?? null,
                'status' => 'active',
            ]);

            $student->parents()->syncWithoutDetaching([
                $parent->id => [
                    'relationship' => $data['relationship'] ?? null,
                    'is_primary' => true,
                ],
            ]);
        }

        return redirect()->route('students.index')->with('success', 'Student record created successfully.');
    }

    public function storeNote(Request $request)
    {
        Gate::authorize('manage-school-setup');

        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'title' => ['required', 'string', 'max:255'],
            'note' => ['required', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
        ]);

        $school = $request->user()?->school ?? School::query()->firstOrCreate(['name' => 'Hillcrest Academy']);
        $school->studentNotes()->create([
            'student_id' => $data['student_id'],
            'created_by' => $request->user()?->id,
            'title' => $data['title'],
            'note' => $data['note'],
            'category' => $data['category'] ?? 'General',
        ]);

        return redirect()->route('students.index')->with('success', 'Student note saved successfully.');
    }

    public function storeTimeline(Request $request)
    {
        Gate::authorize('manage-school-setup');

        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'event_type' => ['nullable', 'string', 'max:100'],
        ]);

        $school = $request->user()?->school ?? School::query()->firstOrCreate(['name' => 'Hillcrest Academy']);

        $school->studentTimelines()->create([
            'student_id' => $data['student_id'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'event_type' => $data['event_type'] ?? 'general',
            'occurred_at' => now(),
        ]);

        return redirect()->route('students.index')->with('success', 'Student timeline entry added successfully.');
    }
}
