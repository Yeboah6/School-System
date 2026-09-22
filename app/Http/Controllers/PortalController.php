<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class PortalController extends Controller
{
    public function parent(Request $request)
    {
        $user = $request->user();
        abort_unless($user->hasRole('Parent'), 403);

        $parent = $user->parentProfile()->where('school_id', $user->school_id)->with([
            'students.schoolClass',
            'students.attendance.session',
            'students.results.assessment.subject',
            'students.invoices',
        ])->firstOrFail();

        return Inertia::render('Portals/Parent', [
            'parent' => ['name' => trim($parent->first_name.' '.$parent->last_name), 'email' => $parent->email],
            'students' => $parent->students->map(fn ($student) => [
                'id' => $student->id,
                'name' => trim($student->first_name.' '.$student->last_name),
                'class' => $student->schoolClass?->name,
                'attendance' => $student->attendance->map(fn ($record) => [
                    'date' => $record->session?->session_date?->format('Y-m-d'),
                    'status' => $record->status,
                ])->values(),
                'results' => $student->results->map(fn ($result) => [
                    'subject' => $result->assessment?->subject?->name,
                    'marks' => (float) $result->marks,
                    'grade' => $result->grade,
                ])->values(),
                'invoices' => $student->invoices->map(fn ($invoice) => [
                    'invoice_no' => $invoice->invoice_no,
                    'total' => (float) $invoice->total,
                    'balance' => (float) $invoice->balance,
                    'status' => $invoice->status,
                ])->values(),
            ])->values(),
            'announcements' => $user->school->announcements()->whereIn('audience', ['all', 'parents'])->where('status', 'published')->latest('published_at')->limit(10)->get(['id', 'title', 'message', 'published_at']),
            'notifications' => $user->unreadNotifications()->latest()->limit(10)->get()->map(fn ($notification) => ['id' => $notification->id, 'title' => $notification->data['title'] ?? 'Notification', 'message' => $notification->data['message'] ?? ''])->values(),
        ]);
    }

    public function teacher(Request $request)
    {
        $user = $request->user();
        abort_unless($user->hasRole('Teacher'), 403);

        $teacher = $user->staffProfile()->where('school_id', $user->school_id)->with([
            'timetableSlots.schoolClass',
            'timetableSlots.subject',
        ])->firstOrFail();
        $assignedClassIds = $teacher->timetableSlots->pluck('class_id')->unique()->values();

        return Inertia::render('Portals/Teacher', [
            'teacher' => ['id' => $teacher->id, 'name' => trim($teacher->first_name.' '.$teacher->last_name), 'position' => $teacher->position],
            'schedule' => $teacher->timetableSlots->map(fn ($slot) => [
                'day' => $slot->day_of_week,
                'start_time' => $slot->start_time?->format('H:i'),
                'end_time' => $slot->end_time?->format('H:i'),
                'class' => $slot->schoolClass?->name,
                'subject' => $slot->subject?->name,
                'room' => $slot->room,
            ])->values(),
            'classes' => $user->school->classes()->whereIn('id', $assignedClassIds)->with('students')->get()->map(fn ($class) => [
                'id' => $class->id,
                'name' => $class->name,
                'students' => $class->students->sortBy('last_name')->values()->map(fn ($student) => ['id' => $student->id, 'name' => trim($student->first_name.' '.$student->last_name), 'admission_no' => $student->admission_no])->values(),
            ])->values(),
            'announcements' => $user->school->announcements()->whereIn('audience', ['all', 'staff'])->where('status', 'published')->latest('published_at')->limit(10)->get(['id', 'title', 'message', 'published_at']),
            'notifications' => $user->unreadNotifications()->latest()->limit(10)->get()->map(fn ($notification) => ['id' => $notification->id, 'title' => $notification->data['title'] ?? 'Notification', 'message' => $notification->data['message'] ?? ''])->values(),
        ]);
    }
}