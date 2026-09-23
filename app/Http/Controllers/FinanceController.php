<?php

namespace App\Http\Controllers;

use App\Models\FeeStructure;
use App\Models\FeeType;
use App\Models\StudentInvoice;
use App\Services\FinanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use App\Services\AuditService;

class FinanceController extends Controller
{
    public function __construct(private readonly FinanceService $finance) {}

    public function index(Request $request)
    {
        Gate::authorize('manage-finance');
        $school = $request->user()->school;
        $invoices = $school->invoices()->with('student')->latest()->paginate(20)->withQueryString();

        return Inertia::render('Finance/Index', [
            'canManageFeeSetup' => $request->user()->roles()->whereIn('slug', ['super-administrator', 'school-administrator', 'principal'])->exists(),
            'feeTypes' => $school->feeTypes()->where('status', 'active')->orderBy('name')->get(['id', 'name', 'code']),
            'structures' => $school->feeStructures()->with(['feeType', 'schoolClass'])->latest()->get()->map(fn (FeeStructure $structure) => [
                'id' => $structure->id, 'fee_type' => $structure->feeType->name, 'class' => $structure->schoolClass?->name,
                'amount' => (float) $structure->amount, 'due_date' => $structure->due_date?->format('Y-m-d'),
            ]),
            'students' => $school->students()->where('status', 'active')->orderBy('last_name')->get(['id', 'first_name', 'last_name', 'admission_no']),
            'invoices' => $invoices->through(fn (StudentInvoice $invoice) => [
                'id' => $invoice->id, 'invoice_no' => $invoice->invoice_no,
                'student' => trim($invoice->student->first_name.' '.$invoice->student->last_name),
                'total' => (float) $invoice->total, 'paid' => (float) $invoice->paid,
                'balance' => (float) $invoice->balance, 'status' => $invoice->status,
            ]),
            'summary' => [
                'invoiced' => (float) $school->invoices()->sum('total'),
                'collected' => (float) $school->payments()->where('status', 'completed')->sum('amount'),
                'outstanding' => (float) $school->invoices()->sum('balance'),
            ],
        ]);
    }

    public function storeFeeType(Request $request, AuditService $audit)
    {
        Gate::authorize('manage-finance');
        $this->authorizeFeeSetup($request);
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'code' => ['required', 'string', 'max:30']]);
        $feeType = $request->user()->school->feeTypes()->create([...$data, 'code' => strtoupper($data['code']), 'status' => 'active']);
        $audit->record($request, 'finance.fee_type_created', 'Fee type created.', $feeType);
        return back()->with('success', 'Fee type created successfully.');
    }

    public function storeStructure(Request $request, AuditService $audit)
    {
        Gate::authorize('manage-finance');
        $this->authorizeFeeSetup($request);
        $data = $request->validate([
            'fee_type_id' => ['required', 'integer'], 'academic_year_id' => ['nullable', 'integer'], 'term_id' => ['nullable', 'integer'],
            'class_id' => ['nullable', 'integer'], 'amount' => ['required', 'numeric', 'min:0'], 'due_date' => ['nullable', 'date'],
        ]);
        $school = $request->user()->school;
        abort_unless($school->feeTypes()->whereKey($data['fee_type_id'])->exists(), 404);
        $structure = $school->feeStructures()->create([...$data, 'status' => 'active']);
        $audit->record($request, 'finance.structure_created', 'Fee structure created.', $structure);
        return back()->with('success', 'Fee structure created successfully.');
    }

    public function storeInvoice(Request $request, AuditService $audit)
    {
        Gate::authorize('manage-finance');
        $data = $request->validate([
            'student_id' => ['required', 'integer'], 'fee_structure_ids' => ['required', 'array', 'min:1'],
            'fee_structure_ids.*' => ['integer'], 'academic_year_id' => ['nullable', 'integer'], 'term_id' => ['nullable', 'integer'],
            'discount' => ['nullable', 'numeric', 'min:0'], 'issued_at' => ['nullable', 'date'], 'due_date' => ['nullable', 'date'],
        ]);
        $school = $request->user()->school;
        $student = $school->students()->whereKey($data['student_id'])->firstOrFail();
        $invoice = $this->finance->createInvoice($student, $data);
        $audit->record($request, 'finance.invoice_created', 'Student invoice created.', $invoice);
        return back()->with('success', 'Invoice created successfully.');
    }

    public function storePayment(Request $request, StudentInvoice $invoice, AuditService $audit)
    {
        Gate::authorize('manage-finance');
        abort_unless($invoice->school_id === $request->user()->school_id, 404);
        $data = $request->validate(['amount' => ['required', 'numeric', 'gt:0'], 'method' => ['required', 'in:cash,bank_transfer,mobile_money,card,other'], 'reference' => ['nullable', 'string', 'max:255'], 'paid_at' => ['nullable', 'date'], 'notes' => ['nullable', 'string']]);
        $payment = $this->finance->recordPayment($invoice, $data, $request->user()->id);
        $audit->record($request, 'finance.payment_recorded', 'Invoice payment recorded.', $payment);
        return back()->with('success', 'Payment recorded successfully.');
    }

    private function authorizeFeeSetup(Request $request): void
    {
        abort_unless($request->user()->roles()->whereIn('slug', ['super-administrator', 'school-administrator', 'principal'])->exists(), 403, 'Only an administrator or principal can configure fee types and fee structures.');
    }

}
