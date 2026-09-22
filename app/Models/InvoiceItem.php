<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = ['student_invoice_id', 'fee_structure_id', 'description', 'amount'];
    protected $casts = ['amount' => 'decimal:2'];

    public function invoice() { return $this->belongsTo(StudentInvoice::class, 'student_invoice_id'); }
    public function feeStructure() { return $this->belongsTo(FeeStructure::class); }
}