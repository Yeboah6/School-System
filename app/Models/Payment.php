<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['school_id', 'student_invoice_id', 'student_id', 'recorded_by', 'receipt_no', 'amount', 'method', 'reference', 'paid_at', 'notes', 'status'];
    protected $casts = ['amount' => 'decimal:2', 'paid_at' => 'date'];

    public function school() { return $this->belongsTo(School::class); }
    public function invoice() { return $this->belongsTo(StudentInvoice::class, 'student_invoice_id'); }
    public function student() { return $this->belongsTo(Student::class); }
    public function recorder() { return $this->belongsTo(User::class, 'recorded_by'); }
}