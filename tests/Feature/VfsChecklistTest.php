<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Country;
use App\Models\Student;
use App\Models\University;
use App\Models\User;
use App\Models\VfsChecklist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class VfsChecklistTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Gate::before(fn () => true);
    }

    public function test_can_view_vfs_checklist_index_page(): void
    {
        $user = User::create([
            'name' => 'Counselor',
            'email' => 'counselor@example.com',
            'password' => bcrypt('secret'),
        ]);

        $response = $this->actingAs($user)->get('/dashboard/vfs-checklist');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('VfsChecklist/Index'));
    }

    public function test_can_view_vfs_checklist_show_page_and_toggle_item(): void
    {
        $user = User::create([
            'name' => 'Officer',
            'email' => 'officer@example.com',
            'password' => bcrypt('secret'),
        ]);

        $country = Country::create(['name' => 'United Kingdom', 'code' => 'GB', 'status' => '1']);
        $university = University::create(['name' => 'Oxford University', 'country_id' => $country->id, 'status' => '1']);

        $student = Student::create([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
            'phone' => '01712345678',
        ]);

        $application = Application::create([
            'application_id' => 'APP-2026-999',
            'student_id' => $student->id,
            'university_id' => $university->id,
            'status' => 'application',
        ]);

        $response = $this->actingAs($user)->get("/dashboard/vfs-checklist/{$application->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('VfsChecklist/Show'));

        $item = VfsChecklist::where('application_id', $application->id)->first();
        $this->assertNotNull($item);
        $this->assertFalse((bool) $item->is_checked);

        // Toggle item via AJAX
        $toggleResponse = $this->actingAs($user)->post("/dashboard/vfs-checklist/items/{$item->id}/toggle");
        $toggleResponse->assertStatus(200);
        $toggleResponse->assertJson(['success' => true, 'is_checked' => true]);

        $this->assertTrue((bool) $item->fresh()->is_checked);
    }

    public function test_can_view_vfs_overview_and_edit_status(): void
    {
        $user = User::create([
            'name' => 'Officer',
            'email' => 'officer2@example.com',
            'password' => bcrypt('secret'),
        ]);

        $country = Country::create(['name' => 'Canada', 'code' => 'CA', 'status' => '1']);
        $university = University::create(['name' => 'Toronto Uni', 'country_id' => $country->id, 'status' => '1']);
        $student = Student::create(['first_name' => 'Bob', 'last_name' => 'Smith', 'email' => 'bob@example.com', 'phone' => '01812345678']);

        $application = Application::create([
            'application_id' => 'APP-2026-888',
            'student_id' => $student->id,
            'university_id' => $university->id,
            'status' => 'application',
            'vfs_appointment_date' => now()->addDays(5),
            'vfs_result' => 'submitted',
        ]);

        // Overview
        $overviewResponse = $this->actingAs($user)->get('/dashboard/vfs-checklist/overview');
        $overviewResponse->assertStatus(200);
        $overviewResponse->assertInertia(fn ($page) => $page->component('VfsChecklist/Overview'));

        // VFS Show
        $showResponse = $this->actingAs($user)->get("/dashboard/vfs-checklist/overview/{$application->id}");
        $showResponse->assertStatus(200);
        $showResponse->assertInertia(fn ($page) => $page->component('VfsChecklist/VfsShow'));

        // VFS Edit
        $editResponse = $this->actingAs($user)->get("/dashboard/vfs-checklist/overview/{$application->id}/edit");
        $editResponse->assertStatus(200);
        $editResponse->assertInertia(fn ($page) => $page->component('VfsChecklist/VfsEdit'));

        // Templates List
        $templatesResponse = $this->actingAs($user)->get('/dashboard/vfs-checklist/templates');
        $templatesResponse->assertStatus(200);
        $templatesResponse->assertInertia(fn ($page) => $page->component('VfsChecklist/Templates'));
    }
}
