<?php

use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function announcementAdmin(): array
{
    $school = School::create(['name' => 'Notice Academy']);
    $role = Role::where('slug', 'super-administrator')->firstOrFail();
    $user = User::factory()->create(['school_id' => $school->id]);
    $user->roles()->attach($role->id);

    return [$user, $school];
}

it('loads the announcements page and creates a public notice for the active school', function () {
    [$user, $school] = announcementAdmin();

    $this->actingAs($user)->get('/announcements')->assertOk();

    $this->actingAs($user)->post('/announcements', [
        'title' => 'Parents Meeting',
        'message' => 'All parents are invited to the term meeting.',
        'audience' => 'parents',
        'published_at' => '2026-10-02 09:00:00',
        'expires_at' => '2026-10-02 17:00:00',
        'status' => 'published',
    ])->assertRedirect();

    $this->assertDatabaseHas('school_announcements', [
        'school_id' => $school->id,
        'title' => 'Parents Meeting',
        'audience' => 'parents',
        'status' => 'published',
    ]);
});

it('rejects duplicate announcements for the same school and title on the same publish date', function () {
    [$user, $school] = announcementAdmin();

    $this->actingAs($user)->post('/announcements', [
        'title' => 'School Assembly',
        'message' => 'First announcement',
        'audience' => 'all',
        'published_at' => '2026-10-04 08:00:00',
        'status' => 'published',
    ])->assertRedirect();

    $this->actingAs($user)->post('/announcements', [
        'title' => 'School Assembly',
        'message' => 'Second announcement',
        'audience' => 'all',
        'published_at' => '2026-10-04 08:00:00',
        'status' => 'published',
    ])->assertSessionHasErrors('title');
});
