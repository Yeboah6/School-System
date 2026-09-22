<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'student_id',
        'branch_id',
        'academic_year_id',
        'class_id',
        'first_name',
        'last_name',
        'middle_name',
        'gender',
        'nationality',
        'email',
        'phone',
        'address',
        'date_of_birth',
        'admission_date',
        'previous_school',
        'student_type',
        'admission_no',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'admission_date' => 'date',
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

    public function branch()
    {
        return $this->belongsTo(SchoolBranch::class, 'branch_id');
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

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function invoices() { return $this->hasMany(StudentInvoice::class); }
    public function payments() { return $this->hasMany(Payment::class); }
    public function results() { return $this->hasMany(StudentResult::class); }
}
