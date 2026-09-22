<?php

namespace App\Http\Controllers;

use App\Models\SchoolEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class EventController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('manage-school-setup');
        $school = $request->user()->school;

        return Inertia::render('Events/Index', [
            'events' => $school->events()->orderBy('event_date')->get()->map(fn (SchoolEvent $event) => [
                'id' => $event->id,
                'title' => $event->title,
                'event_type' => $event->event_type,
                'event_date' => $event->event_date?->format('Y-m-d'),
                'location' => $event->location,
                'description' => $event->description,
                'status' => $event->status,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-school-setup');
        $school = $request->user()->school;

        $data = $request->validate([
            'school_id' => ['nullable', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'event_type' => ['required', 'in:academic,meeting,holiday,exam,other'],
            'event_date' => ['required', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        if (isset($data['school_id']) && (int) $data['school_id'] !== $school->id) {
            return back()->withInput()->withErrors(['school_id' => 'The selected school does not belong to this account.']);
        }

        if ($school->events()->where('title', $data['title'])->whereDate('event_date', $data['event_date'])->exists()) {
            return back()->withInput()->withErrors(['title' => 'An event with this title already exists on the same date.']);
        }

        $school->events()->create([
            'title' => $data['title'],
            'event_type' => $data['event_type'],
            'event_date' => $data['event_date'],
            'location' => $data['location'] ?? null,
            'description' => $data['description'] ?? null,
            'status' => 'scheduled',
        ]);

        return back()->with('success', 'School event created successfully.');
    }
}
