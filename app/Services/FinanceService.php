<?php

namespace App\Services;

use App\Models\FeeStructure;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentInvoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FinanceService
{
    public function createInvoice(Student $student, array $data): StudentInvoice
    {
        return DB::transaction(function () use ($student, $data): StudentInvoice {
            $structures = FeeStructure::query()
                ->with('feeType')
                ->where('school_id', $student->school_id)
                ->whereIn('id', $data['fee_structure_ids'])
                ->get();

            if ($structures->count() !== count($data['fee_structure_ids'])) {
                throw ValidationException::withMessages(['fee_structure_ids' => 'Every fee item must belong to the student school.']);
            }

            $subtotal = (float) $structures->sum('amount');
            $discount = (float) ($data['discount'] ?? 0);
            if ($discount < 0 || $discount > $subtotal) {
                throw ValidationException::withMessages(['discount' => 'Discount must be between zero and the invoice subtotal.']);
            }

            $total = round($subtotal - $discount, 2);
            $invoice = StudentInvoice::create([
                'school_id' => $student->school_id,
                'student_id' => $student->id,
                'academic_year_id' => $data['academic_year_id'] ?? null,
                'term_id' => $data['term_id'] ?? null,
                'invoice_no' => $this->nextInvoiceNumber($student->school_id),
                'issued_at' => $data['issued_at'] ?? now()->toDateString(),
                'due_date' => $data['due_date'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
                'paid' => 0,
                'balance' => $total,
                'status' => $total > 0 ? 'pending' : 'paid',
            ]);

            foreach ($structures as $structure) {
                $invoice->items()->create([
                    'fee_structure_id' => $structure->id,
                    'description' => $structure->feeType->name,
                    'amount' => $structure->amount,
                ]);
            }

            return $invoice->load('items');
        });
    }

    public function recordPayment(StudentInvoice $invoice, array $data, int $userId): Payment
    {
        return DB::transaction(function () use ($invoice, $data, $userId): Payment {
            $invoice->refresh();
            $amount = (float) $data['amount'];
            if ($amount <= 0 || $amount > (float) $invoice->balance) {
                throw ValidationException::withMessages(['amount' => 'Payment must be greater than zero and cannot exceed the invoice balance.']);
            }

            $payment = $invoice->payments()->create([
                'school_id' => $invoice->school_id,
                'student_id' => $invoice->student_id,
                'recorded_by' => $userId,
                'receipt_no' => $this->nextReceiptNumber($invoice->school_id),
                'amount' => $amount,
                'method' => $data['method'],
                'reference' => $data['reference'] ?? null,
                'paid_at' => $data['paid_at'] ?? now()->toDateString(),
                'notes' => $data['notes'] ?? null,
                'status' => 'completed',
            ]);

            $paid = round((float) $invoice->paid + $amount, 2);
            $balance = round((float) $invoice->total - $paid, 2);
            $invoice->update(['paid' => $paid, 'balance' => $balance, 'status' => $balance <= 0 ? 'paid' : 'partial']);

            return $payment;
        });
    }

    private function nextInvoiceNumber(int $schoolId): string
    {
        $last = StudentInvoice::where('school_id', $schoolId)->latest('id')->value('invoice_no');
        return 'INV-'.str_pad((string) (((int) preg_replace('/\D/', '', $last ?: '0')) + 1), 5, '0', STR_PAD_LEFT);
    }

    private function nextReceiptNumber(int $schoolId): string
    {
        $last = Payment::where('school_id', $schoolId)->latest('id')->value('receipt_no');
        return 'RCT-'.str_pad((string) (((int) preg_replace('/\D/', '', $last ?: '0')) + 1), 5, '0', STR_PAD_LEFT);
    }
}
