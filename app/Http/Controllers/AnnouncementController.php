<?php

namespace App\Http\Controllers;

use App\Models\SchoolAnnouncement;
use App\Notifications\AnnouncementNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('manage-school-setup');
        $school = $request->user()->school;

        return Inertia::render('Announcements/Index', [
            'announcements' => $school->announcements()->orderBy('published_at', 'desc')->get()->map(fn (SchoolAnnouncement $announcement) => [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'message' => $announcement->message,
                'audience' => $announcement->audience,
                'published_at' => $announcement->published_at?->format('Y-m-d H:i:s'),
                'expires_at' => $announcement->expires_at?->format('Y-m-d H:i:s'),
                'status' => $announcement->status,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-school-setup');
        $school = $request->user()->school;

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'audience' => ['required', 'in:all,students,parents,staff'],
            'published_at' => ['required', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:published_at'],
            'status' => ['required', 'in:draft,published,archived'],
        ]);

        $publishDate = Carbon::parse($data['published_at'])->toDateString();

        if ($school->announcements()->where('title', $data['title'])->whereRaw('DATE(published_at) = ?', [$publishDate])->exists()) {
            return back()->withInput()->withErrors(['title' => 'An announcement with this title already exists for the same publish date.']);
        }

        $announcement = $school->announcements()->create([
            'title' => $data['title'],
            'message' => $data['message'],
            'audience' => $data['audience'],
            'published_at' => $data['published_at'],
            'expires_at' => $data['expires_at'] ?? null,
            'status' => $data['status'],
        ]);

        $audiences = $data['audience'] === 'all' ? ['Parent', 'Teacher'] : match ($data['audience']) {
            'parents' => ['Parent'],
            'staff' => ['Teacher'],
            default => [],
        };

        $school->users()->whereHas('roles', fn ($query) => $query->whereIn('name', $audiences))->each(
            fn ($user) => $user->notify(new AnnouncementNotification($announcement))
        );

        return back()->with('success', 'Announcement published successfully.');
    }
}