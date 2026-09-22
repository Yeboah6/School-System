<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\GradingScaleItem;
use App\Models\Student;
use App\Models\StudentResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ExaminationService
{
    public function saveResults(Assessment $assessment, array $results, int $userId): void
    {
        DB::transaction(function () use ($assessment, $results, $userId): void {
            $students = $assessment->schoolClass
                ? $assessment->schoolClass->students()->whereIn('id', collect($results)->pluck('student_id'))->get()->keyBy('id')
                : Student::where('school_id', $assessment->school_id)->whereIn('id', collect($results)->pluck('student_id'))->get()->keyBy('id');

            if ($students->count() !== count($results)) {
                throw ValidationException::withMessages(['results' => 'Every result student must belong to the assessment school and class.']);
            }

            foreach ($results as $result) {
                $marks = (float) $result['marks'];
                $maximum = (float) $assessment->maximum_marks;
                if ($marks < 0 || $marks > $maximum) {
                    throw ValidationException::withMessages(['results' => "Marks must be between zero and {$maximum}."]);
                }

                $percentage = $maximum > 0 ? ($marks / $maximum) * 100 : 0;
                $scaleItem = $assessment->gradingScale?->items()->where('minimum_mark', '<=', $percentage)->where('maximum_mark', '>=', $percentage)->first();

                StudentResult::updateOrCreate(
                    ['assessment_id' => $assessment->id, 'student_id' => $result['student_id']],
                    [
                        'school_id' => $assessment->school_id,
                        'entered_by' => $userId,
                        'marks' => $marks,
                        'maximum_marks' => $maximum,
                        'grade' => $scaleItem?->grade,
                        'grade_point' => $scaleItem?->grade_point,
                        'teacher_comment' => $result['teacher_comment'] ?? null,
                        'status' => $result['status'] ?? 'draft',
                    ],
                );
            }
        });
    }
}
