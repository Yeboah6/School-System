<?php

use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function seedSchoolAdminUser(): array
{
    $school = School::create([
        'name' => 'Hillcrest Academy',
        'email' => 'admin@hillcrest.edu.gh',
        'phone' => '+233200000000',
        'currency' => 'GHS',
        'timezone' => 'Africa/Accra',
    ]);

    $role = Role::firstOrCreate([
        'slug' => 'super-administrator',
    ], [
        'name' => 'Super Administrator',
        'description' => 'Full administrative access.',
        'is_system' => true,
    ]);

    $user = User::factory()->create([
        'school_id' => $school->id,
        'email' => 'admin@hillcrest.edu.gh',
        'password' => bcrypt('School@123'),
    ]);

    $user->roles()->syncWithoutDetaching([$role->id]);

    return [$user, $school];
}

it('renders the school setup page for an authenticated admin', function () {
    [$user] = seedSchoolAdminUser();

    $this->actingAs($user)
        ->get('/school')
        ->assertOk();
});

it('stores a new academic year for the active school', function () {
    [$user, $school] = seedSchoolAdminUser();

    $this->actingAs($user)
        ->post('/school/academic-years', [
            'name' => '2026/2027',
            'starts_at' => '2026-09-01',
            'ends_at' => '2027-07-31',
            'is_current' => true,
        ])
        ->assertRedirect('/school');

    $this->assertDatabaseHas('academic_years', [
        'school_id' => $school->id,
        'name' => '2026/2027',
    ]);
});

it('stores a new term for the active academic year', function () {
    [$user, $school] = seedSchoolAdminUser();
    $academicYear = $school->academicYears()->create([
        'name' => '2026/2027',
        'starts_at' => '2026-09-01',
        'ends_at' => '2027-07-31',
        'is_current' => true,
    ]);

    $this->actingAs($user)
        ->post('/school/terms', [
            'academic_year_id' => $academicYear->id,
            'name' => 'Term 1',
            'starts_at' => '2026-09-01',
            'ends_at' => '2026-11-30',
            'is_current' => true,
        ])
        ->assertRedirect('/school');

    $this->assertDatabaseHas('terms', [
        'school_id' => $school->id,
        'academic_year_id' => $academicYear->id,
        'name' => 'Term 1',
    ]);
});

it('stores a new department for the school', function () {
    [$user, $school] = seedSchoolAdminUser();

    $this->actingAs($user)
        ->post('/school/departments', [
            'name' => 'Mathematics',
            'head_name' => 'Mr. Mensah',
            'description' => 'Core maths department',
        ])
        ->assertRedirect('/school');

    $this->assertDatabaseHas('departments', [
        'school_id' => $school->id,
        'name' => 'Mathematics',
    ]);
});

it('stores a new class for the school', function () {
    [$user, $school] = seedSchoolAdminUser();

    $this->actingAs($user)
        ->post('/school/classes', [
            'name' => 'JHS 2',
            'level' => 'JHS',
        ])
        ->assertRedirect('/school');

    $this->assertDatabaseHas('classes', [
        'school_id' => $school->id,
        'name' => 'JHS 2',
    ]);
});

it('stores a new subject for the school', function () {
    [$user, $school] = seedSchoolAdminUser();

    $this->actingAs($user)
        ->post('/school/subjects', [
            'name' => 'Mathematics',
            'code' => 'MATH',
            'description' => 'Core maths subject',
        ])
        ->assertRedirect('/school');

    $this->assertDatabaseHas('subjects', [
        'school_id' => $school->id,
        'name' => 'Mathematics',
        'code' => 'MATH',
    ]);
});

it('stores student and parent records for the school', function () {
    [$user, $school] = seedSchoolAdminUser();

    $this->actingAs($user)
        ->post('/students', [
            'first_name' => 'Ama',
            'last_name' => 'Boateng',
            'gender' => 'Female',
            'date_of_birth' => '2015-01-10',
            'admission_no' => 'STD-101',
            'parent_name' => 'Kwame Boateng',
            'parent_phone' => '+233200001234',
            'relationship' => 'Father',
        ])
        ->assertRedirect('/students');

    $this->assertDatabaseHas('students', [
        'school_id' => $school->id,
        'admission_no' => 'STD-101',
        'first_name' => 'Ama',
    ]);

    $this->assertDatabaseHas('parents', [
        'school_id' => $school->id,
        'phone' => '+233200001234',
    ]);
});

it('stores a teacher profile and student notes and timeline', function () {
    [$user, $school] = seedSchoolAdminUser();
    $student = $school->students()->create([
        'first_name' => 'Kojo',
        'last_name' => 'Mensah',
        'gender' => 'Male',
        'date_of_birth' => '2016-02-20',
        'admission_no' => 'STD-204',
        'status' => 'active',
    ]);

    $this->actingAs($user)
        ->post('/staff', [
            'first_name' => 'Naa',
            'last_name' => 'Adu',
            'role' => 'Mathematics Teacher',
            'department_name' => 'Mathematics',
            'email' => 'naa@hillcrest.edu.gh',
            'phone' => '+233200009999',
        ])
        ->assertRedirect('/staff');

    $this->actingAs($user)
        ->post('/students/notes', [
            'student_id' => $student->id,
            'title' => 'Parent consultation',
            'note' => 'Discussed upcoming exam plans.',
            'category' => 'General',
        ])
        ->assertRedirect('/students');

    $this->actingAs($user)
        ->post('/students/timeline', [
            'student_id' => $student->id,
            'title' => 'Admission complete',
            'description' => 'Student enrolled successfully.',
            'event_type' => 'admission',
        ])
        ->assertRedirect('/students');

    $this->assertDatabaseHas('staff', [
        'school_id' => $school->id,
        'email' => 'naa@hillcrest.edu.gh',
    ]);

    $this->assertDatabaseHas('student_notes', [
        'school_id' => $school->id,
        'student_id' => $student->id,
        'title' => 'Parent consultation',
    ]);

    $this->assertDatabaseHas('student_timelines', [
        'school_id' => $school->id,
        'student_id' => $student->id,
        'title' => 'Admission complete',
    ]);
});

it('allows updating and deleting a school setup record', function () {
    [$user, $school] = seedSchoolAdminUser();
    $department = $school->departments()->create([
        'name' => 'Mathematics',
        'head_name' => 'Mr. Mensah',
        'description' => 'Old description',
        'status' => 'active',
    ]);

    $this->actingAs($user)
        ->put('/school/departments/' . $department->id, [
            'name' => 'Science',
            'head_name' => 'Mrs. Owusu',
            'description' => 'Updated department',
        ])
        ->assertRedirect('/school');

    $this->assertDatabaseHas('departments', [
        'id' => $department->id,
        'name' => 'Science',
        'head_name' => 'Mrs. Owusu',
    ]);

    $this->actingAs($user)
        ->delete('/school/departments/' . $department->id)
        ->assertRedirect('/school');

    $this->assertDatabaseMissing('departments', [
        'id' => $department->id,
    ]);
});
