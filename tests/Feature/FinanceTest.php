<?php

use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function financeAdmin(): array
{
    $school = School::create(['name' => 'Finance Academy']);
    $role = Role::where('slug', 'super-administrator')->firstOrFail();
    $user = User::factory()->create(['school_id' => $school->id]);
    $user->roles()->attach($role->id);

    return [$user, $school];
}

it('creates an invoice and keeps payment totals and balance accurate', function () {
    [$user, $school] = financeAdmin();
    $student = $school->students()->create(['first_name' => 'Ama', 'last_name' => 'Boateng', 'admission_no' => 'AD001', 'status' => 'active']);
    $feeType = $school->feeTypes()->create(['name' => 'Tuition', 'code' => 'TUITION', 'status' => 'active']);
    $structure = $school->feeStructures()->create(['fee_type_id' => $feeType->id, 'amount' => 1000, 'status' => 'active']);

    $this->actingAs($user)->post('/finance/invoices', ['student_id' => $student->id, 'fee_structure_ids' => [$structure->id]])->assertRedirect();
    $invoice = $student->invoices()->firstOrFail();
    expect((float) $invoice->total)->toBe(1000.0)->and((float) $invoice->balance)->toBe(1000.0);

    $this->actingAs($user)->post('/finance/invoices/'.$invoice->id.'/payments', ['amount' => 400, 'method' => 'mobile_money', 'reference' => 'MOMO-001'])->assertRedirect();
    $invoice->refresh();
    expect((float) $invoice->paid)->toBe(400.0)->and((float) $invoice->balance)->toBe(600.0)->and($invoice->status)->toBe('partial');
    $this->assertDatabaseHas('payments', ['student_invoice_id' => $invoice->id]);
});

it('rejects payment above the invoice balance and foreign fee structures', function () {
    [$user, $school] = financeAdmin();
    $otherSchool = School::create(['name' => 'Other Finance School']);
    $student = $school->students()->create(['first_name' => 'Kojo', 'last_name' => 'Mensah', 'admission_no' => 'AD001', 'status' => 'active']);
    $otherType = $otherSchool->feeTypes()->create(['name' => 'Tuition', 'code' => 'TUITION', 'status' => 'active']);
    $foreignStructure = $otherSchool->feeStructures()->create(['fee_type_id' => $otherType->id, 'amount' => 500, 'status' => 'active']);

    $this->actingAs($user)->post('/finance/invoices', ['student_id' => $student->id, 'fee_structure_ids' => [$foreignStructure->id]])->assertSessionHasErrors('fee_structure_ids');
});