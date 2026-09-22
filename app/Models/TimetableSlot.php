<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimetableSlot extends Model
{
    use HasFactory;

    protected $fillable = ['school_id', 'class_id', 'subject_id', 'staff_id', 'day_of_week', 'start_time', 'end_time', 'room', 'status'];

    protected $casts = [
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
    ];

    public function school() { return $this->belongsTo(School::class); }
    public function schoolClass() { return $this->belongsTo(SchoolClass::class, 'class_id'); }
    public function subject() { return $this->belongsTo(Subject::class); }
    public function staff() { return $this->belongsTo(Staff::class, 'staff_id'); }
}
