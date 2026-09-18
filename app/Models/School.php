<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'motto',
        'email',
        'phone',
        'address',
        'website',
        'registration_number',
        'school_type',
        'principal_name',
        'currency',
        'timezone',
        'logo_path',
        'status',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function academicYears()
    {
        return $this->hasMany(AcademicYear::class);
    }

    public function terms()
    {
        return $this->hasMany(Term::class);
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function classes()
    {
        return $this->hasMany(SchoolClass::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function parents()
    {
        return $this->hasMany(ParentGuardian::class, 'school_id');
    }

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function studentNotes()
    {
        return $this->hasMany(StudentNote::class);
    }

    public function studentTimelines()
    {
        return $this->hasMany(StudentTimeline::class);
    }
}
