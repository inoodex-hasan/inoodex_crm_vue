<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Student;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class StudentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Gate::before(fn () => true);
    }

    public function test_can_view_students_index_page(): void
    {
        $user = User::create([
            'name' => 'Consultant Officer',
            'email' => 'consultant@example.com',
            'password' => bcrypt('secret'),
        ]);

        $response = $this->actingAs($user)->get('/dashboard/students');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Students/Index'));
    }

    public function test_can_create_new_student_profile(): void
    {
        $user = User::create([
            'name' => 'Consultant Officer',
            'email' => 'consultant2@example.com',
            'password' => bcrypt('secret'),
        ]);

        $country = Country::create(['name' => 'Canada', 'code' => 'CA', 'status' => '1']);
        $university = University::create(['name' => 'University of Toronto', 'country_id' => $country->id, 'status' => '1']);

        $response = $this->actingAs($user)->post('/dashboard/students', [
            'first_name' => 'David',
            'last_name' => 'Miller',
            'phone' => '+8801812345678',
            'email' => 'david@example.com',
            'country_id' => $country->id,
            'university_id' => $university->id,
            'gender' => 'male',
            'package_amount' => 60000,
        ]);

        $response->assertRedirect('/dashboard/students');

        $this->assertDatabaseHas('students', [
            'first_name' => 'David',
            'last_name' => 'Miller',
            'email' => 'david@example.com',
            'phone' => '+8801812345678',
            'package_amount' => 60000,
        ]);
    }
}
