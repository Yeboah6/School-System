<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Examination;
use App\Models\GradingScale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use App\Services\ExaminationService;

class ExaminationController extends Controller
{
    public function __construct(private readonly ExaminationService $examinations) {}

    public function index(Request $request)
    {
        Gate::authorize('manage-school-setup');
        $school = $request->user()->school;

        return Inertia::render('Examinations/Index', [
            'examinations' => $school->examinations()->with('term')->latest()->get()->map(fn (Examination $exam) => [
                'id' => $exam->id, 'name' => $exam->name, 'type' => $exam->type, 'status' => $exam->status, 'term' => $exam->term?->name,
            ]),
            'assessments' => $school->assessments()->with(['examination', 'subject', 'schoolClass', 'gradingScale'])->latest()->get()->map(fn (Assessment $assessment) => [
                'id' => $assessment->id, 'name' => $assessment->name, 'exam' => $assessment->examination->name,
                'subject' => $assessment->subject->name, 'class' => $assessment->schoolClass?->name, 'maximum_marks' => (float) $assessment->maximum_marks,
            ]),
            'academicYears' => $school->academicYears()->orderByDesc('starts_at')->get(['id', 'name']),
            'terms' => $school->terms()->orderByDesc('starts_at')->get(['id', 'name', 'academic_year_id']),
            'subjects' => $school->subjects()->where('status', 'active')->orderBy('name')->get(['id', 'name']),
            'classes' => $school->classes()->where('status', 'active')->orderBy('name')->get(['id', 'name']),
            'scales' => $school->gradingScales()->with('items')->get(),
        ]);
    }

    public function storeExamination(Request $request)
    {
        Gate::authorize('manage-school-setup');
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'type' => ['required', 'string', 'max:100'], 'academic_year_id' => ['nullable', 'integer'], 'term_id' => ['nullable', 'integer'], 'starts_at' => ['nullable', 'date'], 'ends_at' => ['nullable', 'date'], 'description' => ['nullable', 'string']]);
        $school = $request->user()->school;
        abort_unless(empty($data['academic_year_id']) || $school->academicYears()->whereKey($data['academic_year_id'])->exists(), 404);
        abort_unless(empty($data['term_id']) || $school->terms()->whereKey($data['term_id'])->exists(), 404);
        $school->examinations()->create($data + ['status' => 'draft']);
        return back()->with('success', 'Examination created successfully.');
    }

    public function storeScale(Request $request)
    {
        Gate::authorize('manage-school-setup');
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'items' => ['required', 'array', 'min:1'], 'items.*.grade' => ['required', 'string', 'max:20'], 'items.*.minimum_mark' => ['required', 'numeric', 'min:0', 'max:100'], 'items.*.maximum_mark' => ['required', 'numeric', 'min:0', 'max:100'], 'items.*.grade_point' => ['nullable', 'numeric', 'min:0']]);
        $scale = $request->user()->school->gradingScales()->create(['name' => $data['name'], 'is_default' => false]);
        foreach ($data['items'] as $item) { $scale->items()->create($item + ['remark' => null]); }
        return back()->with('success', 'Grading scale created successfully.');
    }

    public function storeAssessment(Request $request)
    {
        Gate::authorize('manage-school-setup');
        $data = $request->validate(['examination_id' => ['required', 'integer'], 'subject_id' => ['required', 'integer'], 'class_id' => ['nullable', 'integer'], 'grading_scale_id' => ['nullable', 'integer'], 'name' => ['required', 'string', 'max:255'], 'maximum_marks' => ['required', 'numeric', 'gt:0']]);
        $school = $request->user()->school;
        abort_unless($school->examinations()->whereKey($data['examination_id'])->exists(), 404);
        abort_unless($school->subjects()->whereKey($data['subject_id'])->exists(), 404);
        abort_unless(empty($data['class_id']) || $school->classes()->whereKey($data['class_id'])->exists(), 404);
        abort_unless(empty($data['grading_scale_id']) || $school->gradingScales()->whereKey($data['grading_scale_id'])->exists(), 404);
        $school->assessments()->create($data + ['status' => 'draft']);
        return back()->with('success', 'Assessment created successfully.');
    }

    public function results(Request $request, Assessment $assessment)
    {
        Gate::authorize('manage-school-setup');
        abort_unless($assessment->school_id === $request->user()->school_id, 404);
        $assessment->load(['subject', 'schoolClass', 'gradingScale.items', 'results']);
        $students = $assessment->schoolClass ? $assessment->schoolClass->students()->where('status', 'active')->orderBy('last_name')->get() : collect();
        $existing = $assessment->results->keyBy('student_id');

        return Inertia::render('Examinations/Results', [
            'assessment' => ['id' => $assessment->id, 'name' => $assessment->name, 'subject' => $assessment->subject->name, 'class' => $assessment->schoolClass?->name, 'maximum_marks' => (float) $assessment->maximum_marks],
            'students' => $students->map(fn ($student) => ['id' => $student->id, 'name' => trim($student->first_name.' '.$student->last_name), 'admission_no' => $student->admission_no, 'marks' => $existing->get($student->id)?->marks, 'grade' => $existing->get($student->id)?->grade, 'teacher_comment' => $existing->get($student->id)?->teacher_comment]),
        ]);
    }

    public function storeResults(Request $request, Assessment $assessment)
    {
        Gate::authorize('manage-school-setup');
        abort_unless($assessment->school_id === $request->user()->school_id, 404);
        $data = $request->validate(['results' => ['required', 'array', 'min:1'], 'results.*.student_id' => ['required', 'integer'], 'results.*.marks' => ['required', 'numeric'], 'results.*.teacher_comment' => ['nullable', 'string'], 'results.*.status' => ['nullable', 'in:draft,published']]);
        $this->examinations->saveResults($assessment->load(['schoolClass.students', 'gradingScale.items']), $data['results'], $request->user()->id);
        return back()->with('success', 'Results saved successfully.');
    }
}
