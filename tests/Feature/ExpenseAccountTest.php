<?php

namespace Tests\Feature;

use App\Models\ChartOfAccount;
use App\Models\Expense;
use App\Models\OfficeAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ExpenseAccountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Gate::before(fn () => true);

        $this->user = User::create([
            'name' => 'Finance Controller',
            'email' => 'fincontroller@example.com',
            'password' => bcrypt('secret'),
        ]);

        $this->account = OfficeAccount::create([
            'account_name' => 'Petty Cash Dhaka',
            'account_type' => 'cash',
            'account_number' => 'CASH-DHAKA-01',
            'opening_balance' => 25000,
            'status' => 'active',
        ]);

        $this->category = ChartOfAccount::create([
            'code' => '5001',
            'name' => 'Office Utilities & Stationery',
            'type' => 'expense',
            'is_active' => true,
        ]);
    }

    public function test_can_view_expenses_index(): void
    {
        Expense::create([
            'chart_of_account_id' => $this->category->id,
            'office_account_id' => $this->account->id,
            'title' => 'Office Refreshments',
            'description' => 'Office Refreshments',
            'amount' => 1200,
            'expense_date' => now(),
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get('/dashboard/expenses');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Expenses/Index')
            ->has('expenses.data', 1)
        );
    }

    public function test_can_view_office_accounts_index(): void
    {
        $response = $this->actingAs($this->user)->get('/dashboard/office-accounts');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('OfficeAccounts/Index')
            ->has('accounts.data', 1)
        );
    }

    public function test_can_create_office_account(): void
    {
        $response = $this->actingAs($this->user)->post('/dashboard/office-accounts', [
            'account_name' => 'Eastern Bank Ltd',
            'account_type' => 'bank',
            'provider_name' => 'EBL',
            'account_number' => '9988776655',
            'branch_name' => 'Banani',
            'opening_balance' => 50000,
            'status' => 'active',
        ]);

        $response->assertRedirect('/dashboard/office-accounts');
        $this->assertDatabaseHas('office_accounts', [
            'account_name' => 'Eastern Bank Ltd',
            'account_number' => '9988776655',
        ]);
    }
}
