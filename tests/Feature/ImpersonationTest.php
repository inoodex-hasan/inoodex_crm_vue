<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImpersonationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_impersonate_user_and_leave()
    {
        $admin = User::create([
            'name' => 'Admin Boss',
            'email' => 'boss@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $employee = User::create([
            'name' => 'Regular Employee',
            'email' => 'worker@example.com',
            'password' => bcrypt('secret123'),
        ]);

        // Mock role check so admin passes
        \Illuminate\Support\Facades\Gate::before(function ($user) {
            return true;
        });

        // 1. Admin logs in as Employee
        $response = $this->actingAs($admin)->post(route('admin.impersonate', $employee->id));
        $response->assertRedirect(route('tyro-dashboard.index'));

        // Check active auth user is now the employee, but impersonator_id is in session
        $this->assertEquals($employee->id, auth()->id());
        $this->assertEquals($admin->id, session('impersonator_id'));

        // 2. Admin leaves impersonation
        $leaveResponse = $this->get(route('admin.impersonate.leave'));
        $leaveResponse->assertRedirect(route('tyro-dashboard.users.index'));

        // Check active auth user is restored to admin
        $this->assertEquals($admin->id, auth()->id());
        $this->assertFalse(session()->has('impersonator_id'));
    }
}
