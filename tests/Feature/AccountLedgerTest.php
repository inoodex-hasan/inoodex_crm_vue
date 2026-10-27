<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ChartOfAccount;
use App\Models\AccountingPeriod;
use App\Models\JournalEntry;
use App\Models\JournalEntryItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Inertia\Testing\AssertableInertia as Assert;

class AccountLedgerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $period;

    protected function setUp(): void
    {
        parent::setUp();
        Gate::before(fn () => true);

        $this->user = User::create([
            'name' => 'Accountant Super',
            'email' => 'accountant@inoodex.com',
            'password' => bcrypt('password'),
        ]);

        $this->period = AccountingPeriod::create([
            'name' => 'FY 2026-2027',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'status' => 'open',
            'is_closed' => false,
        ]);
    }

    public function test_can_view_chart_of_accounts_index_and_create()
    {
        $account = ChartOfAccount::create([
            'code' => '1000',
            'name' => 'Current Assets',
            'type' => 'asset',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.chart-of-accounts.index'));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('ChartOfAccounts/Index')
            ->has('accounts')
            ->has('parentAccounts')
        );

        $postResponse = $this->actingAs($this->user)->post(route('admin.chart-of-accounts.store'), [
            'type' => 'asset',
            'parent_id' => $account->id,
            'code' => '1010',
            'name' => 'Bank Accounts',
        ]);

        $postResponse->assertRedirect();
        $this->assertDatabaseHas('chart_of_accounts', [
            'code' => '1010',
            'name' => 'Bank Accounts',
            'parent_id' => $account->id,
        ]);
    }

    public function test_can_view_chart_of_accounts_edit()
    {
        $account = ChartOfAccount::create([
            'code' => '5000',
            'name' => 'Operating Expense',
            'type' => 'expense',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.chart-of-accounts.edit', $account));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('ChartOfAccounts/Edit')
            ->where('account.code', '5000')
        );
    }

    public function test_can_view_journal_entries_index_and_create()
    {
        $acc1 = ChartOfAccount::create(['code' => '1001', 'name' => 'Cash in Hand', 'type' => 'asset']);
        $acc2 = ChartOfAccount::create(['code' => '4001', 'name' => 'Consulting Revenue', 'type' => 'revenue']);

        $entry = JournalEntry::create([
            'period_id' => $this->period->id,
            'date' => '2026-10-27',
            'reference_number' => 'JV-20261027-001',
            'note' => 'Student registration fee',
            'status' => 'posted',
            'created_by' => $this->user->id,
        ]);

        JournalEntryItem::create([
            'journal_entry_id' => $entry->id,
            'chart_of_account_id' => $acc1->id,
            'debit' => 25000,
            'credit' => 0,
            'description' => 'Received cash',
        ]);

        JournalEntryItem::create([
            'journal_entry_id' => $entry->id,
            'chart_of_account_id' => $acc2->id,
            'debit' => 0,
            'credit' => 25000,
            'description' => 'Revenue credited',
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.journal-entries.index'));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('JournalEntries/Index')
            ->has('entries.data', 1)
        );

        $createResponse = $this->actingAs($this->user)->get(route('admin.journal-entries.create'));
        $createResponse->assertStatus(200);
        $createResponse->assertInertia(fn (Assert $page) => $page->component('JournalEntries/Create'));

        $showResponse = $this->actingAs($this->user)->get(route('admin.journal-entries.show', $entry));
        $showResponse->assertStatus(200);
        $showResponse->assertInertia(fn (Assert $page) => $page->component('JournalEntries/Show'));
    }

    public function test_can_store_balanced_journal_entry()
    {
        $accDebit = ChartOfAccount::create(['code' => '1002', 'name' => 'Bank Main', 'type' => 'asset']);
        $accCredit = ChartOfAccount::create(['code' => '3001', 'name' => 'Capital Contribution', 'type' => 'equity']);

        $response = $this->actingAs($this->user)->post(route('admin.journal-entries.store'), [
            'date' => '2026-10-27',
            'period_id' => $this->period->id,
            'reference_number' => 'JV-TEST-100',
            'note' => 'Capital injection',
            'items' => [
                [
                    'chart_of_account_id' => $accDebit->id,
                    'description' => 'Capital received into bank',
                    'debit' => 100000,
                    'credit' => null,
                ],
                [
                    'chart_of_account_id' => $accCredit->id,
                    'description' => 'Equity added',
                    'debit' => null,
                    'credit' => 100000,
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.journal-entries.index'));
        $this->assertDatabaseHas('journal_entries', [
            'reference_number' => 'JV-TEST-100',
        ]);
        $this->assertDatabaseHas('journal_entry_items', [
            'chart_of_account_id' => $accDebit->id,
            'debit' => 100000,
        ]);
    }
}
