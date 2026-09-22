<?php

use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function timetableAdmin(): array
{
    $school = School::create(['name' => 'Timetable Academy']);
    $role = Role::where('slug', 'super-administrator')->firstOrFail();
    $user = User::factory()->create(['school_id' => $school->id]);
    $user->roles()->attach($role->id);

    return [$user, $school];
}

it('loads the timetable page for the active school', function () {
    [$user, $school] = timetableAdmin();
    $class = $school->classes()->create(['name' => 'Form 1A', 'status' => 'active']);
    $subject = $school->subjects()->create(['name' => 'English', 'code' => 'ENG', 'status' => 'active']);
    $teacher = $school->staff()->create(['first_name' => 'Ada', 'last_name' => 'Owusu', 'role' => 'Teacher', 'position' => 'English Teacher', 'status' => 'active']);

    $school->timetableSlots()->create([
        'class_id' => $class->id,
        'subject_id' => $subject->id,
        'staff_id' => $teacher->id,
        'day_of_week' => 'monday',
        'start_time' => '08:00:00',
        'end_time' => '09:00:00',
        'room' => 'A1',
        'status' => 'active',
    ]);

    $this->actingAs($user)->get('/timetable')->assertOk();
});

it('creates a timetable slot and keeps class-day-time unique', function () {
    [$user, $school] = timetableAdmin();
    $class = $school->classes()->create(['name' => 'Form 1A', 'status' => 'active']);
    $subject = $school->subjects()->create(['name' => 'English', 'code' => 'ENG', 'status' => 'active']);
    $teacher = $school->staff()->create(['first_name' => 'Ada', 'last_name' => 'Owusu', 'role' => 'Teacher', 'position' => 'English Teacher', 'status' => 'active']);

    $this->actingAs($user)->post('/timetable/slots', [
        'class_id' => $class->id,
        'subject_id' => $subject->id,
        'staff_id' => $teacher->id,
        'day_of_week' => 'monday',
        'start_time' => '08:00',
        'end_time' => '09:00',
        'room' => 'A1',
    ])->assertRedirect();

    $this->assertDatabaseHas('timetable_slots', ['class_id' => $class->id, 'subject_id' => $subject->id, 'staff_id' => $teacher->id, 'day_of_week' => 'monday']);

    $this->actingAs($user)->post('/timetable/slots', [
        'class_id' => $class->id,
        'subject_id' => $subject->id,
        'staff_id' => $teacher->id,
        'day_of_week' => 'monday',
        'start_time' => '08:00',
        'end_time' => '09:00',
        'room' => 'A2',
    ])->assertSessionHasErrors('day_of_week');
});

it('rejects timetable slots for teachers or classes outside the school', function () {
    [$user, $school] = timetableAdmin();
    $otherSchool = School::create(['name' => 'Other School']);
    $class = $school->classes()->create(['name' => 'Form 2A', 'status' => 'active']);
    $subject = $school->subjects()->create(['name' => 'Mathematics', 'code' => 'MATH', 'status' => 'active']);
    $otherTeacher = $otherSchool->staff()->create(['first_name' => 'John', 'last_name' => 'Smith', 'role' => 'Teacher', 'position' => 'Math Teacher', 'status' => 'active']);

    $this->actingAs($user)->post('/timetable/slots', [
        'class_id' => $class->id,
        'subject_id' => $subject->id,
        'staff_id' => $otherTeacher->id,
        'day_of_week' => 'tuesday',
        'start_time' => '10:00',
        'end_time' => '11:00',
        'room' => 'B2',
    ])->assertSessionHasErrors('staff_id');
});
