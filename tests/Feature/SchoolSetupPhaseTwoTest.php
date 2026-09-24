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
    $academicYear = $school->academicYears()->create([
        'name' => '2026/2027',
        'starts_at' => '2026-09-01',
        'ends_at' => '2027-07-31',
        'is_current' => true,
    ]);

    $this->actingAs($user)
        ->post('/school/classes', [
            'name' => 'JHS 2',
            'level' => 'JHS',
            'academic_year_id' => $academicYear->id,
        ])
        ->assertRedirect('/school');

    $this->assertDatabaseHas('classes', [
        'school_id' => $school->id,
        'name' => 'JHS 2',
        'academic_year_id' => $academicYear->id,
    ]);
});

it('includes the academic year on the class details payload', function () {
    [$user, $school] = seedSchoolAdminUser();
    $academicYear = $school->academicYears()->create([
        'name' => '2026/2027',
        'starts_at' => '2026-09-01',
        'ends_at' => '2027-07-31',
        'is_current' => true,
    ]);
    $school->classes()->create([
        'name' => 'JHS 2',
        'level' => 'JHS',
        'academic_year_id' => $academicYear->id,
        'status' => 'active',
    ]);

    $this->actingAs($user)
        ->get('/school/details')
        ->assertOk()
        ->assertJsonPath('classes.0.academic_year_id', $academicYear->id)
        ->assertJsonPath('classes.0.academicYear.name', '2026/2027');
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

it('creates a branch and assigns a student with an automatic admission number', function () {
    [$user, $school] = seedSchoolAdminUser();
    $branch = $school->branches()->create([
        'name' => 'North Campus',
        'code' => 'north',
        'status' => 'active',
    ]);
    $class = $school->classes()->create([
        'branch_id' => $branch->id,
        'name' => 'JHS 1A',
        'level' => 'JHS',
        'status' => 'active',
    ]);

    $this->actingAs($user)
        ->post('/students', [
            'first_name' => 'Yaw',
            'last_name' => 'Mensah',
            'branch_id' => $branch->id,
            'class_id' => $class->id,
            'parent_name' => 'Ama Mensah',
            'parent_email' => 'ama@example.test',
            'parent_phone' => '+233200001111',
            'parent_address' => 'Accra',
            'parent_occupation' => 'Accountant',
            'parent_emergency_contact' => '+233200001112',
            'relationship' => 'Mother',
        ])
        ->assertRedirect('/students');

    $student = $school->students()->where('first_name', 'Yaw')->firstOrFail();
    expect($student->admission_no)->toBe('AD001')
        ->and($student->branch_id)->toBe($branch->id)
        ->and($student->class_id)->toBe($class->id);

    $this->assertDatabaseHas('parents', [
        'school_id' => $school->id,
        'email' => 'ama@example.test',
        'occupation' => 'Accountant',
        'emergency_contact' => '+233200001112',
    ]);
    $this->assertDatabaseHas('student_parent', [
        'student_id' => $student->id,
        'relationship' => 'Mother',
        'is_primary' => true,
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

it('updates a student lifecycle record and stores staff and parent phase three details', function () {
    [$user, $school] = seedSchoolAdminUser();
    $branch = $school->branches()->create(['name' => 'East Campus', 'code' => 'EAST', 'status' => 'active']);
    $student = $school->students()->create(['first_name' => 'Kojo', 'last_name' => 'Mensah', 'admission_no' => 'AD001', 'status' => 'active']);

    $this->actingAs($user)->put('/students/'.$student->id, [
        'first_name' => 'Kojo', 'last_name' => 'Mensah', 'gender' => 'Male', 'nationality' => 'Ghanaian',
        'email' => 'kojo@example.test', 'phone' => '+233200000003', 'status' => 'graduated', 'branch_id' => $branch->id,
    ])->assertRedirect('/students/'.$student->id);

    $this->actingAs($user)->post('/parents', [
        'first_name' => 'Adwoa', 'last_name' => 'Mensah', 'email' => 'adwoa@example.test', 'phone' => '+233200000004',
        'occupation' => 'Nurse', 'relationship_to_student' => 'Mother',
    ])->assertRedirect();

    $this->actingAs($user)->post('/staff', [
        'first_name' => 'Daniel', 'last_name' => 'Adu', 'role' => 'Teacher', 'branch_id' => $branch->id,
        'employee_id' => 'EMP-001', 'qualification' => 'B.Ed Mathematics', 'joining_date' => '2026-09-01',
    ])->assertRedirect('/staff');

    $this->assertDatabaseHas('students', ['id' => $student->id, 'status' => 'graduated', 'branch_id' => $branch->id, 'email' => 'kojo@example.test']);
    $this->assertDatabaseHas('parents', ['email' => 'adwoa@example.test', 'occupation' => 'Nurse']);
    $this->assertDatabaseHas('staff', ['employee_id' => 'EMP-001', 'branch_id' => $branch->id]);
    $this->assertDatabaseHas('student_timelines', ['student_id' => $student->id, 'event_type' => 'student_updated']);
});

it('does not allow a user to create a note for another school student', function () {
    [$user, $school] = seedSchoolAdminUser();
    $otherSchool = School::create(['name' => 'Other School']);
    $student = $otherSchool->students()->create(['first_name' => 'Other', 'last_name' => 'Student', 'admission_no' => 'AD001']);

    $this->actingAs($user)->post('/students/notes', [
        'student_id' => $student->id, 'title' => 'Should fail', 'note' => 'Cross-school note',
    ])->assertNotFound();
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
