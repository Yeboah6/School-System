<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    use HasFactory;

    protected $fillable = ['school_id', 'academic_year_id', 'term_id', 'class_id', 'fee_type_id', 'amount', 'due_date', 'status'];
    protected $casts = ['amount' => 'decimal:2', 'due_date' => 'date'];

    public function school() { return $this->belongsTo(School::class); }
    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    public function term() { return $this->belongsTo(Term::class); }
    public function schoolClass() { return $this->belongsTo(SchoolClass::class, 'class_id'); }
    public function feeType() { return $this->belongsTo(FeeType::class); }
}