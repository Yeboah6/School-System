<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';

    protected $fillable = [
        'school_id',
        'user_id',
        'branch_id',
        'employee_id',
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'address',
        'role',
        'position',
        'qualification',
        'employment_type',
        'joining_date',
        'department_name',
        'email',
        'phone',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function branch()
    {
        return $this->belongsTo(SchoolBranch::class, 'branch_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function timetableSlots()
    {
        return $this->hasMany(TimetableSlot::class);
    }

    public function classes()
    {
        return $this->belongsToMany(SchoolClass::class, 'class_staff', 'staff_id', 'class_id')
            ->withPivot('is_primary')
            ->withTimestamps();
    }
}
