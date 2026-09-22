<?php

use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function examinationAdmin(): array
{
    $school = School::create(['name' => 'Academic Academy']);
    $role = Role::where('slug', 'super-administrator')->firstOrFail();
    $user = User::factory()->create(['school_id' => $school->id]);
    $user->roles()->attach($role->id);

    return [$user, $school];
}

it('creates an assessment and calculates configurable grades when saving results', function () {
    [$user, $school] = examinationAdmin();
    $exam = $school->examinations()->create(['name' => 'End of Term', 'type' => 'end_of_term', 'status' => 'draft']);
    $subject = $school->subjects()->create(['name' => 'Mathematics', 'code' => 'MATH', 'status' => 'active']);
    $class = $school->classes()->create(['name' => 'JHS 2', 'status' => 'active']);
    $scale = $school->gradingScales()->create(['name' => 'Standard', 'is_default' => true]);
    $scale->items()->create(['grade' => 'A', 'minimum_mark' => 80, 'maximum_mark' => 100, 'grade_point' => 4]);
    $scale->items()->create(['grade' => 'F', 'minimum_mark' => 0, 'maximum_mark' => 79.99, 'grade_point' => 0]);
    $student = $school->students()->create(['first_name' => 'Ama', 'last_name' => 'Boateng', 'class_id' => $class->id, 'admission_no' => 'AD001', 'status' => 'active']);

    $this->actingAs($user)->post('/examinations/assessments', [
        'examination_id' => $exam->id, 'subject_id' => $subject->id, 'class_id' => $class->id,
        'grading_scale_id' => $scale->id, 'name' => 'Mathematics Paper', 'maximum_marks' => 100,
    ])->assertRedirect();

    $assessment = $exam->assessments()->firstOrFail();
    $this->actingAs($user)->post('/examinations/assessments/'.$assessment->id.'/results', [
        'results' => [['student_id' => $student->id, 'marks' => 85, 'status' => 'published']],
    ])->assertRedirect();

    $this->assertDatabaseHas('student_results', ['assessment_id' => $assessment->id, 'student_id' => $student->id, 'grade' => 'A', 'status' => 'published']);
});

it('rejects marks above the assessment maximum', function () {
    [$user, $school] = examinationAdmin();
    $exam = $school->examinations()->create(['name' => 'Quiz', 'type' => 'test', 'status' => 'draft']);
    $subject = $school->subjects()->create(['name' => 'Science', 'code' => 'SCI', 'status' => 'active']);
    $class = $school->classes()->create(['name' => 'JHS 1', 'status' => 'active']);
    $student = $school->students()->create(['first_name' => 'Kojo', 'last_name' => 'Mensah', 'class_id' => $class->id, 'admission_no' => 'AD001', 'status' => 'active']);
    $assessment = $school->assessments()->create(['examination_id' => $exam->id, 'subject_id' => $subject->id, 'class_id' => $class->id, 'name' => 'Science Quiz', 'maximum_marks' => 50, 'status' => 'draft']);

    $this->actingAs($user)->post('/examinations/assessments/'.$assessment->id.'/results', [
        'results' => [['student_id' => $student->id, 'marks' => 51]],
    ])->assertSessionHasErrors('results');
});
