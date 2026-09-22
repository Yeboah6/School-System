<?php

use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function schoolEventAdmin(): array
{
    $school = School::create(['name' => 'Event Academy']);
    $role = Role::where('slug', 'super-administrator')->firstOrFail();
    $user = User::factory()->create(['school_id' => $school->id]);
    $user->roles()->attach($role->id);

    return [$user, $school];
}

it('creates a school event for the active school and keeps the date unique per title', function () {
    [$user, $school] = schoolEventAdmin();

    $this->actingAs($user)->post('/events', [
        'title' => 'Inter-house games',
        'event_type' => 'academic',
        'event_date' => '2026-10-15',
        'location' => 'Main field',
        'description' => 'Annual sports festival',
    ])->assertRedirect();

    $this->assertDatabaseHas('school_events', [
        'school_id' => $school->id,
        'title' => 'Inter-house games',
        'event_type' => 'academic',
        'location' => 'Main field',
    ]);

    $this->actingAs($user)->post('/events', [
        'title' => 'Inter-house games',
        'event_type' => 'academic',
        'event_date' => '2026-10-15',
        'location' => 'Sports arena',
        'description' => 'Duplicate title and date',
    ])->assertSessionHasErrors('title');
});

it('rejects events for schools outside the current school', function () {
    [$user, $school] = schoolEventAdmin();
    $otherSchool = School::create(['name' => 'Other Academy']);

    $this->actingAs($user)->post('/events', [
        'title' => 'Staff Meeting',
        'event_type' => 'meeting',
        'event_date' => '2026-11-01',
        'description' => 'Cross-school check',
    ])->assertRedirect();

    $this->assertDatabaseMissing('school_events', ['school_id' => $otherSchool->id, 'title' => 'Staff Meeting']);
});
