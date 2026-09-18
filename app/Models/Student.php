<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'class_id',
        'first_name',
        'last_name',
        'middle_name',
        'gender',
        'date_of_birth',
        'admission_no',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function parents()
    {
        return $this->belongsToMany(ParentGuardian::class, 'student_parent', 'student_id', 'parent_id')
            ->withPivot('relationship', 'is_primary')
            ->withTimestamps();
    }

    public function notes()
    {
        return $this->hasMany(StudentNote::class);
    }

    public function timelines()
    {
        return $this->hasMany(StudentTimeline::class)->orderByDesc('occurred_at');
    }
}
