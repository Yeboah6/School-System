<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Examination extends Model
{
    use HasFactory;

    protected $fillable = ['school_id', 'academic_year_id', 'term_id', 'name', 'type', 'starts_at', 'ends_at', 'status', 'description'];
    protected $casts = ['starts_at' => 'date', 'ends_at' => 'date'];

    public function school() { return $this->belongsTo(School::class); }
    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    public function term() { return $this->belongsTo(Term::class); }
    public function assessments() { return $this->hasMany(Assessment::class); }
}