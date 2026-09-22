<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        'attendance_session_id', 'student_id', 'recorded_by', 'status', 'note',
    ];

    public function session() { return $this->belongsTo(AttendanceSession::class, 'attendance_session_id'); }
    public function student() { return $this->belongsTo(Student::class); }
    public function recorder() { return $this->belongsTo(User::class, 'recorded_by'); }
}