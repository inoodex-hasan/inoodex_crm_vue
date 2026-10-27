<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class LeadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Gate::before(fn () => true);
    }

    public function test_can_view_leads_index_page(): void
    {
        $user = User::create([
            'name' => 'Marketing Officer',
            'email' => 'mkt@example.com',
            'password' => bcrypt('secret'),
        ]);

        $response = $this->actingAs($user)->get('/dashboard/marketing/leads');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Leads/Index'));
    }

    public function test_can_create_new_lead(): void
    {
        $user = User::create([
            'name' => 'Marketing Officer',
            'email' => 'mkt2@example.com',
            'password' => bcrypt('secret'),
        ]);

        $response = $this->actingAs($user)->post('/dashboard/marketing/leads', [
            'student_name' => 'Michael Scott',
            'phone' => '+8801912345678',
            'email' => 'michael@example.com',
            'priority' => 'high',
            'source' => 'facebook',
            'notes' => 'Looking for UK September intake.',
        ]);

        $response->assertRedirect('/dashboard/marketing/leads');

        $this->assertDatabaseHas('leads', [
            'student_name' => 'Michael Scott',
            'phone' => '+8801912345678',
            'priority' => 'high',
        ]);
    }
}
