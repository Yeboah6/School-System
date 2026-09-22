<?php

namespace App\Http\Controllers;

use App\Models\TimetableSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class TimetableController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('manage-school-setup');
        $school = $request->user()->school;
        $query = $school->timetableSlots()->with(['schoolClass', 'subject', 'staff']);
        if ($request->filled('search')) {
            $search = (string) $request->string('search');
            $query->where(function ($query) use ($search) {
                $query->whereHas('subject', fn ($q) => $q->where('name', 'like', '%'.$search.'%'))
                    ->orWhereHas('staff', fn ($q) => $q->where('first_name', 'like', '%'.$search.'%')->orWhere('last_name', 'like', '%'.$search.'%'))
                    ->orWhereHas('schoolClass', fn ($q) => $q->where('name', 'like', '%'.$search.'%'));
            });
        }
        if ($request->filled('day')) $query->where('day_of_week', $request->string('day'));
        if ($request->filled('class_id')) $query->where('class_id', $request->integer('class_id'));
        $slots = $query->orderBy('day_of_week')->orderBy('start_time')->get();

        return Inertia::render('Timetable/Index', [
            'classes' => $school->classes()->where('status', 'active')->orderBy('name')->get(['id', 'name']),
            'subjects' => $school->subjects()->where('status', 'active')->orderBy('name')->get(['id', 'name']),
            'staff' => $school->staff()->where('status', 'active')->orderBy('last_name')->get(['id', 'first_name', 'last_name', 'position']),
            'filters' => $request->only(['search', 'day', 'class_id']),
            'slots' => $slots->map(fn (TimetableSlot $slot) => ['id' => $slot->id, 'class_id' => $slot->class_id, 'subject_id' => $slot->subject_id, 'staff_id' => $slot->staff_id, 'day_of_week' => $slot->day_of_week, 'start_time' => $slot->start_time?->format('H:i'), 'end_time' => $slot->end_time?->format('H:i'), 'room' => $slot->room, 'class' => $slot->schoolClass->name, 'subject' => $slot->subject->name, 'teacher' => trim($slot->staff->first_name.' '.$slot->staff->last_name)]),
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-school-setup');
        $school = $request->user()->school;
        $data = $request->validate([
            'class_id' => ['required', 'integer'],
            'subject_id' => ['required', 'integer'],
            'staff_id' => ['required', 'integer'],
            'day_of_week' => ['required', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'room' => ['nullable', 'string', 'max:100'],
        ]);

        if (! $school->classes()->whereKey($data['class_id'])->exists()) {
            return back()->withInput()->withErrors(['class_id' => 'The selected class does not belong to this school.']);
        }

        if (! $school->subjects()->whereKey($data['subject_id'])->exists()) {
            return back()->withInput()->withErrors(['subject_id' => 'The selected subject does not belong to this school.']);
        }

        if (! $school->staff()->whereKey($data['staff_id'])->exists()) {
            return back()->withInput()->withErrors(['staff_id' => 'The selected teacher does not belong to this school.']);
        }

        $existing = $school->timetableSlots()->where('class_id', $data['class_id'])->where('day_of_week', $data['day_of_week'])->where('start_time', $data['start_time'])->first();
        if ($existing) {
            return back()->withInput()->withErrors(['day_of_week' => 'A class cannot have two lessons at the same time on the same day.']);
        }

        $teacherConflict = $school->timetableSlots()->where('staff_id', $data['staff_id'])->where('day_of_week', $data['day_of_week'])->where('start_time', $data['start_time'])->first();
        if ($teacherConflict) {
            return back()->withInput()->withErrors(['staff_id' => 'This teacher is already assigned at that day and time.']);
        }

        $school->timetableSlots()->create([
            'class_id' => $data['class_id'],
            'subject_id' => $data['subject_id'],
            'staff_id' => $data['staff_id'],
            'day_of_week' => $data['day_of_week'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'room' => $data['room'] ?? null,
            'status' => 'active',
        ]);

        return back()->with('success', 'Timetable slot created successfully.');
    }

    public function update(Request $request, TimetableSlot $slot)
    {
        Gate::authorize('manage-school-setup');
        abort_unless($slot->school_id === $request->user()->school_id, 404);
        $data = $request->validate(['class_id' => ['required', 'integer'], 'subject_id' => ['required', 'integer'], 'staff_id' => ['required', 'integer'], 'day_of_week' => ['required', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'], 'start_time' => ['required', 'date_format:H:i'], 'end_time' => ['required', 'date_format:H:i', 'after:start_time'], 'room' => ['nullable', 'string', 'max:100']]);
        $school = $request->user()->school;
        abort_unless($school->classes()->whereKey($data['class_id'])->exists() && $school->subjects()->whereKey($data['subject_id'])->exists() && $school->staff()->whereKey($data['staff_id'])->exists(), 422);
        $slot->update($data);
        return back()->with('success', 'Timetable slot updated successfully.');
    }
}
