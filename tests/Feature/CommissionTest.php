<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Commission;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CommissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Gate::before(fn () => true);

        $this->accountant = User::create([
            'name' => 'Head Accountant',
            'email' => 'accounts@example.com',
            'password' => bcrypt('secret'),
        ]);

        $this->counselor = User::create([
            'name' => 'Senior Counselor',
            'email' => 'counselor@example.com',
            'password' => bcrypt('secret'),
        ]);

        $this->student = Student::create([
            'first_name' => 'Naimur',
            'last_name' => 'Rahman',
            'email' => 'naimur@example.com',
            'phone' => '01700000000',
            'created_by' => $this->counselor->id,
            'assigned_consultant_id' => $this->counselor->id,
        ]);

        $this->application = Application::create([
            'application_id' => 'APP-2026-TEST',
            'student_id' => $this->student->id,
            'status' => 'enrolled',
            'created_by' => $this->counselor->id,
        ]);
    }

    public function test_accountant_can_view_commissions_dashboard(): void
    {
        Commission::create([
            'application_id' => $this->application->id,
            'user_id' => $this->counselor->id,
            'proposed_amount' => 5000,
            'workflow_status' => 'claimed',
            'claimed_at' => now(),
        ]);

        $response = $this->actingAs($this->accountant)->get('/dashboard/commissions');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Commissions/Index')
            ->has('commissions.data', 1)
            ->where('stats.claimed', 1)
        );
    }

    public function test_accountant_can_view_pending_claims(): void
    {
        Commission::create([
            'application_id' => $this->application->id,
            'user_id' => $this->counselor->id,
            'proposed_amount' => 5000,
            'workflow_status' => 'claimed',
            'claimed_at' => now(),
        ]);

        $response = $this->actingAs($this->accountant)->get('/dashboard/commissions/pending');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Commissions/Pending')
            ->has('commissions.data', 1)
        );
    }

    public function test_accountant_can_approve_commission(): void
    {
        $commission = Commission::create([
            'application_id' => $this->application->id,
            'user_id' => $this->counselor->id,
            'proposed_amount' => 5000,
            'workflow_status' => 'claimed',
            'claimed_at' => now(),
        ]);

        $response = $this->actingAs($this->accountant)->post("/dashboard/commissions/{$commission->id}/approve", [
            'amount' => 4500,
            'review_notes' => 'Approved with standard rate adjustment',
        ]);

        $response->assertRedirect('/dashboard/commissions/pending');
        $this->assertDatabaseHas('commissions', [
            'id' => $commission->id,
            'amount' => 4500,
            'workflow_status' => 'approved',
        ]);
    }

    public function test_employee_can_view_my_commissions(): void
    {
        $response = $this->actingAs($this->counselor)->get('/dashboard/my-commissions');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('MyCommissions/Index')
        );
    }

    public function test_employee_can_view_claimable_applications(): void
    {
        $response = $this->actingAs($this->counselor)->get('/dashboard/my-commissions/claimable');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('MyCommissions/Claimable')
            ->has('applications.data', 1)
        );
    }

    public function test_accountant_can_view_create_commission_page(): void
    {
        $response = $this->actingAs($this->accountant)->get('/dashboard/commissions/create');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Commissions/Create')
            ->has('applications')
            ->has('users')
        );
    }
}
