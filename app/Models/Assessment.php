<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = ['school_id', 'examination_id', 'subject_id', 'class_id', 'grading_scale_id', 'name', 'maximum_marks', 'status'];
    protected $casts = ['maximum_marks' => 'decimal:2'];

    public function school() { return $this->belongsTo(School::class); }
    public function examination() { return $this->belongsTo(Examination::class); }
    public function subject() { return $this->belongsTo(Subject::class); }
    public function schoolClass() { return $this->belongsTo(SchoolClass::class, 'class_id'); }
    public function gradingScale() { return $this->belongsTo(GradingScale::class); }
    public function results() { return $this->hasMany(StudentResult::class); }
}