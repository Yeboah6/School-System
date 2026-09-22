<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentInvoice extends Model
{
    use HasFactory;

    protected $fillable = ['school_id', 'student_id', 'academic_year_id', 'term_id', 'invoice_no', 'issued_at', 'due_date', 'subtotal', 'discount', 'total', 'paid', 'balance', 'status'];
    protected $casts = ['issued_at' => 'date', 'due_date' => 'date', 'subtotal' => 'decimal:2', 'discount' => 'decimal:2', 'total' => 'decimal:2', 'paid' => 'decimal:2', 'balance' => 'decimal:2'];

    public function school() { return $this->belongsTo(School::class); }
    public function student() { return $this->belongsTo(Student::class); }
    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    public function term() { return $this->belongsTo(Term::class); }
    public function items() { return $this->hasMany(InvoiceItem::class); }
    public function payments() { return $this->hasMany(Payment::class); }
}