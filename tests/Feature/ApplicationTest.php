<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Country;
use App\Models\Student;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class ApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Gate::before(fn () => true);
    }

    public function test_can_view_applications_index_page(): void
    {
        $user = User::create([
            'name' => 'Application Officer',
            'email' => 'appofficer@example.com',
            'password' => bcrypt('secret'),
        ]);

        $response = $this->actingAs($user)->get('/dashboard/applications');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Applications/Index'));
    }

    public function test_can_create_new_application(): void
    {
        $user = User::create([
            'name' => 'Application Officer',
            'email' => 'appofficer2@example.com',
            'password' => bcrypt('secret'),
        ]);

        $student = Student::create([
            'first_name' => 'Sarah',
            'last_name' => 'Connor',
            'email' => 'sarah@example.com',
            'phone' => '+8801999999999',
        ]);

        $country = Country::create(['name' => 'Australia', 'code' => 'AU', 'status' => '1']);
        $university = University::create(['name' => 'University of Melbourne', 'country_id' => $country->id, 'status' => '1']);

        $response = $this->actingAs($user)->post('/dashboard/applications', [
            'student_id' => $student->id,
            'university_id' => $university->id,
            'status' => 'application',
            'application_priority' => 'priority',
            'visa_status' => 'pending',
            'notes' => 'Applied for February semester.',
        ]);

        $response->assertRedirect('/dashboard/applications');

        $this->assertDatabaseHas('applications', [
            'student_id' => $student->id,
            'university_id' => $university->id,
            'status' => 'application',
        ]);
    }

    public function test_can_view_country_report(): void
    {
        $user = User::create([
            'name' => 'Report Viewer',
            'email' => 'report@example.com',
            'password' => bcrypt('secret'),
        ]);

        $response = $this->actingAs($user)->get('/dashboard/applications/country-report');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Applications/CountryReport'));
    }
}
