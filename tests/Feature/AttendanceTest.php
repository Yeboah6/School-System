<?php

use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function attendanceAdmin(): array
{
    $school = School::create(['name' => 'Attendance Academy']);
    $role = Role::where('slug', 'super-administrator')->firstOrFail();
    $user = User::factory()->create(['school_id' => $school->id]);
    $user->roles()->attach($role->id);

    return [$user, $school];
}

it('records bulk attendance for a class and updates the same session without duplicates', function () {
    [$user, $school] = attendanceAdmin();
    $class = $school->classes()->create(['name' => 'JHS 2', 'status' => 'active']);
    $first = $school->students()->create(['first_name' => 'Ama', 'last_name' => 'Boateng', 'class_id' => $class->id, 'admission_no' => 'AD001', 'status' => 'active']);
    $second = $school->students()->create(['first_name' => 'Kojo', 'last_name' => 'Mensah', 'class_id' => $class->id, 'admission_no' => 'AD002', 'status' => 'active']);

    $payload = [
        'class_id' => $class->id,
        'attendance_date' => '2026-09-19',
        'records' => [
            ['student_id' => $first->id, 'status' => 'present'],
            ['student_id' => $second->id, 'status' => 'absent', 'note' => 'Sick leave'],
        ],
    ];

    $this->actingAs($user)->post('/attendance', $payload)->assertRedirect('/attendance?class_id='.$class->id.'&date=2026-09-19');
    $this->actingAs($user)->post('/attendance', [...$payload, 'records' => [
        ['student_id' => $first->id, 'status' => 'late'],
        ['student_id' => $second->id, 'status' => 'absent'],
    ]])->assertRedirect();

    $this->assertDatabaseCount('attendance_sessions', 1);
    $this->assertDatabaseCount('attendance', 2);
    $this->assertDatabaseHas('attendance', ['student_id' => $first->id, 'status' => 'late']);
});

it('rejects attendance records for students outside the selected class', function () {
    [$user, $school] = attendanceAdmin();
    $class = $school->classes()->create(['name' => 'JHS 2', 'status' => 'active']);
    $otherClass = $school->classes()->create(['name' => 'JHS 3', 'status' => 'active']);
    $student = $school->students()->create(['first_name' => 'Ama', 'last_name' => 'Boateng', 'class_id' => $otherClass->id, 'admission_no' => 'AD001', 'status' => 'active']);

    $this->actingAs($user)->post('/attendance', [
        'class_id' => $class->id,
        'attendance_date' => '2026-09-19',
        'records' => [['student_id' => $student->id, 'status' => 'present']],
    ])->assertStatus(422);
});