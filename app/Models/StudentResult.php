<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentResult extends Model
{
    use HasFactory;

    protected $fillable = ['school_id', 'assessment_id', 'student_id', 'entered_by', 'marks', 'maximum_marks', 'grade', 'grade_point', 'teacher_comment', 'status'];
    protected $casts = ['marks' => 'decimal:2', 'maximum_marks' => 'decimal:2', 'grade_point' => 'decimal:2'];

    public function school() { return $this->belongsTo(School::class); }
    public function assessment() { return $this->belongsTo(Assessment::class); }
    public function student() { return $this->belongsTo(Student::class); }
    public function enteredBy() { return $this->belongsTo(User::class, 'entered_by'); }
}