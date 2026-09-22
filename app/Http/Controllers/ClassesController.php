<?php

namespace App\Http\Controllers;

use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ClassesController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('manage-school-setup');
        $school = $request->user()->school;
        return Inertia::render('Classes/Index', ['classes' => $school->classes()->with(['branch', 'students'])->where('status', 'active')->orderBy('name')->get()->map(fn ($class) => [
            'id' => $class->id, 'name' => $class->name, 'level' => $class->level, 'branch' => $class->branch?->name,
            'students' => $class->students->sortBy('last_name')->values()->map(fn ($student) => ['id' => $student->id, 'name' => trim($student->first_name.' '.$student->last_name), 'admission_no' => $student->admission_no]),
        ])]);
    }

    public function promote(Request $request, AuditService $audit)
    {
        Gate::authorize('manage-school-setup');
        $data = $request->validate(['student_ids' => ['required', 'array', 'min:1'], 'student_ids.*' => ['integer'], 'class_id' => ['required', 'integer']]);
        $school = $request->user()->school;
        $class = $school->classes()->whereKey($data['class_id'])->firstOrFail();
        $count = $school->students()->whereIn('id', $data['student_ids'])->update(['class_id' => $class->id]);
        $audit->record($request, 'students.promoted', "{$count} student(s) promoted to {$class->name}.", $class, ['student_ids' => $data['student_ids']]);
        return back()->with('success', "{$count} student(s) moved to {$class->name}.");
    }
}