<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Salary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Inertia\Testing\AssertableInertia as Assert;

class SalaryTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        Gate::before(fn () => true);

        $this->user = User::create([
            'name' => 'Admin Manager',
            'email' => 'admin_mgr@inoodex.com',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_can_view_salaries_index()
    {
        $currentMonth = now()->format('Y-m');
        Salary::create([
            'user_id' => $this->user->id,
            'employee_name' => 'Admin Manager',
            'month' => $currentMonth,
            'basic_salary' => 50000,
            'payment_status' => 'pending',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.salaries.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Salaries/Index')
            ->has('salaries.data', 1)
            ->has('stats')
        );
    }

    public function test_can_view_salary_create_and_store()
    {
        $response = $this->actingAs($this->user)->get(route('admin.salaries.create'));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page->component('Salaries/Create'));

        $currentMonth = now()->format('Y-m');
        $storeResponse = $this->actingAs($this->user)->post(route('admin.salaries.store'), [
            'user_id' => $this->user->id,
            'employee_name' => 'Admin Manager',
            'month' => $currentMonth,
            'basic_salary' => 60000,
            'allowances' => 5000,
            'other_deductions' => 2000,
            'payment_status' => 'pending',
            'notes' => 'Test monthly payroll',
        ]);

        $storeResponse->assertRedirect(route('admin.salaries.index'));
        $this->assertDatabaseHas('salaries', [
            'user_id' => $this->user->id,
            'month' => $currentMonth,
            'basic_salary' => 60000,
        ]);
    }

    public function test_can_view_salary_show_and_edit()
    {
        $currentMonth = now()->format('Y-m');
        $salary = Salary::create([
            'user_id' => $this->user->id,
            'employee_name' => 'Admin Manager',
            'month' => $currentMonth,
            'basic_salary' => 45000,
            'payment_status' => 'pending',
            'created_by' => $this->user->id,
        ]);

        $showResponse = $this->actingAs($this->user)->get(route('admin.salaries.show', $salary));
        $showResponse->assertStatus(200);
        $showResponse->assertInertia(fn (Assert $page) => $page->component('Salaries/Show'));

        $editResponse = $this->actingAs($this->user)->get(route('admin.salaries.edit', $salary));
        $editResponse->assertStatus(200);
        $editResponse->assertInertia(fn (Assert $page) => $page->component('Salaries/Edit'));
    }

    public function test_can_view_bulk_pay_form()
    {
        $currentMonth = now()->format('Y-m');
        $salary = Salary::create([
            'user_id' => $this->user->id,
            'employee_name' => 'Admin Manager',
            'month' => $currentMonth,
            'basic_salary' => 45000,
            'payment_status' => 'pending',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.salaries.bulk-pay-form', ['salary_ids' => [$salary->id]]));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page->component('Salaries/BulkPay'));
    }

    public function test_can_view_generate_salaries_page()
    {
        $response = $this->actingAs($this->user)->get(route('admin.salaries.generate'));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page->component('Salaries/Generate'));
    }
}
