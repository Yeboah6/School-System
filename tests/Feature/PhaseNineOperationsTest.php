<?php

use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function phaseNineAdmin(): array
{
    $school = School::create(['name' => 'Operations School']);
    $role = Role::where('slug', 'school-administrator')->firstOrFail();
    $user = User::factory()->create(['school_id' => $school->id, 'password' => bcrypt('old-password')]);
    $user->roles()->attach($role->id);

    return [$user, $school];
}

it('changes the authenticated users password through profile settings', function () {
    [$user] = phaseNineAdmin();

    $this->actingAs($user)->put('/profile/password', [
        'current_password' => 'old-password',
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ])->assertRedirect();

    $this->post('/logout');
    $this->post('/login', ['email' => $user->email, 'password' => 'new-password-123'])->assertRedirect('/dashboard');
});

it('generates employee ids and records staff audit activity', function () {
    [$admin, $school] = phaseNineAdmin();

    $this->actingAs($admin)->post('/staff', [
        'first_name' => 'Grace',
        'last_name' => 'Adu',
        'role' => 'Teacher',
    ])->assertRedirect('/staff');

    $staff = $school->staff()->firstOrFail();
    expect($staff->employee_id)->toStartWith('EMP-');
    $this->assertDatabaseHas('audit_logs', ['action' => 'staff.created', 'auditable_id' => $staff->id]);
});

it('promotes selected students and records the promotion audit', function () {
    [$admin, $school] = phaseNineAdmin();
    $oldClass = $school->classes()->create(['name' => 'Class 1', 'level' => 'Primary', 'status' => 'active']);
    $newClass = $school->classes()->create(['name' => 'Class 2', 'level' => 'Primary', 'status' => 'active']);
    $student = $school->students()->create(['class_id' => $oldClass->id, 'first_name' => 'Esi', 'last_name' => 'Doe', 'admission_no' => 'AD901', 'status' => 'active']);

    $this->actingAs($admin)->post('/classes/promote', ['student_ids' => [$student->id], 'class_id' => $newClass->id])->assertRedirect();

    expect($student->fresh()->class_id)->toBe($newClass->id);
    $this->assertDatabaseHas('audit_logs', ['action' => 'students.promoted', 'auditable_id' => $newClass->id]);
});

it('allows a teacher to record attendance only for assigned classes', function () {
    [$admin, $school] = phaseNineAdmin();
    $teacherRole = Role::where('name', 'Teacher')->firstOrFail();
    $teacherUser = User::factory()->create(['school_id' => $school->id]);
    $teacherUser->roles()->attach($teacherRole->id);
    $teacher = $school->staff()->create(['user_id' => $teacherUser->id, 'first_name' => 'Teacher', 'last_name' => 'One', 'role' => 'Teacher', 'status' => 'active']);
    $class = $school->classes()->create(['name' => 'Assigned Class', 'status' => 'active']);
    $subject = $school->subjects()->create(['name' => 'Math', 'code' => 'MATH', 'status' => 'active']);
    $student = $school->students()->create(['class_id' => $class->id, 'first_name' => 'Ama', 'last_name' => 'Doe', 'admission_no' => 'AD902', 'status' => 'active']);
    $class->timetableSlots()->create(['school_id' => $school->id, 'subject_id' => $subject->id, 'staff_id' => $teacher->id, 'day_of_week' => 'monday', 'start_time' => '08:00', 'end_time' => '09:00', 'status' => 'active']);

    $this->actingAs($teacherUser)->post('/attendance', ['class_id' => $class->id, 'attendance_date' => '2026-09-22', 'records' => [['student_id' => $student->id, 'status' => 'present']]])->assertRedirect();
    $this->actingAs($teacherUser)->get('/attendance/report')->assertForbidden();
});

it('updates timetable slots', function () {
    [$admin, $school] = phaseNineAdmin();
    $class = $school->classes()->create(['name' => 'Class A', 'status' => 'active']);
    $subject = $school->subjects()->create(['name' => 'Math', 'code' => 'MATH', 'status' => 'active']);
    $teacher = $school->staff()->create(['first_name' => 'Teacher', 'last_name' => 'One', 'role' => 'Teacher', 'status' => 'active']);
    $slot = $school->timetableSlots()->create(['class_id' => $class->id, 'subject_id' => $subject->id, 'staff_id' => $teacher->id, 'day_of_week' => 'monday', 'start_time' => '08:00', 'end_time' => '09:00', 'status' => 'active']);

    $this->actingAs($admin)->put('/timetable/slots/'.$slot->id, ['class_id' => $class->id, 'subject_id' => $subject->id, 'staff_id' => $teacher->id, 'day_of_week' => 'tuesday', 'start_time' => '10:00', 'end_time' => '11:00', 'room' => 'B2'])->assertRedirect();
    $this->assertDatabaseHas('timetable_slots', ['id' => $slot->id, 'day_of_week' => 'tuesday', 'room' => 'B2']);
});
