<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentNote extends Model
{
    use HasFactory;

    protected $table = 'student_notes';

    protected $fillable = [
        'school_id',
        'student_id',
        'created_by',
        'title',
        'note',
        'category',
        'priority',
        'visibility',
        'follow_up_date',
        'follow_up_status',
        'updated_by',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
