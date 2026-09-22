<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id', 'academic_year_id', 'term_id', 'class_id', 'branch_id', 'recorded_by',
        'attendance_date', 'session', 'notes',
    ];

    protected $casts = ['attendance_date' => 'date'];

    public function school() { return $this->belongsTo(School::class); }
    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    public function term() { return $this->belongsTo(Term::class); }
    public function schoolClass() { return $this->belongsTo(SchoolClass::class, 'class_id'); }
    public function branch() { return $this->belongsTo(SchoolBranch::class, 'branch_id'); }
    public function recorder() { return $this->belongsTo(User::class, 'recorded_by'); }
    public function records() { return $this->hasMany(Attendance::class); }
}