<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\OfficeAccount;
use App\Models\BankReconciliation;
use App\Models\ChartOfAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Inertia\Testing\AssertableInertia as Assert;

class BankReconciliationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        Gate::before(fn () => true);

        $this->user = User::create([
            'name' => 'Bank Auditor',
            'email' => 'bank_audit@inoodex.com',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_can_view_bank_reconciliations_index_and_create()
    {
        $account = OfficeAccount::create([
            'account_name' => 'Islami Bank Main',
            'account_number' => 'IBBL-00129-99',
            'account_type' => 'bank',
            'opening_balance' => 500000,
            'status' => 'active',
        ]);

        $reconciliation = BankReconciliation::create([
            'account_id' => $account->id,
            'statement_date' => '2026-10-27',
            'statement_balance' => 500000,
            'system_balance' => 500000,
            'difference' => 0,
            'status' => 'draft',
        ]);

        $indexResponse = $this->actingAs($this->user)->get(route('admin.bank-reconciliations.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertInertia(fn (Assert $page) => $page
            ->component('BankReconciliations/Index')
            ->has('reconciliations.data', 1)
        );

        $createResponse = $this->actingAs($this->user)->get(route('admin.bank-reconciliations.create'));
        $createResponse->assertStatus(200);
        $createResponse->assertInertia(fn (Assert $page) => $page->component('BankReconciliations/Create'));

        $showResponse = $this->actingAs($this->user)->get(route('admin.bank-reconciliations.show', $reconciliation));
        $showResponse->assertStatus(200);
        $showResponse->assertInertia(fn (Assert $page) => $page->component('BankReconciliations/Show'));
    }
}
