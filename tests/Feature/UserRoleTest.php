<?php

namespace Tests\Feature;

use App\Models\User;
use HasinHayder\Tyro\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_can_view_roles_index(): void
    {
        $admin = User::first();
        $response = $this->actingAs($admin)->get(route('tyro-dashboard.roles.index'));

        $response->assertStatus(200);
    }

    public function test_can_view_users_index(): void
    {
        $admin = User::first();
        $response = $this->actingAs($admin)->get(route('tyro-dashboard.users.index'));

        $response->assertStatus(200);
    }

    public function test_can_create_and_toggle_role(): void
    {
        $admin = User::first();

        $createResponse = $this->actingAs($admin)->post(route('tyro-dashboard.roles.store'), [
            'name' => 'Agent Supervisor',
            'slug' => 'agent-supervisor',
            'is_active' => true,
        ]);

        $createResponse->assertRedirect(route('tyro-dashboard.roles.index'));
        $this->assertDatabaseHas('roles', ['name' => 'Agent Supervisor']);

        $role = Role::where('slug', 'agent-supervisor')->first();
        $toggleResponse = $this->actingAs($admin)->post(route('tyro-dashboard.roles.toggle', $role->id));
        $toggleResponse->assertRedirect();

        $role->refresh();
        $this->assertFalse((bool) $role->is_active);
    }

    public function test_can_create_and_manage_user(): void
    {
        $admin = User::first();

        $storeResponse = $this->actingAs($admin)->post(route('tyro-dashboard.users.store'), [
            'name' => 'Test User Operator',
            'email' => 'operator@inoodex.test',
            'username' => 'operator_test',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'designation' => 'junior',
        ]);

        $storeResponse->assertRedirect(route('tyro-dashboard.users.index'));
        $this->assertDatabaseHas('users', ['email' => 'operator@inoodex.test']);

        $user = User::where('email', 'operator@inoodex.test')->first();

        // Test suspend
        $suspendResponse = $this->actingAs($admin)->post(route('tyro-dashboard.users.suspend', $user->id), [
            'reason' => 'Annual leave',
        ]);
        $suspendResponse->assertRedirect(route('tyro-dashboard.users.index'));
        $user->refresh();
        $this->assertNotNull($user->suspended_at);

        // Test unsuspend
        $unsuspendResponse = $this->actingAs($admin)->post(route('tyro-dashboard.users.unsuspend', $user->id));
        $unsuspendResponse->assertRedirect(route('tyro-dashboard.users.index'));
        $user->refresh();
        $this->assertNull($user->suspended_at);
    }
}
