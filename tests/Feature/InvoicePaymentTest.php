<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\ChartOfAccount;
use App\Models\Invoice;
use App\Models\OfficeAccount;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class InvoicePaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Gate::before(fn () => true);

        $this->user = User::create([
            'name' => 'Accountant Staff',
            'email' => 'finance@example.com',
            'password' => bcrypt('secret'),
        ]);

        $this->student = Student::create([
            'first_name' => 'Tanvir',
            'last_name' => 'Ahmed',
            'email' => 'tanvir@example.com',
            'phone' => '01800000000',
            'created_by' => $this->user->id,
        ]);

        $this->application = Application::create([
            'application_id' => 'APP-FIN-001',
            'student_id' => $this->student->id,
            'status' => 'submitted',
            'created_by' => $this->user->id,
        ]);

        $this->account = OfficeAccount::create([
            'account_name' => 'City Bank Corporate',
            'account_type' => 'bank',
            'provider_name' => 'City Bank',
            'account_number' => '1234567890',
            'current_balance' => 100000,
            'status' => 'active',
        ]);

        $this->chartAccount = ChartOfAccount::create([
            'code' => '4001',
            'name' => 'Tuition Fee Revenue',
            'type' => 'revenue',
            'is_active' => true,
        ]);
    }

    public function test_can_view_invoices_index(): void
    {
        Invoice::create([
            'invoice_number' => 'INV-2026-0001',
            'student_id' => $this->student->id,
            'application_id' => $this->application->id,
            'date' => now(),
            'due_date' => now()->addDays(14),
            'total_amount' => 50000,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->get('/dashboard/invoices');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Invoices/Index')
            ->has('invoices.data', 1)
        );
    }

    public function test_can_view_payments_index(): void
    {
        Payment::create([
            'receipt_number' => 'REC-2026-0001',
            'student_id' => $this->student->id,
            'application_id' => $this->application->id,
            'amount' => 20000,
            'payment_type' => 'advance',
            'payment_status' => 'completed',
            'payment_date' => now(),
            'office_account_id' => $this->account->id,
        ]);

        $response = $this->actingAs($this->user)->get('/dashboard/payments');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Payments/Index')
            ->has('payments.data', 1)
        );
    }

    public function test_can_view_invoice_edit_page(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => 'INV-2026-EDIT',
            'student_id' => $this->student->id,
            'application_id' => $this->application->id,
            'total_amount' => 50000,
            'status' => 'draft',
            'date' => now(),
            'due_date' => now()->addDays(14),
        ]);

        $response = $this->actingAs($this->user)->get("/dashboard/invoices/{$invoice->id}/edit");

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Invoices/Edit')
            ->has('invoice')
            ->has('applications')
            ->has('accounts')
        );
    }
}
