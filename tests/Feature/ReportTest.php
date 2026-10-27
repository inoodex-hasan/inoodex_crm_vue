<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\ChartOfAccount;
use App\Models\OfficeAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Inertia\Testing\AssertableInertia as Assert;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        Gate::before(fn () => true);

        $this->user = User::create([
            'name' => 'Report Auditor',
            'email' => 'auditor@inoodex.com',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_can_view_financial_summary_report()
    {
        $officeAccount = OfficeAccount::create([
            'account_name' => 'City Bank Principal',
            'account_number' => 'CB-990-112',
            'account_type' => 'bank',
            'opening_balance' => 100000,
            'status' => 'active',
        ]);

        $account = ChartOfAccount::create([
            'code' => '5200',
            'name' => 'Office Utility',
            'type' => 'expense',
            'is_active' => true,
        ]);

        Expense::create([
            'chart_of_account_id' => $account->id,
            'office_account_id' => $officeAccount->id,
            'amount' => 5000,
            'description' => 'Electricity bill payment',
            'expense_date' => now()->format('Y-m-d'),
            'created_by' => $this->user->id,
            'payment_method' => 'bank_transfer',
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.reports.summary'));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Reports/Summary')
            ->has('summary')
            ->has('expenses', 1)
        );
    }

    public function test_can_view_balance_sheet_report()
    {
        $response = $this->actingAs($this->user)->get(route('admin.reports.balance-sheet'));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Reports/BalanceSheet')
            ->has('data')
            ->has('totalAssets')
            ->has('totalLiabilities')
            ->has('totalEquity')
        );
    }
}
