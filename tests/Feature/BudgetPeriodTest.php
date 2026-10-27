<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Budget;
use App\Models\ChartOfAccount;
use App\Models\AccountingPeriod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Inertia\Testing\AssertableInertia as Assert;

class BudgetPeriodTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        Gate::before(fn () => true);

        $this->user = User::create([
            'name' => 'Finance Director',
            'email' => 'finance_dir@inoodex.com',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_can_view_budgets_index_create_and_store()
    {
        $account = ChartOfAccount::create([
            'code' => '5100',
            'name' => 'Marketing & Ad Spend',
            'type' => 'expense',
            'is_active' => true,
        ]);

        $budget = Budget::create([
            'chart_of_account_id' => $account->id,
            'amount' => 150000,
            'period' => 'monthly',
            'start_date' => '2026-10-01',
            'end_date' => '2026-10-31',
            'created_by' => $this->user->id,
        ]);

        $indexResponse = $this->actingAs($this->user)->get(route('admin.budgets.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertInertia(fn (Assert $page) => $page
            ->component('Budgets/Index')
            ->has('budgets.data', 1)
        );

        $createResponse = $this->actingAs($this->user)->get(route('admin.budgets.create'));
        $createResponse->assertStatus(200);
        $createResponse->assertInertia(fn (Assert $page) => $page->component('Budgets/Create'));

        $storeResponse = $this->actingAs($this->user)->post(route('admin.budgets.store'), [
            'chart_of_account_id' => $account->id,
            'amount' => 200000,
            'period' => 'monthly',
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
        ]);
        $storeResponse->assertRedirect(route('admin.budgets.index'));
        $this->assertDatabaseHas('budgets', [
            'chart_of_account_id' => $account->id,
            'amount' => 200000,
        ]);
    }

    public function test_can_view_accounting_periods_index_create_and_store()
    {
        $period = AccountingPeriod::create([
            'name' => 'FY 2026-2027',
            'type' => 'fiscal_year',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'status' => 'open',
        ]);

        $indexResponse = $this->actingAs($this->user)->get(route('admin.accounting-periods.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertInertia(fn (Assert $page) => $page
            ->component('AccountingPeriods/Index')
            ->has('periods', 1)
        );

        $createResponse = $this->actingAs($this->user)->get(route('admin.accounting-periods.create'));
        $createResponse->assertStatus(200);
        $createResponse->assertInertia(fn (Assert $page) => $page->component('AccountingPeriods/Create'));

        $editResponse = $this->actingAs($this->user)->get(route('admin.accounting-periods.edit', $period));
        $editResponse->assertStatus(200);
        $editResponse->assertInertia(fn (Assert $page) => $page->component('AccountingPeriods/Edit'));

        $storeResponse = $this->actingAs($this->user)->post(route('admin.accounting-periods.store'), [
            'name' => 'Q1 2026',
            'type' => 'quarterly',
            'start_date' => '2026-01-01',
            'end_date' => '2026-03-31',
        ]);
        $storeResponse->assertRedirect();
        $this->assertDatabaseHas('accounting_periods', [
            'name' => 'Q1 2026',
        ]);
    }
}
